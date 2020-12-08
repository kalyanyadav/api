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
	$user_id= $_GET["user_id"];
	   $sql = "SELECT user_id FROM `chatrooms` WHERE `created_by` ='$user_id' AND `user_id` !='$user_id' GROUP BY user_id";
	   //$sql = "SELECT user_id FROM `chatrooms` WHERE `chatrooms`.`created_by` ='$user_id' GROUP BY user_id";
	   //echo $sql;
	    $res = mysqli_query($conn,$sql);
	    if(mysqli_num_rows($res)>0){
	        while($row=mysqli_fetch_assoc($res)){
	            $user_ids[]=$row["user_id"];
	        }
		//	print_r($user_ids);
	        foreach($user_ids as $val){
	            $getusers="select u.* from users u where u.id='$val'";
	             //echo "<br>".$getusers;
				$resusers = mysqli_query($conn,$getusers);
	            if($resusers){
	                while($gros=mysqli_fetch_assoc($resusers)){
	                    $outp[]=$gros;
	                    
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Group Chat Users Listed Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $user_id;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
	                    
	                }
	            }
	            else{
	                $outp=0;

/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Grouo Chat Member Listed Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $user_id;
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */

	            }
	        }
	    }
        
	
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>
