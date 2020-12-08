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
		$lead_id =$request->lead_id;
		$leadref_id = $request->leadref_id;
		$lead_posted_by = $request->lead_posted_by;
		$response_posted_by = $request->response_posted_by;
		//$req_type = $request->req_type;
		$response_quantity = $request->response_quantity;
		$uom = $request->uom;
		$product_id = $request->product_id;
		$description = $request->description;
		$costperunit = $request->costperunit;
		$totalcost = $request->totalcost;
		$title = "New Reponse Received ";
		$message = "You have received a new response for your Lead";
		
		// To protect MySQL injection for Security purpose
		$insertresponse="INSERT INTO `responses` (`lead_id`, `lead_posted_by`, `response_posted_by`,  `response_quantity`, `uom`, `description`,`leadref_id`,`product_id`, costperunit, totalcost) VALUES ('$lead_id', '$lead_posted_by', '$response_posted_by', '$response_quantity', '$uom', '$description','$leadref_id','$product_id','$costperunit','$totalcost')";
	    $chkresponse = mysqli_query($conn,$insertresponse);
	    if($chkresponse){
			//FCM Starts
			$sql1 = "select * from users where id='$lead_posted_by'";
						$res1 = mysqli_query($conn,$sql1);
						$count = mysqli_num_rows($res1);
							if($count >0){
							while ($row = mysqli_fetch_assoc($res1)) {
								$id[]=$row['device_id']; 
							}
							
							}
							//print_r($id);
                               	fcm($message,$id,$title);
			
			// FCM Ends;
	        $outp=1;
	        
	        /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to Post Buy Lead Requirement";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $lead_posted_by;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
	    }
	    else{
			$outp=0;
			
			/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to Post Buy Lead Requirement";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $lead_posted_by;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
	    }
        $outp = json_encode($outp);
		
		echo($outp);
	
		
		$conn->close();
?> 