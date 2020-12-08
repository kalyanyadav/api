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
    
   
    
    $user_id = $_GET['user_id'];
	include("config.php"); 
        $sql = "SELECT * FROM wallet where user_id='$user_id'";
		$result = mysqli_query($conn,$sql);

        while ($row = mysqli_fetch_assoc($result)) 
        {
        //print_r ($row);
         $arr[]= $row; 
         
         /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "User  Credits";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
        }
    $outp= json_encode($arr);
		
		echo $outp;
		
	$conn->close();	

	
?> 