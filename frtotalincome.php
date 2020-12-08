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
	$mobile=$_GET["mobile"];

		$sql = "select * from franchise_users where mobile = '$mobile'";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
			while ($row = mysqli_fetch_assoc($result)) {
			$franchise_id[] = $row['id'];
				
			}
			//print_r ($row);
		}	
			foreach($franchise_id as $key){
			$getleads = "select sum(amount) as amount from frachise_accounts where franchise_id='$key'";
			$getres = mysqli_query($conn,$getleads);
			if($getres){
				while($rows=mysqli_fetch_assoc($getres)){
					$outp=$rows['amount'];
					
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Franchise Income  listed Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $_GET["mobile"];
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
					}
				}
			
		
		else{
			$outp="0";

/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to fetch Franchise Income";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $_GET["mobile"];
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
		}
		}
		
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>