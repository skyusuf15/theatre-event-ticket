<?php
//USER ACTION DAL
    require 'db_connection.php';

    class CART_DAL
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

        public function order_ticket($user_id, $tqty, $tamount, $order_list)
        {
            $paid = "paid";
            $query = $this->db->prepare("INSERT INTO base_txn(user_id, ticket_qty, total_amount, status) 
                VALUES (:user_id,:tqty,:tamount,:status)");  

                $query->bindParam("tqty", $tqty, PDO::PARAM_STR);
                $query->bindParam("tamount", $tamount, PDO::PARAM_STR);
                $query->bindParam("status", $paid, PDO::PARAM_STR);

                $query->bindParam("user_id", $user_id, PDO::PARAM_STR);                     
                $query->execute();
                $txn_id = $this->db->lastInsertId();
                //loop through order list to insert in order to base_order table
                if(!empty($txn_id)){
                    $sql = "INSERT INTO base_txn_detail (txn_id, event_id, ticket_id, unit_id, quantity_order, unit_price, total_amount, is_used,status) VALUES (?,?,?,?,?,?,?,?,?)";
                    $stmt = $this->db->prepare($sql);
                    foreach($order_list as $order)
                    {
                        $val = array($txn_id, $order->eid, $order->tid, $order->uid, $order->qty, $order->price, $order->total, 0, 'pending');
                        $stmt->execute($val);
                    }
                    $idd = $this->db->lastInsertId();                
                    settype($idd, "integer"); 

                    //insert to log
                    $log = $this->base_log($user_id,"Order some ticket","Cart");

                    return $idd;
                } 
        }       
        

     }
 
?>