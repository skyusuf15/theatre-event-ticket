<?php
//USER ACTION DAL - recieved all javascript call and send to Dal for querying
	require '../dal/roleDal.php';

	$arr = $_POST['json'];
	$postedData = json_decode($arr);
	$action = $postedData[0];

	$DAL = new ROLE_DAL();

	switch ($action) {
		case 'page_list':
			$list = $DAL->page_list();
			echo $result = json_encode($list); //return page access	
		break;
		case 'role_page_access':
			$dt = $postedData[1];
			$rid = htmlentities($dt->rid);
			$list = $DAL->role_page_access($rid);
			echo $result = json_encode($list);
		break;
		case 'select':
			$list = $DAL->select();
			echo $result = json_encode($list); //return page access	
		break;				
		case 'save':
			$dt = $postedData[1];
			$rname = htmlentities($dt->role_name);
			$ractive = htmlentities($dt->role_active);
			$pages = $dt->pages;
			$dpid = htmlentities($dt->dpid);
			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

           if($rname != '' && $ractive != '' && $pages != ''){
               try {				   
				   $res = $DAL->save($user_id, $page_id, $rname, $ractive, $pages, $dpid);	
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
			$rid = htmlentities($dt->rid);
			$rname = htmlentities($dt->role_name);
			$ractive = htmlentities($dt->role_active);
			$pages = $dt->pages;			
			$dpid = htmlentities($dt->dpid);

			$dc = htmlentities($dt->dc);
			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

           if($rid != '' && $rname != '' && $ractive != '' && $pages != ''){
               try {				   
				   $res = $DAL->update($user_id, $page_id, $rid, $rname, $ractive, $pages, $dpid, $dc);	
                   $type = gettype($res);
                    if(!empty($res) &&  $type == 'integer'){                                                 
                      echo $result = 'Transaction was successful,success';
                    }else{
					  echo $res; //return       
                    } 
               } catch (Exception $e) {
				echo $e;
                 // echo $result; //= $e->getMessage();               
               }
           }else{
               echo $result = 'Empty field not allowed,error';           
		   }
		   
		break;
		case 'delete':
			$dt = $postedData[1];
			$rid = htmlentities($dt->rid);
			$uid = htmlentities($dt->uid);
			$msg = $DAL->delete($uid,$rid);
			echo $msg;
		break;
		
		default:
			# code...
			break;
	}

   //echo $result;
 
?>