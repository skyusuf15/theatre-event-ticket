<?php
//USER ACTION DAL - recieved all javascript call and send to Dal for querying
	require '../dal/ticketDal.php';

	$arr = $_POST['json'];
	$postedData = json_decode($arr);
	$action = $postedData[0];

	$DAL = new TICKET_DAL();

	switch ($action) {
		case 'event_list':
			$list = $DAL->event_list();
			echo $result = json_encode($list); //return page access	
        break;
        case 'unit_list':
			$list = $DAL->unit_list();
			echo $result = json_encode($list); //return page access	
		break;
		case 'ticket_unit_list':
			$dt = $postedData[1];
			$tid = htmlentities($dt->tid);
			$list = $DAL->ticket_unit_list($tid);
			echo $result = json_encode($list);
		break;
		case 'select':
			$list = $DAL->select();
			echo $result = json_encode($list); //return page access	
		break;				
		case 'save':
            $dt = $postedData[1];

            $eid = htmlentities($dt->event_id);
            $tcode = htmlentities($dt->code);
			$tname = htmlentities($dt->name);
			$tactive = htmlentities($dt->tactive);
            $unit_list = $dt->unit_list;
            
			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

           if($eid != '' && $tcode != '' && $tname != '' && $tcode != '' && $unit_list != ''){
               try {				   
				   $res = $DAL->save($user_id, $page_id, $eid, $tcode, $tname, $tactive, $unit_list);	
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
		case 'update':
            $dt = $postedData[1];
            
            $tid = htmlentities($dt->tid);
            $dc = htmlentities($dt->dc);

            $eid = htmlentities($dt->event_id);
            $tcode = htmlentities($dt->code);
			$tname = htmlentities($dt->name);
			$tactive = htmlentities($dt->tactive);
            $unit_list = $dt->unit_list;
            
			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

           if($eid != '' && $tcode != '' && $tname != '' && $tcode != '' && $unit_list != ''){
               try {				   
				   $res = $DAL->update($user_id, $page_id, $eid, $tcode, $tname, $tactive, $unit_list, $tid, $dc);	
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
		case 'delete':
			$dt = $postedData[1];
			$tid = htmlentities($dt->tid);
			$uid = htmlentities($dt->uid);
			$msg = $DAL->delete($uid,$tid);
			echo $msg;
		break;
		
		default:
			# code...
			break;
	}

   //echo $result;
 
?>