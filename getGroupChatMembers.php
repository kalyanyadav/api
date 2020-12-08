<?php 
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache for 1 day
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
	$chatroom = $_GET["chatroom"];
	$user_id = $_GET["user_id"];
	
	   //$sql = "SELECT gc.*,u.name, u.business_name FROM group_chatrooms gc, users u WHERE gc.user_id= u,id AND chatroom ='Group No 1' AND status='1' AND gc.user_id !='$user_id' GROUP BY user_id";
	   $sql = "SELECT gc.*,u.name, u.business_name FROM group_chatrooms gc, users u WHERE gc.user_id= u.id AND gc.chatroom ='$chatroom' AND gc.status='1' GROUP BY gc.user_id";
	   
	   $res = mysqli_query($conn,$sql);
	    if(mysqli_num_rows($res)>0){
	        while($row=mysqli_fetch_assoc($res)){
	            $outp[]=$row;

/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Grouo Chat Member Listed Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $user_id;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */

	        }
 
	    }else{
	        $outp = 0;
	        
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to Fetch Group Chat Members";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $user_id;
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
	        
	    }
        
	
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>