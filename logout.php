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
 	
    require_once('transactions.php');
    $transaction_name = basename($_SERVER['PHP_SELF']); 
    $transaction_desc = "User Logged Out";
    $transaction_source = $_SERVER['HTTP_USER_AGENT'];
    $transaction_user = $request->email;
    log_function($transaction_name, $transaction_desc, $transaction_source,$transaction_user);
 	
	

	include("config.php"); 
	
		$email=$request->email;
	
		    $last_active= date('Y-m-d H:i:s');
		    $upduser = "update users set last_active='$last_active', is_online=0 where email='$email'";
		    $resupd = mysqli_query($conn,$upduser);
		    if($resupd){
		    $outp=1;
		}
		else{
			$outp=0;
		}
	    
        $outp = json_encode($outp);
		
		echo($outp);
	

		$conn->close();
?> 