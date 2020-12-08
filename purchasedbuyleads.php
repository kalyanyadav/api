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
	//$lead_id = $request->lead_id;
	
	//$country_id = $_GET['country_id'];
		$user_id = $_GET['user_id'];
        $sql="select * from purchased_leads where user_id ='$user_id' and status=1";
		
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
            while ($row = mysqli_fetch_array($result)) {
            $leadid[] = $row['lead_id'];
			//echo json_encode($leadid);
			
			}
				foreach($leadid as $key){
					$getleads = "select l.*, u.uom from leads l, uoms u where l.id = '$key' and l.uom_id = u.id and l.lead_type='Buy'";
				
					$getres = mysqli_query($conn,$getleads);
					if($getres){
						while($rows=mysqli_fetch_assoc($getres)){
							$outp[]=$rows;
							
/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Purchased Buy Leads";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
        /* Transaction Log Code End  */ 
        
						}
					}
					
				}
		}
		else{
			$outp=0;
			
/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to fetch Purchased Buy Leads";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
		}
		
	$outp = json_encode($outp,JSON_INVALID_UTF8_IGNORE);
	echo $outp;
	$conn->close();

?>
