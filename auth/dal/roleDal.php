<?php
//USER ACTION DAL
    require 'db_connection.php';

    class ROLE_DAL
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
            $query = $this->db->prepare("SELECT role_id, role_name, role_active, (SELECT user_name FROM base_user bu WHERE bu.user_id = br.created_by_id) posted_by, date_created 
            FROM base_role br ORDER BY date_created DESC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function save($user_id, $pid, $rname, $ractive, $pages, $dpid)
        {
            $query = $this->db->prepare("SELECT role_name FROM base_role WHERE role_name = :role_name");
            $query->bindParam("role_name", $rname, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if(empty($row)){

                $query = $this->db->prepare("INSERT INTO base_role(role_name, role_active, default_page_id, created_by_id, page_id) 
                VALUES (:rname,:ractive,:dpid,:user_id,:pid)");                
                $query->bindParam("rname", $rname, PDO::PARAM_STR);
                $query->bindParam("ractive", $ractive, PDO::PARAM_STR);
                $query->bindParam("dpid", $dpid, PDO::PARAM_STR);

                $query->bindParam("user_id", $user_id, PDO::PARAM_STR);  
                $query->bindParam("pid", $pid, PDO::PARAM_STR);                     
                $query->execute();
                $rid = $this->db->lastInsertId();
                //loop through pages to insert in role access table
                if(!empty($rid)){
                    $sql = "INSERT INTO base_page_access (page_id, role_id) VALUES (?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($pages as $pg)
                    {
                        $val = array($pg->page_id, $rid);
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer"); 

                    //insert to log
                    $log = $this->base_log($user_id,"Save a record","Role");

                    return $idd;
                }                  
            }else{
                $result = 'Role already exist,warning';
                return $result;
            };
        } 
        
        public function update($user_id, $pid, $rid, $rname, $ractive, $pages, $dpid, $dc) //role id included
        {
            $query = $this->db->prepare("SELECT role_name FROM base_role WHERE role_id != :id AND role_name = :rname");
            $query->bindParam("id", $rid, PDO::PARAM_STR);
            $query->bindParam("rname", $rname, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if(!empty($row)){
                return "Role already exist,info";
            }else{
                return $this->commitUpdate($user_id, $pid, $rid, $rname, $ractive, $pages, $dpid, $dc);
            }
        }  

        function commitUpdate($user_id, $pid, $rid, $rname, $ractive, $pages, $dpid, $dc){

            $query = $this->db->prepare("UPDATE base_role SET role_name = :rname, role_active = :ractive, default_page_id = :dpid, modified_by_id = :id, date_modified = :dc
                    WHERE role_id = :rid"); 
            $query->bindParam("id", $user_id, PDO::PARAM_STR);
            $query->bindParam("dc", $dc, PDO::PARAM_STR);
            $query->bindParam("rid", $rid, PDO::PARAM_STR); 
            $query->bindParam("rname", $rname, PDO::PARAM_STR);
            $query->bindParam("ractive", $ractive, PDO::PARAM_STR);
            $query->bindParam("dpid", $dpid, PDO::PARAM_STR);
            $query->execute();
            if($query->rowCount() <= 1){
                //delete all page related to this role in page access table and re insert new ones                
                $query = $this->db->prepare("DELETE FROM base_page_access WHERE role_id = :rid"); 
                $query->bindParam("rid", $rid, PDO::PARAM_STR); 
                $query->execute();
                if($query->rowCount() >= 1){
                    //insert into base page access for the role
                    $sql = "INSERT INTO base_page_access (page_id, role_id) VALUES (?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($pages as $pg)
                    {
                        $val = array($pg->page_id, $rid);
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer");
                    //insert to log
                    $log = $this->base_log($user_id,"Perform an Update","Role");
                    return $idd;
                }else{
                    //no child exist before
                    $sql = "INSERT INTO base_page_access (page_id, role_id) VALUES (?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($pages as $pg)
                    {
                        $val = array($pg->page_id, $rid);
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer");
                    //insert to log
                    $log = $this->base_log($user_id,"Perform an Update","Role");
                    return $idd;
                }                              
            }else{   
                return "Unable to update role, try again later";
            }
        }


        public function delete($uid,$role_id)
        {
            $query = $this->db->prepare("DELETE FROM base_page_access WHERE role_id = :rid");
            $query->bindParam("rid", $role_id, PDO::PARAM_STR);
            $query->execute();
            if($query){
                $query = $this->db->prepare("DELETE FROM base_role WHERE role_id = :rid");
                $query->bindParam("rid", $role_id, PDO::PARAM_STR);
                $query->execute();
                if($query->rowCount() == 1){

                    $log = $this->base_log($uid,"Delete a record","Role");

                    return "Record successfully deleted,success";
                }
            }else{
                return "Could not delete this time please try again,error";
            }            
            
        }


        public function page_list()
        {
            $query = $this->db->prepare("SELECT BP.page_id, BP.custom_name, BP.page_id drop_value, custom_name AS drop_text, custom_type AS drop_custom,
            custom_type AS module_custom_name
            FROM base_page BP, base_page_type BPT
            WHERE BP.page_type_id = BPT.page_type_id AND BPT.page_type_order > 0");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }
        
        public function role_page_access($role_id)
        {
            $query = $this->db->prepare("SELECT BP.page_id, BP.custom_name, BP.page_id drop_value, BP.custom_name AS drop_text, 
            (SELECT custom_type FROM base_page_type BPT WHERE BPT.page_type_id = BP.page_type_id) AS drop_custom,
            (SELECT custom_type FROM base_page_type BPT WHERE BPT.page_type_id = BP.page_type_id) AS module_custom_name
            FROM base_page_access AS BPA, base_page AS BP, base_role AS BR
            WHERE BPA.role_id = BR.role_id AND BR.role_id = :rid AND BP.page_id = BPA.page_id");
            $query->bindParam("rid", $role_id, PDO::PARAM_STR);
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }
            

            
     }
 
?>