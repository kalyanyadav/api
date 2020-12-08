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
	  
	    $mobile =$request->mobile;
	    $otp =$request->otp;
	    
	    //$otp = "7426";
	    
		$chkotp="select * from otp where mobile='$mobile' and otp = '$otp' and status = 1";
        $chkres = mysqli_query($conn,$chkotp);
	    
	    if(mysqli_num_rows($chkres)>0){
	        
	        $updotp = "delete from otp WHERE mobile = '$mobile' and otp ='$otp'";
	        $updres = mysqli_query($conn,$updotp);
	        
	        $outp=1;
	        
	        
	    }
	    else{
	        
	        $outp = "Invalid OTP";
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 