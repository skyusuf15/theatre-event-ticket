<?php
	require '../dal/my_ticketDal.php';

	$arr = $_POST['json'];
	$postedData = json_decode($arr);
	$action = $postedData[0];

	$DAL = new MY_TICKET_DAL();

	switch ($action) {		
        case 'order_list':   
            $dt = $postedData[1];     
            $uid = htmlentities($dt->uid);

            $order = $DAL->order_list($uid);

            $orderData = array(); //to store all order
            if (!empty($order)) {
                $orderData[] = $order; //store all list

                $list = array(); 

                $orderLength = count($order); //get length
                
                //fetch list tag_list
                for($i = 0; $i < $orderLength; $i++) {
                    $id = $order[$i];
                    $tid = $id['txn_id']; //get each list id
                    
                    $list[$i]['txn_id'] = 'TMS'.$id['txn_id'];
                    $list[$i]['tqty'] = $id['tqty'];
                    $list[$i]['tamount'] = $id['tamount'];
                    $list[$i]['status'] = $id['status'];
                    $list[$i]['date_created'] = $id['date_created'];

                    //fetch each list tag_list
                    $ticket = $DAL->order_ticket_list($tid);   
                    $list[$i]['unit_list'] = $ticket;

                }
                echo $result = json_encode($list);
            }else{
                echo $result = json_encode($list);
            }
		break;
		
		default:
			# code...
			break;
	}
 
?>