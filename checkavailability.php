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
	    $franchise_type = $request->franchise_type;
	    $country_id = $request->country_id;
	    $state_id = $request->state_id;
	    if($franchise_type==1)
	    {
	        $frcode = 'CF-'.$country_id;
	        $chkemail="SELECT * FROM `franchise_users` WHERE frcode='$frcode'";
	    }
	    else
	    {
	        $frcode = 'SF-'.$country_id.'-'.$state_id;
	        $chkemail="SELECT * FROM `franchise_users` WHERE frcode='$frcode'";
	    }
	    $chkres = mysqli_query($conn,$chkemail);
	    $count = mysqli_num_rows($chkres);
	    if($count>0){
	        $outp=1;
	        
	        	    

	    }
	    else{
	        $outp=0;
	    }
		$outp = json_encode($outp);
		echo($outp);
	    $conn->close();
?> 