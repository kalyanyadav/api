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
	$user_id=$_GET['user_id'];
    $sql = "SELECT s.plan_type FROM subscriptions as s, wallet as w where s.id = w.subscription_id AND w.user_id = '$user_id' group by w.user_id";
      $result=mysqli_query($conn,$sql);
      if($result){
       while ($row = mysqli_fetch_assoc($result)) {
         $outp = $row['plan_type']; 
         
         
         /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "User Plan Package ";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
        }
        //$outp= json_encode($arr);
      }
		else{
			$outp="0";
			
			/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "failed to Get user Package";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
		}
		
	$conn->close();
		
		echo($outp);
	
?> 