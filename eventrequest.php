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
	$franchise_id = $request->franchise_id;
	$request_id = $request->request_id;
	$event_name = $request->event_name;
	$event_date = $request->event_date;
	$budget=$request->budget;
	$participents = $request->participents;
	$guest=$request->guest;
	$venue = $request->venue;
	$conversions = $request->conversions;
	$status = 0;
	
	$sql ="insert into event_request (franchise_id, event_name,request_id,event_date,budget,participents,guest,venue,conversions,status) values('$franchise_id','$event_name','$request_id','$event_date','$budget','$participents','$guest','$venue','$conversions','$status')";
	$res = mysqli_query($conn,$sql);
	if($res){
	    $outp=1;
	    
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Event Request Succesful";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->user_id;
$transaction_status = $outp;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
	}
	else{
	    $outp=$sql;
	    
	    	    
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = $sql;
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->user_id;
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
	}
	
	$outp= json_encode($outp);
	echo $outp;
	$conn->close();
	?>