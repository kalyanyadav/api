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
	//$lead_id = $request->lead_id;
	
	//$country_id = $_GET['country_id'];
        $today = date('Y-m-d');
		$user_id = $request->user_id;
		$lead_id = $request->lead_id;
        
        $sql = "insert into purchased_leads(user_id,lead_id,status) values ('$user_id', '$lead_id','2')";
        $res = mysqli_query($conn,$sql);
        if($res){

                $ressql1 = mysqli_query($conn, "SELECT leadref_id FROM `leads` WHERE id='$lead_id'");
                $resqur1 = mysqli_fetch_array($ressql1);
                $leadref_id = $resqur1['leadref_id'];

                $message = "You are Successful skiped the". $leadref_id. "from your lead list.";
                
                $ins_inbox= "INSERT INTO `inbox` (`user_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', 'Skiped $leadref_id Successful.', '$message', '1', '$today')";
                $result_inbox = mysqli_query($conn,$ins_inbox);
                
                $ins_unotify= "INSERT INTO `user_notification` (`user_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', 'Skiped $leadref_id  Successful.', '$message', '1', '$today')";
                $result_unotify = mysqli_query($conn,$ins_unotify);
                
/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = $message;
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 

            $outp=1;
        }
        else{
            $outp=0;
            
/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "You are Successful skiped the". $leadref_id. "from your lead list.";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
        }
		
	$outp = json_encode($outp);
	echo $outp;
	$conn->close();

?>
