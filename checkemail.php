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
	   //$email = 'kalyan@gmail.com';
	    $email = $_POST['email'];
		$chkemail="SELECT COUNT(*) FROM `franchise_users` WHERE country_id='101' and state_id='2'";
        $chkres = mysqli_query($conn,$chkemail);
	    $count = mysqli_num_rows($chkres);
	    if($count>0){
	        $outp="Email Exists";
	    }
	    else{
	        $outp = "Email Available";
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 