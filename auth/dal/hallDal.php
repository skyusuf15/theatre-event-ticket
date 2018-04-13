<?php
//USER ACTION DAL
    require 'db_connection.php';

    class HALL_DAL
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

        public function hall_category_list()
        {
            $query = $this->db->prepare("SELECT category_id As drop_value, category_name AS drop_text, category_id, category_name
            FROM base_category bc WHERE (SELECT type_name FROM base_category_type bct WHERE bct.type_id = bc.type_id ) = 'Hall' AND category_active = 1 ORDER BY category_name ASC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function select()
        {
            $query = $this->db->prepare("SELECT hall_id, hall_code, hall_name, category_id, hall_capacity, hall_active,
            (SELECT category_name FROM base_category CT WHERE CT.category_id = C.category_id) hall_category, 
            (SELECT user_name FROM base_user bu WHERE bu.user_id = C.created_by_id) posted_by, date_created 
            FROM base_hall C ORDER BY date_created DESC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
            
        }

        public function save($user_id, $page_id, $code, $name, $type, $cap, $hactive)
        {
            $query = $this->db->prepare("SELECT hall_name FROM base_hall WHERE (hall_name = :name OR hall_code = :code) AND category_id = :type");
            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);
            $query->bindParam("type", $type, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if($query->rowCount() == 1){
                return "Hall already exist,info";
            }else{

                //insert hall
                $query = $this->db->prepare("INSERT INTO base_hall(category_id, hall_code, hall_name, hall_capacity, hall_active, created_by_id, page_id) 
                VALUES (:type,:code,:name,:cap,:hactive,:user_id,:pid)");    

                $query->bindParam("code", $code, PDO::PARAM_STR);
                $query->bindParam("name", $name, PDO::PARAM_STR);
                $query->bindParam("type", $type, PDO::PARAM_STR);
                $query->bindParam("cap", $cap, PDO::PARAM_STR);
                $query->bindParam("hactive", $hactive, PDO::PARAM_STR);

                $query->bindParam("user_id", $user_id, PDO::PARAM_STR);  
                $query->bindParam("pid", $page_id, PDO::PARAM_STR);                     
                $query->execute();                
                $id = $this->db->lastInsertId();                
                settype($id, "integer"); 

                //insert to log
                $log = $this->base_log($user_id,"Save a record","Hall");

                return $id;               

            }   

        } 

        public function update($user_id, $page_id, $code, $name, $type, $cap, $hactive, $hid, $dc) //role id included
        {

            $query = $this->db->prepare("SELECT hall_name FROM base_hall WHERE (hall_name = :name OR hall_code = :code) AND category_id = :type AND hall_id != :hid ");
            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);
            $query->bindParam("hid", $hid, PDO::PARAM_STR);
            $query->bindParam("type", $type, PDO::PARAM_STR);
            
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if(!empty($row)){
                return "Hall already exist,info";
            }else{
                return $this->commitUpdate($user_id, $page_id, $code, $name, $type, $cap, $hactive, $hid, $dc);
            }        

        } 

        function commitUpdate($user_id, $page_id, $code, $name, $type, $cap, $hactive, $hid, $dc)
        {

            $query = $this->db->prepare("UPDATE base_hall SET hall_code = :code, hall_name = :name, category_id = :type, hall_active = :hactive, hall_capacity = :cap,
                    modified_by_id = :user_id, date_modified = :dc
                    WHERE hall_id = :hid"); 

            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);            
            $query->bindParam("type", $type, PDO::PARAM_STR);
            $query->bindParam("cap", $cap, PDO::PARAM_STR);
            $query->bindParam("hactive", $hactive, PDO::PARAM_STR);

            $query->bindParam("hid", $hid, PDO::PARAM_STR);

            $query->bindParam("dc", $dc, PDO::PARAM_STR);
            $query->bindParam("user_id", $user_id, PDO::PARAM_STR);

            $query->execute();
            if($query->rowCount() <= 1){                
                $count = $query->rowCount();
                settype($count, "integer");    
                //insert to log
                $log = $this->base_log($user_id,"Perform an update","Hall");
                return $count;      
            }else{   
                return "Unable to update hall, try again later";
            }
        }

        public function delete($uid,$hid)
        {
            $query = $this->db->prepare("DELETE FROM base_hall WHERE hall_id = :hid");
            $query->bindParam("hid", $hid, PDO::PARAM_STR);
            $query->execute();
            if($query){                
                $log = $this->base_log($uid,"Delete a record","hall");
                return "Record successfully deleted,success";
            }           
            
        }

        
        

            
     }
 
?>