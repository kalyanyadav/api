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
	
		$username = $_GET['email_id'];
	    $chkemail="select * from playstore_mail where email_id='$username' AND status='1'";
	    $chkres = mysqli_query($conn,$chkemail);
	    $count = mysqli_num_rows($chkres);
	    if($count==1){
	        
	        $updatesql = mysqli_query($conn,"UPDATE playstore_mail SET status='0' WHERE email_id='$username' ");

	        $outp=1;
	    }
	    else{
	        $outp = 0;
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 