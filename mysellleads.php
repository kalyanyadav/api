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
	$u_id = $_GET['u_id'];
        $sql="select l.*, u.uom from leads l, uoms u  where l.uom_id=u.id and l.lead_type = 'Sell' and l.posted_by = '$u_id' and l.status = '1'";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count>0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $arr[] = $row; 
}
    $outp= json_encode($arr, JSON_INVALID_UTF8_IGNORE);
		
		
	/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "My Sell Leads";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $u_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
		}
		else{
			$outp="0";
			
			/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to fetch My Sell Leads";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $u_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
		}
		
	$conn->close();
		
		echo($outp);
	
?>