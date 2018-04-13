<?php
//USER ACTION DAL
    require 'db_connection.php';

    class USER_DAL
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

        public function register_auth($page_id, $uname, $fname, $lname, $gender, $email, $phone, $pass, $uactive)
        {
            $query = $this->db->prepare("SELECT user_name FROM base_user WHERE user_name = :uname OR user_email = :email OR user_phone_number = :phone");
            $query->bindParam("uname", $uname, PDO::PARAM_STR);
            $query->bindParam("email", $email, PDO::PARAM_STR);
            $query->bindParam("phone", $phone, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if($query->rowCount() == 1){
                return "User already exist,info";
            }else{
                $rname = "Customer";
                $user_id = 0;

                $query = $this->db->prepare("SELECT role_id FROM base_role WHERE role_name = :rname");
                $query->bindParam("rname", $rname, PDO::PARAM_STR);
                $query->execute();
                $row = $query->fetch(PDO::FETCH_ASSOC);
                $rid = $row["role_id"];

                //insert user
                $query = $this->db->prepare("INSERT INTO base_user(role_id,user_name, user_first_name, user_last_name, user_gender, user_email, user_phone_number, user_password, 
                user_active, created_by_id, page_id) 
                VALUES (:rid,:uname,:fname,:lname,:gender,:email,:phone,:pass,:uactive,:uid,:pid)");    

                $query->bindParam("rid", $rid, PDO::PARAM_STR);
                $query->bindParam("uname", $uname, PDO::PARAM_STR);
                $query->bindParam("fname", $fname, PDO::PARAM_STR);
                $query->bindParam("lname", $lname, PDO::PARAM_STR);
                $query->bindParam("gender", $gender, PDO::PARAM_STR);
                $query->bindParam("email", $email, PDO::PARAM_STR);
                $query->bindParam("phone", $phone, PDO::PARAM_STR);
                $query->bindParam("pass", $pass, PDO::PARAM_STR);  
                $query->bindParam("uactive", $uactive, PDO::PARAM_STR);
                
                $query->bindParam("uid", $user_id, PDO::PARAM_STR);
                $query->bindParam("pid", $page_id, PDO::PARAM_STR);                     
                $query->execute();                
                $id = $this->db->lastInsertId();                
                settype($id, "integer"); 

                //insert to log
                $log = $this->base_log($id,"New user register","Sign up");

                return $id;               

            }   

        } 

        public function verify_email($email)
        {
            $query = $this->db->prepare("SELECT user_id FROM base_user WHERE user_email = :em");
            $query->bindParam("em", $email, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if($query->rowCount() == 1){
                return $row["user_id"];
            }else{
                return "Email does not exist"; 
            }   
        } 

        public function update_password($pass,$id)
        {
            $query = $this->db->prepare("UPDATE base_user SET user_password = :p WHERE user_id = :id");
            $query->bindParam("id", $id, PDO::PARAM_STR);
            $query->bindParam("p", $pass, PDO::PARAM_STR);
            $query->execute();
            if($query){
                //insert to log
                $log = $this->base_log($id,"Perform an update","Forget Password");
                return "Password Updated Successfully,success";
            }else{
                return "An error occur please try again,error"; 
            }   
        } 

        public function login_auth($username,$password)
        {
            $query = $this->db->prepare("SELECT * FROM base_user WHERE user_name = :uname AND user_password = :pword");
            $query->bindParam("uname", $username, PDO::PARAM_STR);
            $query->bindParam("pword", $password, PDO::PARAM_STR);
            $query->execute();
            $userDT = $query->fetch(PDO::FETCH_ASSOC);
            if (!empty($userDT)) {
                // if($userDT['login_status'] == 1){
                //     return "User already logged in on another device";
                // }               

                $id = $userDT['user_id'];
                $query = $this->db->prepare("UPDATE base_user SET login_status = 1 WHERE user_id = :id");
                $query->bindParam("id", $id, PDO::PARAM_STR);
                $query->execute();

                //insert to log
                $log = $this->base_log($id,"User logged in","Login");  

                if(!empty($log)){
                    $query = $this->db->prepare("SELECT user_active uact, user_id uid,(SELECT role_name FROM base_role br WHERE br.role_id = bu.role_id  ) role, role_id rid,
                    user_name uname, user_first_name fname, user_password pword, user_last_name lname, user_email email, user_phone_number phone, 
                    (SELECT default_page_id FROM base_role BR WHERE BR.role_id = bu.role_id) default_page_id, login_status,
                    (SELECT page_name FROM base_page BP WHERE BP.page_id = (SELECT default_page_id FROM base_role BR WHERE BR.role_id = bu.role_id)) default_page                     
                    FROM base_user bu WHERE user_id = :id");
                    $query->bindParam("id", $id, PDO::PARAM_STR);
                    $query->execute();
                    return $query->fetch(PDO::FETCH_ASSOC);
                }

            }
        }

        public function logout_user($userid)
        {
            $query = $this->db->prepare("UPDATE base_user SET login_status = 0 WHERE user_id = :id");
            $query->bindParam("id", $userid, PDO::PARAM_STR);
            $query->execute();
            //insert to log
            $log = $this->base_log($userid,"User logged out","Logout");
            return "SUCCESS";
        }

        public function user_page_access($uid)
        {
            $query = $this->db->prepare("SELECT role_id FROM base_user WHERE user_id = :id");
            $query->bindParam("id", $uid, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
                
            $role_id = $row['role_id'];

            $query = $this->db->prepare("SELECT BP.page_id, page_name, BP.custom_name, page_icon, 
            (SELECT page_type_name FROM base_page_type BPT WHERE BPT.page_type_id = BP.page_type_id) AS module_name,
            (SELECT custom_type FROM base_page_type BPT WHERE BPT.page_type_id = BP.page_type_id) AS module_custom_name,
            (SELECT page_type_icon FROM base_page_type BPT WHERE BPT.page_type_id = BP.page_type_id) AS module_icon,
            (SELECT page_type_order FROM base_page_type BPT WHERE BPT.page_type_id = BP.page_type_id) AS module_order
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

        public function role_list()
        {
            $query = $this->db->prepare("SELECT role_id As drop_value, role_name AS drop_text, role_id, role_name, 
            (SELECT custom_name FROM base_page BP WHERE BR.default_page_id = BP.page_id ) default_page, default_page_id
            FROM base_role BR WHERE role_active = 1 ORDER BY role_name ASC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function select()
        {
            $query = $this->db->prepare("SELECT user_id, role_id, user_name, user_first_name, user_last_name, user_email, user_phone_number, 
            (SELECT default_page_id FROM base_role BR WHERE BR.role_id = bu.role_id) default_page_id, user_active, 
            (SELECT user_name FROM base_user bun WHERE bun.user_id = bu.created_by_id) posted_by, date_created 
            FROM base_user bu ORDER BY date_created DESC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
            
        }

        public function save($user_id, $page_id, $uname, $fname, $lname, $email, $phone, $pass, $uactive, $rid)
        {
            $query = $this->db->prepare("SELECT user_name FROM base_user WHERE user_name = :uname OR user_email = :email OR user_phone_number = :phone");
            $query->bindParam("uname", $uname, PDO::PARAM_STR);
            $query->bindParam("email", $email, PDO::PARAM_STR);
            $query->bindParam("phone", $phone, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if($query->rowCount() == 1){
                return "User already exist : check username or phone number or email,info";
            }else{
                //insert user
                $query = $this->db->prepare("INSERT INTO base_user(role_id,user_name, user_first_name, user_last_name, user_email, user_phone_number, user_password, 
                user_active, created_by_id, page_id) 
                VALUES (:rid,:uname,:fname,:lname,:email,:phone,:pass,:uactive,:user_id,:pid)");    

                $query->bindParam("rid", $rid, PDO::PARAM_STR);
                $query->bindParam("uname", $uname, PDO::PARAM_STR);
                $query->bindParam("fname", $fname, PDO::PARAM_STR);
                $query->bindParam("lname", $lname, PDO::PARAM_STR);
                $query->bindParam("email", $email, PDO::PARAM_STR);
                $query->bindParam("phone", $phone, PDO::PARAM_STR);
                $query->bindParam("pass", $pass, PDO::PARAM_STR);  
                $query->bindParam("uactive", $uactive, PDO::PARAM_STR);

                $query->bindParam("user_id", $user_id, PDO::PARAM_STR);  
                $query->bindParam("pid", $page_id, PDO::PARAM_STR);                     
                $query->execute();                
                $id = $this->db->lastInsertId();                
                settype($id, "integer"); 

                //insert to log
                $log = $this->base_log($user_id,"Save a record","User");

                return $id;               

            }   

        } 

        public function update($user_id, $page_id, $uname, $fname, $lname, $email, $phone, $pass, $uactive, $rid, $uid, $dc) //role id included
        {

            $query = $this->db->prepare("SELECT user_name FROM base_user WHERE (user_name = :uname OR user_email = :email OR user_phone_number = :phone) AND user_id != :id");
            $query->bindParam("uname", $uname, PDO::PARAM_STR);
            $query->bindParam("email", $email, PDO::PARAM_STR);
            $query->bindParam("phone", $phone, PDO::PARAM_STR);
            $query->bindParam("id", $uid, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if(!empty($row)){
                return "User already exist : check username or phone number or email,info";
            }else{
                return $this->commitUpdate($user_id, $page_id, $uname, $fname, $lname, $email, $phone, $pass, $uactive, $rid, $uid, $dc);
            }        

        } 

        function commitUpdate($user_id, $page_id, $uname, $fname, $lname, $email, $phone, $pass, $uactive, $rid, $uid, $dc){
            
            $query = $this->db->prepare("UPDATE base_user SET role_id = :rid, user_name = :uname, user_first_name = :fname, user_last_name = :lname, user_email = :email,
                    user_phone_number = :phone, user_password = :pass, user_active = :uactive, modified_by_id = :user_id, date_modified = :dc
                    WHERE user_id = :id"); 

            $query->bindParam("rid", $rid, PDO::PARAM_STR);
            $query->bindParam("uname", $uname, PDO::PARAM_STR);
            $query->bindParam("fname", $fname, PDO::PARAM_STR);
            $query->bindParam("lname", $lname, PDO::PARAM_STR);
            $query->bindParam("email", $email, PDO::PARAM_STR);
            $query->bindParam("phone", $phone, PDO::PARAM_STR);
            $query->bindParam("pass", $pass, PDO::PARAM_STR); 
            $query->bindParam("uactive", $uactive, PDO::PARAM_STR);

            $query->bindParam("id", $uid, PDO::PARAM_STR);
            $query->bindParam("dc", $dc, PDO::PARAM_STR);

            $query->bindParam("user_id", $user_id, PDO::PARAM_STR);  

            $query->execute();
            if($query->rowCount() <= 1){                
                $count = $query->rowCount();
                settype($count, "integer");    
                //insert to log
                $log = $this->base_log($user_id,"Perform an update","User");
                return $count;      
            }else{   
                return "Unable to update role, try again later";
            }
        }

        public function update_profile($uname, $fname, $lname, $email, $phone, $pass, $uid, $dc) //role id included
        {
            $query = $this->db->prepare("SELECT user_name FROM base_user WHERE (user_name = :uname OR user_email = :email OR user_phone_number = :phone) AND user_id != :id");
            $query->bindParam("uname", $uname, PDO::PARAM_STR);
            $query->bindParam("email", $email, PDO::PARAM_STR);
            $query->bindParam("phone", $phone, PDO::PARAM_STR);
            $query->bindParam("id", $uid, PDO::PARAM_STR);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if(!empty($row)){
                return "User already exist : check username or phone number or email,info";
            }else{
                $query = $this->db->prepare("UPDATE base_user SET user_name = :uname, user_first_name = :fname, user_last_name = :lname, user_email = :email,
                user_phone_number = :phone, user_password = :pass, modified_by_id = :uid, date_modified = :dc
                WHERE user_id = :user_id"); 

                $query->bindParam("uname", $uname, PDO::PARAM_STR);
                $query->bindParam("fname", $fname, PDO::PARAM_STR);
                $query->bindParam("lname", $lname, PDO::PARAM_STR);
                $query->bindParam("email", $email, PDO::PARAM_STR);
                $query->bindParam("phone", $phone, PDO::PARAM_STR);
                $query->bindParam("pass", $pass, PDO::PARAM_STR); 
                
                $query->bindParam("uid", $uid, PDO::PARAM_STR);
                $query->bindParam("user_id", $uid, PDO::PARAM_STR); 
                $query->bindParam("dc", $dc, PDO::PARAM_STR);
                $query->execute();

                if($query->rowCount() <= 1){                
                    $count = $query->rowCount();
                    settype($count, "integer");    
                    //insert to log
                    $log = $this->base_log($uid,"Perform an update","Profile");

                    if(!empty($log)){
                        $query = $this->db->prepare("SELECT user_id uid,(SELECT role_name FROM base_role br WHERE br.role_id = bu.role_id  ) role, role_id rid,
                        user_name uname, user_first_name fname, user_password pword, user_last_name lname, user_email email, user_phone_number phone, 
                        (SELECT default_page_id FROM base_role BR WHERE BR.role_id = bu.role_id) default_page_id, login_status,
                        (SELECT page_name FROM base_page BP WHERE BP.page_id = (SELECT default_page_id FROM base_role BR WHERE BR.role_id = bu.role_id)) default_page 
                        FROM base_user bu WHERE user_id = :id");
                        $query->bindParam("id", $uid, PDO::PARAM_STR);
                        $query->execute();
                        return $query->fetch(PDO::FETCH_ASSOC);
                    }
                        
                }else{   
                    return "Unable to update profile, try again later";
                }

            }        

        } 

        public function delete($cuid,$uid)
        {
            $query = $this->db->prepare("DELETE FROM base_log WHERE user_id = :uid");
            $query->bindParam("uid", $uid, PDO::PARAM_STR);
            $query->execute();
            
            $query = $this->db->prepare("DELETE FROM base_user WHERE user_id = :uid");
            $query->bindParam("uid", $uid, PDO::PARAM_STR);
            $query->execute();
            if($query){           
                $log = $this->base_log($cuid,"Delete a record","User");
                return "Record successfully deleted,success";
            }           
            
        }


        

            
     }
 
?>