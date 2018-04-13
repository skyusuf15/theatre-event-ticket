<?php
//USER ACTION DAL
    require 'db_connection.php';

    class EXPLORE_DAL
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
            $query = $this->db->prepare("SELECT ticket_id tid, ticket_code tcode, ticket_name tname, be.event_id eid, 
            (SELECT hall_name FROM base_hall bh WHERE bh.hall_id = be.hall_id) hall, 
            event_name ename, event_description 'desc', image_link url, thumbnail_link turl, use_hall_capacity use_cap, event_date, event_time
            FROM base_ticket bt, base_event be
            WHERE bt.event_id = be.event_id AND bt.ticket_active =1 AND be.event_active =1 ORDER BY event_date DESC");
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function event_tag_list($eid)
        {
            $query = $this->db->prepare("SELECT (SELECT category_name FROM base_category bc WHERE bc.category_id = bt.category_id AND bc.category_active = 1) tag
            FROM base_tag bt WHERE bt.event_id = :eid");
            $query->bindParam("eid", $eid, PDO::PARAM_STR);
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function event_unit_list($eid)
        {
            $query = $this->db->prepare("SELECT ticket_id tid FROM base_ticket WHERE event_id = :eid AND ticket_active = 1");
            $query->bindParam("eid", $eid, PDO::PARAM_STR);
            $query->execute();

            $row = $query->fetch(PDO::FETCH_ASSOC);        
            $tid = $row['tid']; 

            $data = array();
            if(!empty($tid)){
                $query = $this->db->prepare("SELECT unit_id uid, btd.price, btd.discount, 
                (SELECT unit_name FROM base_unit bu WHERE bu.unit_id = btd.unit_id AND bu.unit_active = 1) name,
                (SELECT quantity FROM base_unit bu WHERE bu.unit_id = btd.unit_id AND bu.unit_active = 1) quant_per_unit
                FROM base_ticket_detail btd
                WHERE ticket_id = :tid");
                $query->bindParam("tid", $tid, PDO::PARAM_STR);
                $query->execute();

                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }
                return $data;

            }//end if
            else{
                return $data;
            }
            
        }





            
     }
 
?>