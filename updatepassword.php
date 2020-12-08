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
	    $password = md5($password);
	    $oldpassword =$request->oldpassword;
	    $oldpassword  = md5($oldpassword);	
	    //$otp = "7426";
	    
			$chekmobile = "select * from users where mobile='$mobile'";
        	$reschk = mysqli_query($conn,$chekmobile);
	       if(mysqli_num_rows($reschk)>0){
			   $rowchk = mysqli_fetch_array($reschk);
	           $passworddb = $rowchk['password'];
			   if($oldpassword==$passworddb)
			   {
					$updpwd = "update users set password='$password' where mobile='$mobile'";
					$updres = mysqli_query($conn,$updpwd);
					
					$outp=1;
					
       /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Updated Password";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $mobile;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
			   }
				else{
					$outp = 3;
				}
	    }
	    else{
	        
	        $outp = "2";
	        
	        /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to update Password ";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $mobile;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 