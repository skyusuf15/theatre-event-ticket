<?php
	require '../dal/cartDal.php';

	$arr = $_POST['json'];
	$postedData = json_decode($arr);
	$action = $postedData[0];

	$DAL = new CART_DAL();

	switch ($action) {		
		case 'order_ticket':
            $dt = $postedData[1];

            $tamount = htmlentities($dt->total_amount);
			$tqty = htmlentities($dt->ticket_qty);
			$order_list = $dt->order_list;
            
			$user_id = htmlentities($dt->uid);
			// $page_id = htmlentities($dt->pid);

           if($user_id != '' && $tamount != '' && $tqty != '' && $order_list != ''){
               try {				   
				   $res = $DAL->order_ticket($user_id, $tqty, $tamount, $order_list);	
                   $type = gettype($res);
                    if(!empty($res) &&  $type == 'integer'){                                                 
                      echo $result = 'Transaction was successful,success';
                    }else{
					  echo $res; //return       
                    } 
               } catch (Exception $e) {
                  echo $result = $e->getMessage();               
               }
           }else{
               echo $result = 'Empty field not allowed,error';           
           }		  
            
		break;
		
		default:
			# code...
			break;
	}
 
?>