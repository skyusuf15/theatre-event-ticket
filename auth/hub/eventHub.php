<?php

	require '../dal/eventDal.php';
	
	$arr = $_POST['json'];
	$postedData = json_decode($arr);
	$action = $postedData[0];

	$DAL = new EVENT_DAL();

	switch ($action) {
		case 'hall_list':
			$list = $DAL->hall_list();
			echo $result = json_encode($list); //return page access	
        break;
        case 'event_category_list':
			$list = $DAL->event_category_list();
			echo $result = json_encode($list); //return page access	
        break;
        case 'event_tag_list':
			$dt = $postedData[1];
			$eid = htmlentities($dt->eid);
			$list = $DAL->event_tag_list($eid);
			echo $result = json_encode($list);
		break;
		case 'select':
			$list = $DAL->select();
			echo $result = json_encode($list); //return page access	
		break;
		case 'save':
			$dt = $postedData[1];
			$code = htmlentities($dt->code);
			$name = htmlentities($dt->name);
            $desc = htmlentities($dt->desc);           		   
			$eactive = htmlentities($dt->event_active);
            $hall_id = htmlentities($dt->hall_id);	
            $use_cap = htmlentities($dt->use_cap);
            $date = htmlentities($dt->date);
            $time = htmlentities($dt->time);
            $tags = $dt->tags;

			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

           if($code != '' && $name != '' && $hall_id != ''  && $date != '' && $time != ''){
               try {				   
				   $res = $DAL->save($user_id, $page_id, $code, $name, $desc, $eactive, $hall_id, $use_cap, $date, $time, $tags);	
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

            $eid = htmlentities($dt->eid);
			$dc = htmlentities($dt->dc);

			$code = htmlentities($dt->code);
			$name = htmlentities($dt->name);
            $desc = htmlentities($dt->desc);           		   
			$eactive = htmlentities($dt->event_active);
            $hall_id = htmlentities($dt->hall_id);	
            $use_cap = htmlentities($dt->use_cap);
            $date = htmlentities($dt->date);
            $time = htmlentities($dt->time);
            $tags = $dt->tags;

			$user_id = htmlentities($dt->uid);
			$page_id = htmlentities($dt->pid);

           if($code != '' && $name != '' && $hall_id != ''  && $date != '' && $time != ''){
               try {				   
				   $res = $DAL->update($user_id, $page_id, $code, $name, $desc, $eactive, $hall_id, $use_cap, $date, $time, $tags, $eid, $dc);	
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
			$eid = htmlentities($dt->eid);
			$uid = htmlentities($dt->uid);
			$msg = $DAL->delete($uid,$eid);
			echo $msg;
		break;

		default:
			# code...
			break;
	}

   //echo $result;



    


?>