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
	$user_id = $_GET["user_id"];
	
      //  $sql = "SELECT w.credits,s.chapters,s.plan_duration,u.id ,(select count(*) from chatrooms where created_by='$user_id' and user_id='$user_id')as chatusers,(select count(*) from leads where posted_by='$user_id')as posted_leads,(select count(*) from banner where posted_by='$user_id')as posted_banners,(select count(*) from webinar where posted_by='$user_id')as webinars from users u, subscriptions s, wallet w where w.user_id='$user_id' and u.subscription_id=s.id limit 1";
		
	  $sql ="SELECT w.credits,s.plan_name,s.chapters,s.rfq,s.plan_duration,u.id ,(select count(*) from chatrooms where created_by='$user_id' and user_id='$user_id')as chatusers,(select count(*) from leads where posted_by='$user_id')as posted_leads,(select count(*) from banner where posted_by='$user_id')as posted_banners,(select count(*) from webinar where posted_by='$user_id')as webinars, (SELECT count(*) FROM `purchased_leads` WHERE LEFT(reference_id,1) ='S' AND user_id = '$user_id' AND status=1) as purchase_sales , (SELECT count(*) FROM `purchased_leads` WHERE LEFT(reference_id,1) ='B' AND user_id = '$user_id' AND status=1) as purchase_buys , (select count(*) from rfq where posted_by='$user_id')as rfqcnt from users u, subscriptions s, wallet w where w.user_id='$user_id' and w.subscription_id=s.id limit 1";
		
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $arr[] = $row; 
 
        /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "User  Profile";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
}
    $outp= json_encode($arr);
		
		}
		else{
			$outp="0";
			
			/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to Fetch user Profile ";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
		}
		
	$conn->close();
		
		echo($outp);
	

	
?> 