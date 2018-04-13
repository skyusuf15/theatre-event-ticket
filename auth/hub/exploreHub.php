<?php
//USER ACTION DAL - recieved all javascript call and send to Dal for querying
	require '../dal/exploreDal.php';

	$arr = $_POST['json'];
	$postedData = json_decode($arr);
	$action = $postedData[0];

	$DAL = new EXPLORE_DAL();

	switch ($action) {
        case 'event_list':            
            $events = $DAL->event_list();

            $eventData = array(); //to store all events
            if (!empty($events)) {
                $eventData[] = $events; //store all event

                $tag_list = array();
                $unit_list = array(); 

                $eventLength = count($events); //get length
                
                //fetch event tag_list and unit_list
                for($i = 0; $i < $eventLength; $i++) {
                    $id = $events[$i];
                    $eid = $id['eid']; //get each event id
                    
                    $event[$i]['eid'] = $id['eid'];
                    $event[$i]['tid'] = $id['tid'];
                    $event[$i]['tcode'] = $id['tcode'];
                    $event[$i]['tname'] = $id['tname'];
                    $event[$i]['hall'] = $id['hall'];
                    $event[$i]['title'] = $id['ename'];
                    $event[$i]['desc'] = $id['desc'];
                    $event[$i]['url'] = $id['url'];
                    $event[$i]['use_cap'] = $id['use_cap'];
                    $event[$i]['date'] = $id['event_date'].' '.$id['event_time'];

                    //fetch each event tag_list i.e categories genre
                    $tags = $DAL->event_tag_list($eid);   
                    $event[$i]['tags'] = $tags;
                                        
                    //fetch each event unit list i.e ticket pricing
                    $units = $DAL->event_unit_list($eid);  
                    $event[$i]['unit_list'] = $units;                             
                }
                                   
                $eventData[] = $tag_list;
                $eventData[] = $unit_list;
                
                echo $result = json_encode($event);
            }
		break;
		
		default:
			# code...
			break;
	}

   //echo $result;
 
?>