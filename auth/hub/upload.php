<?php
//USER SELECT ACTION HUB
require '../dal/eventDal.php';

  $DAL = new EVENT_DAL();
   
    if(isset($_FILES["file"]["type"])) {
        $validextensions = array("jpeg", "jpg", "png");
        $temporary = explode(".", $_FILES["file"]["name"]);
        $file_extension = end($temporary);        
        $matric = $_POST['uid'];
        $newfilename = $matric .".".$file_extension;
        
        if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")) && ($_FILES["file"]["size"] <= 2000000) && in_array($file_extension, $validextensions)) {
            if ($_FILES["file"]["error"] > 0)
            {
            echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
            }
            else
            {
              if (file_exists("../../uploads/" . $newfilename)) {
                echo "Image exist.";
                $sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
                $targetPath = "../../uploads/".$newfilename; // Target path where file is to be stored
                move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
              }
              else
              {
                $sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
                $targetPath = "../../uploads/".$newfilename; // Target path where file is to be stored
                move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
                echo "Image successfully uploaded.";
                try {
                $data = $DAL->update_eventImage($_POST['user_id'], $newfilename);
                  if(!empty($data)){
                    echo $result = $data;
                  }else{
                      throw new Exception("FAIL", 1);        
                  }   
                } catch (Exception $e) {
                  echo $result = $e->getMessage();               
                }
                                
              }
            }
        }
        else
        {
        echo "Invalid file Size or Type ".$_FILES["file"]["type"]." ".$_FILES["file"]["size"];
        }
    }else{
      echo "No file selected";
    }
   //echo $result;
 
?>