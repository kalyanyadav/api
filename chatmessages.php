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
	include("fcmpush.php");
	$chatroom = $request->chatroom;
	$posted_by = $request->sender_id;
	$message = $request->message;
	$file_path = $request->file_path;
	$other_id = $request->other_id;
	$file_type = $request->file_type;
	$posted_time = date("d-m-Y H:i:s");
	$title = "New Chat Message";
	$push_message = $message;
	$addchat = "insert into chatmessages (chatroom,posted_by,message,file_path,posted_time,file_type,other_id) values ('$chatroom','$posted_by','$message','$file_path','$posted_time','$file_type','$other_id')";
	$res = mysqli_query($conn,$addchat);
	if($res){
	    $outp=1;
	    
	        
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Chat Mesage Sent Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->sender_id;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );

/* Transaction Log Code End  */
	    /*$fcmid ="select device_id from users where id='$other_id' ";
	    $resid = mysqli_query($conn,$fcmid);
	    if($resid){
	        while($row = mysqli_fetch_assoc($resid)){
	            $id[]=$row["device_id"];
	        }
	    }
	    fcm($push_message,$id); */
	}
	else{
	    $outp=$addchat;
	    
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = $addchat;
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->sender_id;
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );

	}
	$outp=json_encode($outp);
	echo $outp;
	
	?>