<?php
//USER ACTION DAL
    require 'db_connection.php';

    class TICKET_DAL
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

        public function event_list()
        {
            $query = $this->db->prepare("SELECT event_id, event_name, use_hall_capacity as custom, event_id drop_value, event_name AS drop_text 
            FROM base_event WHERE event_active = 1");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function unit_list()
        {
            $query = $this->db->prepare("SELECT unit_id, unit_name, quantity as custom, unit_id drop_value, unit_name AS drop_text 
            FROM base_unit WHERE unit_active = 1");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }
        
        public function ticket_unit_list($ticket_d)
        {
            $query = $this->db->prepare("SELECT unit_id, price, discount, (SELECT quantity FROM base_unit BU WHERE BU.unit_id = BTD.unit_id) quantity,
            (SELECT unit_name FROM base_unit BU WHERE BU.unit_id = BTD.unit_id) unit_name
            FROM base_ticket_detail BTD WHERE ticket_id = :tid");
            $query->bindParam("tid", $ticket_d, PDO::PARAM_STR);
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function select()
        {
            $query = $this->db->prepare("SELECT event_id, ticket_id, ticket_code, ticket_name, ticket_active,
            (SELECT user_name FROM base_user bu WHERE bu.user_id = bt.created_by_id) posted_by, date_created 
            FROM base_ticket bt ORDER BY date_created DESC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function save($user_id, $page_id, $eid, $tcode, $tname, $tactive, $unit_list)
        {
            $query = $this->db->prepare("SELECT ticket_name FROM base_ticket WHERE ticket_name = :tname OR ticket_code = :tcode");
            $query->bindParam("tname", $tname, PDO::PARAM_STR);
            $query->bindParam("tcode", $tcode, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if(empty($row)){

                $query = $this->db->prepare("INSERT INTO base_ticket(event_id, ticket_code, ticket_name, ticket_active, created_by_id, page_id) 
                VALUES (:eid,:tcode,:tname,:tactive,:user_id,:pid)");  

                $query->bindParam("eid", $eid, PDO::PARAM_STR);
                $query->bindParam("tcode", $tcode, PDO::PARAM_STR);
                $query->bindParam("tname", $tname, PDO::PARAM_STR);
                $query->bindParam("tactive", $tactive, PDO::PARAM_STR);

                $query->bindParam("user_id", $user_id, PDO::PARAM_STR);  
                $query->bindParam("pid", $page_id, PDO::PARAM_STR);                     
                $query->execute();
                $tid = $this->db->lastInsertId();
                //loop through unit list to insert in unit to base_unit table
                if(!empty($tid)){
                    $sql = "INSERT INTO base_ticket_detail (ticket_id, unit_id, price, discount) VALUES (?,?,?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($unit_list as $unit)
                    {
                        $val = array($tid, $unit->unit_id, $unit->price, $unit->discount);
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer"); 

                    //insert to log
                    $log = $this->base_log($user_id,"Save a record","Ticket");

                    return $idd;
                }                  
            }else{
                $result = 'Ticket already exist,warning';
                return $result;
            }
        }
        
        public function update($user_id, $page_id, $eid, $tcode, $tname, $tactive, $unit_list, $tid, $dc) //role id included
        {
            $query = $this->db->prepare("SELECT ticket_name FROM base_ticket WHERE (ticket_name = :tname OR ticket_code = :tcode) AND ticket_id != :tid");
            $query->bindParam("tname", $tname, PDO::PARAM_STR);
            $query->bindParam("tcode", $tcode, PDO::PARAM_STR);
            $query->bindParam("tid", $tid, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if(!empty($row)){
                return "Ticket already exist,info";
            }else{
                return $this->commitUpdate($user_id, $page_id, $eid, $tcode, $tname, $tactive, $unit_list, $tid, $dc);
            }
        }  

        function commitUpdate($user_id, $page_id, $eid, $tcode, $tname, $tactive, $unit_list, $tid, $dc){

            $query = $this->db->prepare("UPDATE base_ticket SET event_id = :eid, ticket_code = :tcode, ticket_name = :tname, ticket_active = :tactive,                    
                    modified_by_id = :id, date_modified = :dc
                    WHERE ticket_id = :tid"); 

            $query->bindParam("tid", $tid, PDO::PARAM_STR);
            $query->bindParam("id", $user_id, PDO::PARAM_STR);
            $query->bindParam("dc", $dc, PDO::PARAM_STR);

            $query->bindParam("eid", $eid, PDO::PARAM_STR); 
            $query->bindParam("tcode", $tcode, PDO::PARAM_STR);
            $query->bindParam("tname", $tname, PDO::PARAM_STR);
            $query->bindParam("tactive", $tactive, PDO::PARAM_STR);
            $query->execute();

            if($query->rowCount() <= 1){
                //delete all page related to this role in page access table and re insert new ones                
                $query = $this->db->prepare("DELETE FROM base_ticket_detail WHERE ticket_id = :tid"); 
                $query->bindParam("tid", $tid, PDO::PARAM_STR); 
                $query->execute();
                if($query->rowCount() >= 1){
                    //insert into base page access for the role
                    $sql = "INSERT INTO base_ticket_detail (ticket_id, unit_id, price, discount) VALUES (?,?,?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($unit_list as $unit)
                    {
                        $val = array($tid, $unit->unit_id, $unit->price, $unit->discount);
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer"); 

                    //insert to log
                    $log = $this->base_log($user_id,"Perform an Update","Role");
                    return $idd;
                }else{
                    //no child exist before
                    $sql = "INSERT INTO base_ticket_detail (ticket_id, unit_id, price, discount) VALUES (?,?,?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($unit_list as $unit)
                    {
                        $val = array($tid, $unit->unit_id, $unit->price, $unit->discount);
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer"); 

                    //insert to log
                    $log = $this->base_log($user_id,"Perform an Update","Ticket");
                    return $idd;
                }                              
            }else{   
                return "Unable to update ticket, try again later";
            }
        }

        public function delete($uid,$tid)
        {
            $query = $this->db->prepare("DELETE FROM base_ticket_detail WHERE ticket_id = :tid");
            $query->bindParam("tid", $tid, PDO::PARAM_STR);
            $query->execute();
            if($query){
                $query = $this->db->prepare("DELETE FROM base_ticket WHERE ticket_id = :tid");
                $query->bindParam("tid", $tid, PDO::PARAM_STR);
                $query->execute();
                if($query->rowCount() == 1){

                    $log = $this->base_log($uid,"Delete a record","Ticket");

                    return "Record successfully deleted,success";
                }
            }else{
                return "Could not delete this time please try again,error";
            }            
            
        }
        
        

     }
 
?>