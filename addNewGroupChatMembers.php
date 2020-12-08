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
    $user_id = $request->user_id;
    $other_id = $request->other_id;
    $chatroom = $request->group_name;


        foreach($other_id as $val){
        
        $create = "insert into group_chatrooms (chatroom,user_id,created_by,status) values ('$chatroom','$val','$user_id','1')";
        $rescreate = mysqli_query($conn,$create);
        if($rescreate){
            
              $outp=1;
              
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Group Created Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->mobile;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
              
               
            }
            else{
                $outp = 0;
            
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to Create Group";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->mobile;
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
            }
            
        }
        //$outp = $create;
	
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>
