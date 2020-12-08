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
	
		$date = date('Y-m-d h:i:s');
	    $user_id = $request->user_id;
		$country_id = $request->country_id;
		$title = $request->title;
		$notification = $request->notification;
		$type = $request->type;
	    
	    $query="INSERT INTO `user_notification`(`user_id`, `country_id`, `title`, `notification`, `type`, `created`, `readstatus`, `status`) VALUES ('$user_id','$country_id','$title','$notification','$type','$date','0','1')";
		//echo $query;
		$result = mysqli_query($conn,$query);
		if($result){
			$outp=1;
		}
		else{
			$outp=0;
		}
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 