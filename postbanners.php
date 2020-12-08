<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
 
        exit(0);
    }
 	
	$postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

	include("config.php"); 
		/* Getting file name */
$filename = $_FILES['file']['name'];

/* Location */
$location = 'uploads/';

/* Upload file */
move_uploaded_file($_FILES['file']['tmp_name'],$location.$filename);
		//$banner_image=$filename;
		$banner_image = $request->banner_image;
		$chapter_id = $request->chapter_id;
	    $category_id = $request->category_id;
	    $posted_by = $request->posted_by;
	    $country = $request->country;
	    $user_country = $request->user_country;
	    
	    $todaysdate = date('Y-m-d H:i:00');
	    
	    $query="INSERT INTO banner (posted_by,banner_image,category_id,chapter_id,status) values ('$posted_by','$banner_image','$category_id','$chapter_id','0')";
	    $result = mysqli_query($conn,$query);
		if($result){
		    
		    
		    $last_id = mysqli_insert_id($conn);
		    
		    if($country !='null'){
		        
		        foreach($country as $key => $value ){
		            
		            $country_id[$key] = $value->country_id; 
		        }
		        
		        foreach($country_id as $val){
		            $portsql = "INSERT INTO banner_display_countries (banner_id,countries,chapter_id) VALUES('$last_id','$val','$chapter_id')";
		            $resport = mysqli_query($conn, $portsql);
		            
		        }
		        
		    }		    
		    		  $outp=1;
		  
		    $chkemail="select country_id from users where id='$posted_by'";
    	    $chkresmail = mysqli_query($conn,$chkemail);
    	    $row = mysqli_fetch_array($chkresmail);
    	    $country = $row['country_id'];
		  
		    $message = "Your Banner Posted Successfully";
		    
		    $ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Banner Posted Successfully.', '$message', '1', '$todaysdate')";
    		$result_inbox = mysqli_query($conn,$ins_inbox);
    		
    		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Banner Posted Successfully', '$message', '1', '$todaysdate')";
    		$result_unotify = mysqli_query($conn,$ins_unotify);
		}
		else{
			$outp=$query;
		}
		$outp = json_encode($outp);
		
		echo($outp);
	
	$conn->close();
	
?> 