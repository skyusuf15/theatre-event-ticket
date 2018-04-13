<?php
//USER ACTION DAL
    require 'db_connection.php';

    class CATEGORY_DAL
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

        public function category_type_list()
        {
            $query = $this->db->prepare("SELECT type_id As drop_value, type_name AS drop_text, type_id, type_name
            FROM base_category_type ORDER BY type_name ASC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function select()
        {
            $query = $this->db->prepare("SELECT category_id, category_code, category_name, type_id, category_active,
            (SELECT type_name FROM base_category_type CT WHERE CT.type_id = C.type_id) category_type, 
            (SELECT user_name FROM base_user bu WHERE bu.user_id = C.created_by_id) posted_by, date_created 
            FROM base_category C ORDER BY date_created DESC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
            
        }

        public function save($user_id, $page_id, $code, $name, $type, $cactive)
        {
            $query = $this->db->prepare("SELECT category_name FROM base_category WHERE (category_name = :name OR category_code = :code) AND type_id = :type");
            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);
            $query->bindParam("type", $type, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if($query->rowCount() == 1){
                return "Category already exist,info";
            }else{

                //insert category
                $query = $this->db->prepare("INSERT INTO base_category(category_code,category_name, type_id, category_active, created_by_id, page_id) 
                VALUES (:code,:name,:type,:cactive,:user_id,:pid)");    

                $query->bindParam("code", $code, PDO::PARAM_STR);
                $query->bindParam("name", $name, PDO::PARAM_STR);
                $query->bindParam("type", $type, PDO::PARAM_STR);
                $query->bindParam("cactive", $cactive, PDO::PARAM_STR);

                $query->bindParam("user_id", $user_id, PDO::PARAM_STR);  
                $query->bindParam("pid", $page_id, PDO::PARAM_STR);                     
                $query->execute();                
                $id = $this->db->lastInsertId();                
                settype($id, "integer"); 

                //insert to log
                $log = $this->base_log($user_id,"Save a record","Category");

                return $id;               

            }   

        } 

        public function update($user_id, $page_id, $code, $name, $type, $cactive, $cid, $dc) //role id included
        {

            $query = $this->db->prepare("SELECT category_name FROM base_category WHERE (category_name = :name OR category_code = :code) AND type_id = :type AND category_id != :cid ");
            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);
            $query->bindParam("cid", $cid, PDO::PARAM_STR);
            $query->bindParam("type", $type, PDO::PARAM_STR);
            
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if(!empty($row)){
                return "Category already exist,info";
            }else{
                return $this->commitUpdate($user_id, $page_id, $code, $name, $type, $cactive, $cid, $dc);
            }        

        } 

        function commitUpdate($user_id, $page_id, $code, $name, $type, $cactive, $cid, $dc)
        {

            $query = $this->db->prepare("UPDATE base_category SET category_code = :code, category_name = :name, type_id = :type, category_active = :cactive, 
                    modified_by_id = :user_id, date_modified = :dc
                    WHERE category_id = :cid"); 

            $query->bindParam("name", $name, PDO::PARAM_STR);
            $query->bindParam("code", $code, PDO::PARAM_STR);            
            $query->bindParam("type", $type, PDO::PARAM_STR);
            $query->bindParam("cactive", $cactive, PDO::PARAM_STR);

            $query->bindParam("cid", $cid, PDO::PARAM_STR);

            $query->bindParam("dc", $dc, PDO::PARAM_STR);
            $query->bindParam("user_id", $user_id, PDO::PARAM_STR);

            $query->execute();
            if($query->rowCount() <= 1){                
                $count = $query->rowCount();
                settype($count, "integer");    
                //insert to log
                $log = $this->base_log($user_id,"Perform an update","Category");
                return $count;      
            }else{   
                return "Unable to update role, try again later";
            }
        }

        public function delete($uid,$cid)
        {
            $query = $this->db->prepare("DELETE FROM base_category WHERE category_id = :cid");
            $query->bindParam("cid", $cid, PDO::PARAM_STR);
            $query->execute();
            if($query){                
                $log = $this->base_log($uid,"Delete a record","Category");
                return "Record successfully deleted,success";
            }           
            
        }

        
        

            
     }
 
?>