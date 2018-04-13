<?php
//USER ACTION DAL - recieved all javascript call and send to Dal for querying
	require '../dal/categoryDal.php';
	
	$arr = $_POST['json'];
	$postedData = json_decode($arr);
	$action = $postedData[0];

	$DAL = new CATEGORY_DAL();

	switch ($action) {
		case 'category_type_list':
			$list = $DAL->category_type_list();
			echo $result = json_encode($list); //return page access	
		break;
		case 'select':
			$list = $DAL->select();
			echo $result = json_encode($list); //return page access	
		break;
		case 'save':
			$dt = $postedData[1];
			$code = htmlentities($dt->code);
			$name = htmlentities($dt->name);
			$type = htmlentities($dt->type);			   
			$cactive = htmlentities($dt->cat_active);

			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

           if($code != '' && $name != '' && $type != '' ){
               try {				   
				   $res = $DAL->save($user_id, $page_id, $code, $name, $type, $cactive);	
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
			
			$cid = htmlentities($dt->cid);
			$dc = htmlentities($dt->dc);

			$code = htmlentities($dt->code);
			$name = htmlentities($dt->name);
			$type = htmlentities($dt->type);			   
			$cactive = htmlentities($dt->cat_active);

			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

			if($code != '' && $name != '' && $type != ''){
				try {				   
					$res = $DAL->update($user_id, $page_id, $code, $name, $type, $cactive, $cid, $dc);	
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
			$cid = htmlentities($dt->cid);
			$uid = htmlentities($dt->uid);
			$msg = $DAL->delete($uid,$cid);
			echo $msg;
		break;

		default:
			# code...
			break;
	}

   //echo $result;
 
?>