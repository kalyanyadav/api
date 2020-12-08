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
	    $chkotp="select * from otp where mobile='$mobile' and otp = '$otp' and status = 1";
        $chkres = mysqli_query($conn,$chkotp);
	    $count = mysqli_num_rows($chkres);
	    if($count == 1){
			
			$sql_userid = "select id from users where mobile='$mobile'";
			$res_userid = mysqli_query($conn,$sql_userid);
			$row_userid = mysqli_fetch_array($res_userid);
			$user_id = $row_userid['id'];
	        
	        $updlead = "UPDATE leads SET `status` = '2' WHERE posted_by = '$user_id' and status='0'";
	        $updleadres = mysqli_query($conn,$updlead);
	        
	        $updotp = "delete from `otp` WHERE `mobile` = '$mobile' and otp ='$otp'";
	        $updres = mysqli_query($conn,$updotp);
	        
	        $outp=1;
	        
	        
/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Lead OTP Validated Sucefully";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
	        
	        
	    }
	    else{
	        
	        $outp = "Invalid OTP";
	        /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = $outp;
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
	        
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 