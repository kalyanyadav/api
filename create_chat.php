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
	include("fcmpush.php");
    $user_id = $request->user_id;
    $other_id = $request->other_id;
    $chatroom = $user_id.$other_id;
    $rchatroom = $other_id.$user_id;
    
    
    $chkchatroom = "select * from chatrooms where chatroom='$chatroom' OR chatroom='$rchatroom' ";
    $res = mysqli_query($conn,$chkchatroom);
    if(mysqli_num_rows($res)>0){
        $outp = "Room already created";
        
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = $outp;
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->sender_id;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */

    }
    else{
        $create = "insert into chatrooms (chatroom,user_id,created_by,status) values ('$chatroom','$user_id','$user_id','1')";
        $rescreate = mysqli_query($conn,$create);
        if($rescreate){
            $create2 = "insert into chatrooms (chatroom,user_id,created_by,status) values ('$chatroom','$other_id','$user_id','1')";
        $rescreate2 = mysqli_query($conn,$create2);
        if($rescreate2){
            $outp=1;
            
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Chat Room Created Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->sender_id;
$transaction_status = $outp;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
            
        $getuser = "select * from user where id='$user_id'";
        $resuser = mysqli_query($conn,$getuser);
        if($resuser){
            while($rows= mysqli_fetch_assoc($resuser)){
                $country = $rows["country"];
            }  
        }
        
        $getother = "select * from user where id='$other_id'";
        $resother = mysqli_query($conn,$getother);
        if($resother){
            while($rowsother= mysqli_fetch_assoc($resother)){
                $othername = $rowsother["name"];
            }  
        }
        
        $getuser = "select * from user where id='$user_id'";
        $resuser = mysqli_query($conn,$getuser);
        if($resuser){
            while($rowsuser= mysqli_fetch_assoc($resuser)){
                $username = $rowsuser["name"];
            }  
        }
            
            $message = "You have created Chat with" .$othername.""; 
            $ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Chat Created Successfully.', '$message', '1', '$todaysdate')";
    		$result_inbox = mysqli_query($conn,$ins_inbox);
    		
    		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Chat Created Successfully.', '$message', '1', '$todaysdate')";
    		$result_unotify = mysqli_query($conn,$ins_unotify);
    		
    		$pushmessage = $message;
    		$title ="New Chat Created";
    		$fcmid ="select * from users where id='$other_id' and device_id !='' and is_online !='1'";
            $resid = mysqli_query($conn,$fcmid);
            if($resid){
                while($furow = mysqli_fetch_assoc($resid)){
                    $id[]=$furow["device_id"];
                }
            }
            fcm($push_message,$id,$title);
            
            $pushmessage = "You have created Chat with" .$username."";
    		$title ="New Chat Created";
    		$fcmid ="select * from users where id='$user_id' and device_id !='' and is_online !='1'";
            $resid = mysqli_query($conn,$fcmid);
            if($resid){
                while($furow = mysqli_fetch_assoc($resid)){
                    $id[]=$furow["device_id"];
                }
            }
            fcm($push_message,$id,$title);
        }
        }
            else{
                $outp = 0;
                
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to create Chat Room";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->sender_id;
$$transaction_status = $outp;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
            }
        }
    
        
	
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>
