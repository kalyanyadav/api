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
	$user_id = $_GET['user_id'];
	$status = $_GET['status'];
	$meeting_id = $_GET['webinar_id'];
	
	
	
        $sql = "UPDATE webinar SET status='$status' WHERE id='$meeting_id' AND posted_by='$user_id' ";
		$result =mysqli_query($conn,$sql);
		if($result){
		   $outp=1;
		}
		else{
		    $outp=0;
		}
		
	
		echo($outp);

	$conn->close();	
	
?>