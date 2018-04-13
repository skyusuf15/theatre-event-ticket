<?php
    require 'db_connection.php';

    class UNIT_DAL
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

        public function select()
        {
            $query = $this->db->prepare("SELECT unit_id, unit_code, unit_name, quantity, unit_active,
            (SELECT user_name FROM base_user bu WHERE bu.user_id = b.created_by_id) posted_by, date_created 
            FROM base_unit b ORDER BY date_created DESC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
            
        }

        public function save($user_id, $page_id, $code, $name, $qty, $uactive)
        {
            $query = $this->db->prepare("SELECT unit_name FROM base_unit WHERE unit_name = :name OR unit_code = :code");
            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if($query->rowCount() == 1){
                return "Unit already exist,info";
            }else{

                //insert unit
                $query = $this->db->prepare("INSERT INTO base_unit(unit_code,unit_name, quantity, unit_active, created_by_id, page_id) 
                VALUES (:code,:name,:qty,:uactive,:user_id,:pid)");    

                $query->bindParam("code", $code, PDO::PARAM_STR);
                $query->bindParam("name", $name, PDO::PARAM_STR);
                $query->bindParam("qty", $qty, PDO::PARAM_STR);
                $query->bindParam("uactive", $uactive, PDO::PARAM_STR);

                $query->bindParam("user_id", $user_id, PDO::PARAM_STR);  
                $query->bindParam("pid", $page_id, PDO::PARAM_STR);                     
                $query->execute();                
                $id = $this->db->lastInsertId();                
                settype($id, "integer"); 

                //insert to log
                $log = $this->base_log($user_id,"Save a record","unit");

                return $id;               

            }   

        } 

        public function update($user_id, $page_id, $code, $name, $qty, $uactive, $uid, $dc) //unit id included
        {

            $query = $this->db->prepare("SELECT unit_name FROM base_unit WHERE (unit_name = :name OR unit_code = :code) AND quantity = :qty AND unit_id != :uid ");
            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);
            $query->bindParam("uid", $uid, PDO::PARAM_STR);
            $query->bindParam("qty", $qty, PDO::PARAM_STR);
            
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if(!empty($row)){
                return "unit already exist,info";
            }else{
                return $this->commitUpdate($user_id, $page_id, $code, $name, $qty, $uactive, $uid, $dc);
            }        

        } 

        function commitUpdate($user_id, $page_id, $code, $name, $qty, $uactive, $uid, $dc)
        {

            $query = $this->db->prepare("UPDATE base_unit SET unit_code = :code, unit_name = :name, quantity = :qty, unit_active = :uactive, 
                    modified_by_id = :user_id, date_modified = :dc
                    WHERE unit_id = :uid"); 

            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);            
            $query->bindParam("qty", $qty, PDO::PARAM_STR);
            $query->bindParam("uactive", $uactive, PDO::PARAM_STR);

            $query->bindParam("uid", $uid, PDO::PARAM_STR);

            $query->bindParam("dc", $dc, PDO::PARAM_STR);
            $query->bindParam("user_id", $user_id, PDO::PARAM_STR);

            $query->execute();
            if($query->rowCount() <= 1){                
                $count = $query->rowCount();
                settype($count, "integer");    
                //insert to log
                $log = $this->base_log($user_id,"Perform an update","unit");
                return $count;      
            }else{   
                return "Unable to update role, try again later";
            }
        }
        
        public function delete($uid,$unit_id)
        {
            $query = $this->db->prepare("DELETE FROM base_ticket_detail WHERE unit_id = :utid");
            $query->bindParam("utid", $unit_id, PDO::PARAM_STR);
            $query->execute();
            if($query){
                $query = $this->db->prepare("DELETE FROM base_unit WHERE unit_id = :utid");
                $query->bindParam("utid", $unit_id, PDO::PARAM_STR);
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