<?php
//USER ACTION DAL - recieved all javascript call and send to Dal for querying
	session_start();
	// session_destroy();
	require '../dal/userDal.php';
	
	$arr = $_POST['json'];
	$postedData = json_decode($arr);
	$action = $postedData[0];

	$DAL = new USER_DAL();

	switch ($action) {
		case 'register_auth':
			$dt = $postedData[1];
			$uname = strtolower(htmlentities($dt->username));			
			$fname = htmlentities($dt->firstname);
			$lname = htmlentities($dt->lastname);
			$gender = htmlentities($dt->gender);
			$email = htmlentities($dt->email);
			$phone = htmlentities($dt->phone);
			$pword = strtolower(htmlentities($dt->password));

			$encryptPass = md5($pword);
			$pass = 'tic'.$encryptPass.'ket';
			   
			$uactive = 1;
			$page_id = 14; //change later

           if($uname != '' && $fname != '' && $lname != '' && $email != '' && $phone != '' && $uactive != '' && $pass != ''){
			   if($gender == ''){
					echo $result = 'Gender cannot be empty,error';
			   }else{
					try {				   
						$res = $DAL->register_auth($page_id, $uname, $fname, $lname, $gender, $email, $phone, $pass, $uactive);	
						$type = gettype($res);
						if(!empty($res) &&  $type == 'integer'){                                                 
						echo $result = 'Registration was successful,success';
						}else{
						echo $res; //return       
						} 
					} catch (Exception $e) {
					echo $result = $e->getMessage();               
					}
			   }               
           }else{
               echo $result = 'Empty field not allowed,error';           
		   }		   
		break;
		case 'verify_email':
			$dt = $postedData[1];
			$email = strtolower(htmlentities($dt->email));
           if($email != ''){
				try {				   
					$res = $DAL->verify_email($email);	
					$type = gettype($res);
					if(!empty($res) && $type == 'integer'){                                                 
						echo $res;
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
		case 'update_password':
			$dt = $postedData[1];
			$pword = strtolower(htmlentities($dt->password));

			$encryptPass = md5($pword);
			$pass = 'tic'.$encryptPass.'ket';

			$id = strtolower(htmlentities($dt->id));
           if($pass != ''){
				try {				   
					$res = $DAL->update_password($pass,$id);	
					$type = gettype($res);
					if(!empty($res) && $type == 'integer'){                                                 
						echo $res;
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
		case 'login_auth':
			$dt = $postedData[1];
			$uname = strtolower(htmlentities($dt->username));
			$pword = strtolower(htmlentities($dt->password));
			$encryptPass = md5($pword);
			$pass = 'tic'.$encryptPass.'ket';

			$userData = $DAL->login_auth($uname, $pass);

			if(empty($userData)){
				echo $result = 'Incorrect Username or Password.';                          
			}else if($userData['uact'] == 0){
				echo $result = 'This user has been disabled on the system, contact the administrator for help';       
			}else{	

				if ($userData['uname'] == $uname && $pass == $userData['pword']) {

					$StringTable = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
					$Shuffle_string = str_shuffle($StringTable);
					$Get_half = substr($Shuffle_string, 0, strlen($StringTable)/2);
					$Generate_code = strrev(substr($Get_half, 0,8)).'-'.strrev(substr($Get_half, 5,4)).'-'.strrev(substr($Get_half, 9,4)).'-'.strrev(substr($Get_half, 12,4)).'-'.strrev(substr($Get_half, 0,12));

					$_SESSION['user'] = $userData;
					$userData['ssid'] = $Generate_code;
					$_SESSION['ssid'] = $Generate_code;
					$_SESSION['locked'] = false;
					$_SESSION['logged_in'] = true;
					
					echo $result = json_encode($userData);
				}else{         
					echo $result = $userData;
				}

			}	  
		break;
		case 'logout_user':
			try {                  
				if(isset($_SESSION['user'])){
					$data = $DAL->logout_user($_SESSION['user']['uid']);
					if(!empty($data)){                          
						echo $result = $data;
						session_destroy();
					}else{
						throw new Exception("FAIL", 1);        
					}
				}else{
					echo 'not set';
				}                     
			} catch (Exception $e) {
				echo $result = $e->getMessage();               
			}
		break; 
		case 'user_page_access':
			$dt = $postedData[1];
	        $uid = htmlentities($dt->uid);
			$access = $DAL->user_page_access($uid);
			echo $result = json_encode($access); //return page access	
		break;	
		case 'role_page_access':
			$dt = $postedData[1];
			$rid = htmlentities($dt->rid);
			$list = $DAL->role_page_access($rid);
			echo $result = json_encode($list);
		break;
		case 'role_list':
			$list = $DAL->role_list();
			echo $result = json_encode($list); //return page access	
		break;
		case 'select':
			$list = $DAL->select();
			echo $result = json_encode($list); //return page access	
		break;
		case 'save':
			$dt = $postedData[1];
			$rid = htmlentities($dt->rid);
			$uname = strtolower(htmlentities($dt->uname));			
			$fname = htmlentities($dt->fname);
			$lname = htmlentities($dt->lname);
			$email = htmlentities($dt->email);
			$phone = htmlentities($dt->phone);
			$pword = strtolower(htmlentities($dt->pword));

			$encryptPass = md5($pword);
			$pass = 'tic'.$encryptPass.'ket';
			   
			$uactive = htmlentities($dt->user_active);

			$user_id = htmlentities($dt->cuid);
			$page_id = htmlentities($dt->pid);

           if($rid != '' && $uname != '' && $fname != '' && $lname != '' && $email != '' && $phone != '' && $uactive != ''){
               try {				   
				   $res = $DAL->save($user_id, $page_id, $uname, $fname, $lname, $email, $phone, $pass, $uactive, $rid);	
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
			
			$uid = htmlentities($dt->uid);
			$dc = htmlentities($dt->dc);

			$rid = htmlentities($dt->rid);
			$uname = htmlentities($dt->uname);
			$fname = htmlentities($dt->fname);
			$lname = htmlentities($dt->lname);
			$email = htmlentities($dt->email);
			$phone = htmlentities($dt->phone);

			$pword = htmlentities($dt->pword);
			$encryptPass = md5($pword);
			$pass = 'tic'.$encryptPass.'ket';
			
			$uactive = htmlentities($dt->user_active);

			$user_id = htmlentities($dt->cuid);
			$page_id = htmlentities($dt->pid);

			if($rid != '' && $uname != '' && $fname != '' && $lname != '' && $email != '' && $phone != '' && $uactive != ''){
				try {				   
					$res = $DAL->update($user_id, $page_id, $uname, $fname, $lname, $email, $phone, $pass, $uactive, $rid, $uid, $dc);	
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
		case 'update_profile':
			$dt = $postedData[1];
			
			$uid = htmlentities($dt->uid);
			$dc = htmlentities($dt->dc);

			$uname = htmlentities($dt->uname);
			$fname = htmlentities($dt->fname);
			$lname = htmlentities($dt->lname);
			$email = htmlentities($dt->em);
			$phone = htmlentities($dt->ph);

			$pword = htmlentities($dt->pass);
			$encryptPass = md5($pword);
			$pass = 'tic'.$encryptPass.'ket';

			$userData = $DAL->update_profile($uname, $fname, $lname, $email, $phone, $pass, $uid, $dc);

			if(empty($userData)){
				echo $result = $userData;                          
			}else{	
				$_SESSION['user'] = $userData;					
				echo $result = json_encode($userData);
			}

		break;
		case 'delete':
			$dt = $postedData[1];
			$uid = htmlentities($dt->uid);
			$cuid = htmlentities($dt->cuid);
			$msg = $DAL->delete($cuid,$uid);
			echo $msg;
		break;

		default:
			# code...
			break;
	}

   //echo $result;
 
?>