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

        $user_id =$request->user_id;
	    $mpin =$request->mpin;
	    $todaysdate = date("Y-m-d H:i:00");
		$checkusr = "SELECT * FROM `users` WHERE id=$user_id";
    	$reschk = mysqli_query($conn,$checkusr);
    	$count = mysqli_num_rows($reschk);
        if(mysqli_num_rows($reschk)>0)
        {
            while($rows = mysqli_fetch_array($reschk))
        	{
        	    $country = $rows['country'];
        	}
	        
	       $message = "Your mpin updated Successfully";
	       $ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'MPIN updated Successfully.', '$message', '1', '$todaysdate')";
		   $result_inbox = mysqli_query($conn,$ins_inbox);
		
		   $ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'MPIN updated Successfuly.', '$message', '1', '$todaysdate')";
		   $result_unotify = mysqli_query($conn,$ins_unotify);    
	           
	        $updpwd = "update users set mpin='$mpin' where id='$user_id'";
	        $updres = mysqli_query($conn,$updpwd);
	        
	        $outp=1;
	        
	        /* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Mpin Created Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->user_id;
$transaction_status = $outp;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
	        
	        
	    }
	    else{
	        
	        $outp = 0;
	        
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to create Mpin";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->user_id;
$transaction_status = $outp;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
}
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 