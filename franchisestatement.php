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
	$franchise_id = $_GET["user_id"];
	$from_date  = $_GET["from_date"];
	$to_date = $_GET["to_date"];
	$get_statement = "select i.* , e.* from franchise_accounts i , franchise_expenses e where i.franchise_id='$franchise_id' OR e.franchise_id='$franchise_id'";
	$res_get =mysqli_query($conn,$get_statement);
	if($res_get){
		while($row = mysqli_fetch_assoc($res_get)){
			$outp[]=$row;
			
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Franchise Statement  listed Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $_GET["user_id"];
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
$transaction_desc = "Failed to list Franchise Statement";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $_GET["user_id"];
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
	}
        $outp = json_encode($outp);
	    echo $outp;
	
	$conn->close();
	
?>