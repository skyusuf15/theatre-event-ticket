<?php
//USER ACTION DAL
    require 'db_connection.php';

    class EVENT_DAL
    {
     
       protected $db;

       function __construct()
        {
            $this->db = DB();
        }
     
       function __destruct()
        {
            $this->db = null;
        }


        function base_log($id,$msg,$pg){

            $query = $this->db->prepare("SELECT user_id, user_name, (SELECT role_name FROM base_role br WHERE br.role_id = bu.role_id ) user_role
            FROM base_user bu WHERE user_id = :id");
                $query->bindParam("id", $id, PDO::PARAM_STR);
                $query->execute();
                $row = $query->fetch(PDO::FETCH_ASSOC);
        
                $uid = $row['user_id']; 
                $uname = $row['user_name']; 
                $role = $row['user_role'];
        
            $query = $this->db->prepare("INSERT INTO base_log(user_id,user_name, user_role, user_action, page) 
            VALUES (:uid,:uname,:role,:action,:pg)");
                $query->bindParam("uid", $uid, PDO::PARAM_STR);
                $query->bindParam("uname", $uname, PDO::PARAM_STR);
                $query->bindParam("role", $role, PDO::PARAM_STR);
                $query->bindParam("action", $msg, PDO::PARAM_STR);
                $query->bindParam("pg", $pg, PDO::PARAM_STR);
                $query->execute();
                return $this->db->lastInsertId();  
        }

        public function hall_list()
        {
            $query = $this->db->prepare("SELECT hall_id As drop_value, hall_name AS drop_text, hall_id, hall_name, hall_capacity
            FROM base_hall WHERE hall_active = 1 ORDER BY hall_name ASC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function event_category_list()
        {
            $query = $this->db->prepare("SELECT category_id As drop_value, category_name AS drop_text, category_id, category_name
            FROM base_category bc WHERE (SELECT type_name FROM base_category_type bct WHERE bct.type_id = bc.type_id ) = 'Event' 
            AND category_active = 1 ORDER BY category_name ASC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function event_tag_list($eid)
        {
            $query = $this->db->prepare("SELECT category_id FROM base_tag WHERE event_id = :eid");
            $query->bindParam("eid", $eid, PDO::PARAM_STR);
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function select()
        {
            $query = $this->db->prepare("SELECT event_id, hall_id, event_code, event_name, event_description, image_link, thumbnail_link, event_active, event_date, event_time, use_hall_capacity, 
            (SELECT user_name FROM base_user bu WHERE bu.user_id = BE.created_by_id) posted_by, date_created,
            (SELECT hall_name FROM base_hall bh WHERE bh.hall_id = BE.hall_id) hall_name 
            FROM base_event BE ORDER BY date_created DESC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
            
        }

        public function save($user_id, $page_id, $code, $name, $desc, $eactive, $hall_id, $use_cap, $date, $time, $tags)
        {
            $query = $this->db->prepare("SELECT event_name FROM base_event WHERE event_name = :name OR event_code = :code");
            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if(empty($row)){

                $query = $this->db->prepare("INSERT INTO base_event(hall_id, event_code, event_name, event_description, event_active, event_date, event_time, use_hall_capacity,
                created_by_id, page_id) 
                VALUES (:hall_id,:code,:name,:desc,:eactive,:date,:time,:use_cap,:user_id,:pid)");       

                $query->bindParam("hall_id", $hall_id, PDO::PARAM_STR);
                $query->bindParam("code", $code, PDO::PARAM_STR);
                $query->bindParam("name", $name, PDO::PARAM_STR);
                $query->bindParam("desc", $desc, PDO::PARAM_STR);
                $query->bindParam("eactive", $eactive, PDO::PARAM_STR);
                $query->bindParam("date", $date, PDO::PARAM_STR);
                $query->bindParam("time", $time, PDO::PARAM_STR);
                $query->bindParam("use_cap", $use_cap, PDO::PARAM_STR);

                $query->bindParam("user_id", $user_id, PDO::PARAM_STR);  
                $query->bindParam("pid", $page_id, PDO::PARAM_STR);                     
                $query->execute();
                $event_id = $this->db->lastInsertId();

                //loop through pages to insert event genre into tag table
                if(!empty($event_id)){
                    $sql = "INSERT INTO base_tag (event_id, category_id) VALUES (?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($tags as $tg)
                    {
                        $val = array($event_id, $tg);
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer"); 
                    //insert to log
                    $log = $this->base_log($user_id,"Save a record","Event");
                    return $idd;

                }                  
            }else{
                $result = 'Role already exist,warning';
                return $result;
            };
        } 

        public function update($user_id, $page_id, $code, $name, $desc, $eactive, $hall_id, $use_cap, $date, $time, $tags, $eid, $dc) //event id included
        {
            $query = $this->db->prepare("SELECT event_name FROM base_event WHERE (event_name = :name OR event_code = :code) AND event_id != :eid");
            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);
            $query->bindParam("eid", $eid, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if(!empty($row)){
                return "Role already exist : check username or phone number or email,info";
            }else{
                return $this->commitUpdate($user_id, $page_id, $code, $name, $desc, $eactive, $hall_id, $use_cap, $date, $time, $tags, $eid, $dc);
            }
        }  

        function commitUpdate($user_id, $page_id, $code, $name, $desc, $eactive, $hall_id, $use_cap, $date, $time, $tags, $eid, $dc){

            $query = $this->db->prepare("UPDATE base_event SET hall_id = :hall_id, event_code = :code, event_name = :name, event_active = :eactive, event_description = :desc,
            event_date = :date, event_time = :time, use_hall_capacity = :use_cap, 
            modified_by_id = :id, date_modified = :dc
                    WHERE event_id = :eid"); 

            $query->bindParam("id", $user_id, PDO::PARAM_STR);
            $query->bindParam("dc", $dc, PDO::PARAM_STR);
            $query->bindParam("eid", $eid, PDO::PARAM_STR);

            $query->bindParam("hall_id", $hall_id, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);
            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("desc", $desc, PDO::PARAM_STR);
            $query->bindParam("eactive", $eactive, PDO::PARAM_STR);
            $query->bindParam("date", $date, PDO::PARAM_STR);
            $query->bindParam("time", $time, PDO::PARAM_STR);
            $query->bindParam("use_cap", $use_cap, PDO::PARAM_STR);

            $query->execute();

            if(empty($tags)) return 1;

            if($query->rowCount() <= 1){
                //delete all category related to this event in base_tag and re-insert new ones                
                $query = $this->db->prepare("DELETE FROM base_tag WHERE event_id = :eid"); 
                $query->bindParam("eid", $eid, PDO::PARAM_STR); 
                $query->execute();
                if($query->rowCount() >= 1){
                    //insert into base tag for the event
                    $sql = "INSERT INTO base_tag (event_id, category_id) VALUES (?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($tags as $tg)
                    {
                        $val = array($eid, $tg);
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer"); 
                    //insert to log
                    $log = $this->base_log($user_id,"Update a record","Event");
                    return $idd;
                }else{
                    //no child to exist before
                    $sql = "INSERT INTO base_tag (event_id, category_id) VALUES (?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($tags as $tg)
                    {
                        $val = array($eid, $tg);
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer"); 
                    //insert to log
                    $log = $this->base_log($user_id,"Update a record","Event");
                    return $idd;
                }                              
            }else{   
                return "Unable to update event, try again later";
            }
        }

        public function delete($uid,$eid)
        {
            $query = $this->db->prepare("DELETE FROM base_tag WHERE event_id = :eid");
            $query->bindParam("eid", $eid, PDO::PARAM_STR);
            $query->execute();
            if($query){
                $query = $this->db->prepare("DELETE FROM base_event WHERE event_id = :eid");
                $query->bindParam("eid", $eid, PDO::PARAM_STR);
                $query->execute();
                if($query->rowCount() == 1){

                    $log = $this->base_log($uid,"Delete a record","Event");

                    return "Record successfully deleted,success";
                }
            }else{
                return "Could not delete this time please try again,error";
            }            
            
        }
        

            
     }
 
?>