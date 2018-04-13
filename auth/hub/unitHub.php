<?php
//USER ACTION DAL - recieved all javascript call and send to Dal for querying
	require '../dal/unitDal.php';
	
	$arr = $_POST['json'];
	$postedData = json_decode($arr);
	$action = $postedData[0];

	$DAL = new UNIT_DAL();

	switch ($action) {
		case 'select':
			$list = $DAL->select();
			echo $result = json_encode($list); //return page access	
		break;
		case 'save':
			$dt = $postedData[1];
			$code = htmlentities($dt->code);
			$name = htmlentities($dt->name);
			$qty = htmlentities($dt->qty);			   
			$uactive = htmlentities($dt->unit_active);

			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

           if($code != '' && $name != '' && $qty != '' ){
               try {				   
				   $res = $DAL->save($user_id, $page_id, $code, $name, $qty, $uactive);	
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
			
			$uid = htmlentities($dt->unit_id);
			$dc = htmlentities($dt->dc);

			$code = htmlentities($dt->code);
			$name = htmlentities($dt->name);
			$qty = htmlentities($dt->qty);			   
			$uactive = htmlentities($dt->unit_active);

			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

			if($code != '' && $name != '' && $qty != ''){
				try {				   
					$res = $DAL->update($user_id, $page_id, $code, $name, $qty, $uactive, $uid, $dc);	
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
			$unit_id = htmlentities($dt->unit_id);
			$uid = htmlentities($dt->uid);
			$msg = $DAL->delete($uid,$unit_id);
			echo $msg;
		break;

		default:
			# code...
			break;
	}

   //echo $result;
 
?>