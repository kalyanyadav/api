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
	
		$user_image=$request->user_image;
		$user_id = $request->user_id;
	    $todaysdate = date('Y-m-d H:i:00');
	    $query="update users set user_image='$user_image' where id=$user_id";
	    $result = mysqli_query($conn,$query);
		if($result){
		  $outp=1;
		  
		    $chkemail="select * from users where id='$user_id'";
    	    $chkresmail = mysqli_query($conn,$chkemail);
    	    $row = mysqli_fetch_array($chkresmail);
    	    $country = $row['country'];
		    $user_image = $row["user_image"];
		    $message = "Your Profile Image updated Successfully";
		    
		    $ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Profile Image Updated Successfully.', '$message', '1', '$todaysdate')";
    		$result_inbox = mysqli_query($conn,$ins_inbox);
    		
    		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Profile Image Updated Successfully.', '$message', '1', '$todaysdate')";
    		$result_unotify = mysqli_query($conn,$ins_unotify);
    		
    		/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "user Profile updated Sucesfully";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
		}
		else{
			$outp=0;
			
			
			/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "failed To Update user Profile Image";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
		}
		$outp = json_encode($user_image);
		
		echo($outp);
	
	$conn->close();
	
?> 