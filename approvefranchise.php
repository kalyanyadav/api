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
	$id = $request->id;
	$userid = $request->user_id;
	$frtid = $request->frt_id;
	$commission =$request->commission;
	$status = "1";
    	$sql="update franchise_request set status='$status',commission='$commission' where id='$id'";
    	//echo $sql;
		$res = mysqli_query($conn,$sql);
			if($res){
               //$outp="Subscription Package Added Sucessfully";
                 $upd="update users set isfranchise='1', franchise_type_id = '$frtid' where id='$userid'";
                 //echo $upd;
                 $result = mysqli_query($conn,$upd);
		        $outp=1;
		        
 /* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Franchise Approved Sucefully - ".$frtid;
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->user_id;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
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
/* Transaction Log Code End  */
			}
	$outp = json_encode($outp);
	echo $outp;
	
	$conn->close();
?>
