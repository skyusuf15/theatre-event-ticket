<?php
//USER ACTION DAL
    require 'db_connection.php';

    class MY_TICKET_DAL
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

        public function order_list($uid)
        {
            $query = $this->db->prepare("SELECT txn_id, ticket_qty tqty, total_amount tamount, status, date_created 
            FROM base_txn WHERE user_id = :user_id ORDER BY date_created DESC");
            $query->bindParam("user_id", $uid, PDO::PARAM_STR);
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }

        public function order_ticket_list($tid)
        {
            $query = $this->db->prepare("SELECT event_id eid, ticket_id tid, quantity_order qty, unit_price price, total_amount total,
            (SELECT event_name FROM base_event be WHERE be.event_id = btd.event_id) ename,
            (SELECT unit_name FROM base_unit bu WHERE bu.unit_id = btd.unit_id) uname,
            (SELECT quantity FROM base_unit bu WHERE bu.unit_id = btd.unit_id) qty_per_unit            
            FROM base_txn_detail btd WHERE btd.txn_id = :tid");
            $query->bindParam("tid", $tid, PDO::PARAM_STR);
            $query->execute();
            $data = array();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        }       
        

     }
 
?>