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
	    $password =$request->password;
	    $password = md5($password);  // Date 29-Nov-2019 Changed by Ganesh because Change Password is not working
	    $todaysdate = date("Y-m-d H:i:00");
	    //$otp = "7426";
	    
			$chekmobile = "select * from users where mobile='$mobile'";
        	$reschk = mysqli_query($conn,$chekmobile);
	       if(mysqli_num_rows($reschk)>0){
	           
	       while($rows = mysqli_fetch_array($reschk))
        	{
        	    $user_id = $rows['id'];
        	    $country = $rows['country'];
        	}
	        
	       $message = "Your password updated Successfully";
	       $ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Password updated Successfully.', '$message', '1', '$todaysdate')";
		   $result_inbox = mysqli_query($conn,$ins_inbox);
		
		   $ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Password updated Successfuly.', '$message', '1', '$todaysdate')";
		   $result_unotify = mysqli_query($conn,$ins_unotify);    
	           
	        $updpwd = "update users set password='$password' where mobile='$mobile'";
	        $updres = mysqli_query($conn,$updpwd);
	        
	        $outp=1;
	        
	            
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Password Updated Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->mobile;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
	        
	        
	    }
	    else{
	        
	        $outp = "2";
	        
	            
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to update Password";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->mobile;
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 