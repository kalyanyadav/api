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
	$user_id = $request->user_id;
	$title = $request->title;
	$description=$request->description;
	$webdate = $request->webdate;
	$webtime = $request->webtime;
	$duration = $request->duration;
	$todaysdate = date('Y-m-d H:i:00');
	$meeting_id = "MiiVision_".rand(111111,999999);
	$meeting_pass = rand(111111,999999);
	$webinar_datetime = $webdate." ".$webtime.":00";
	$webinar_link = 'https://eximbni.com:444/'.$meeting_id;
	
        $sql = "insert into webinar (title,description,webinar_date,webinar_time,posted_by,webinar_datetime,duration,status,meeting_id,meeting_pass,meeting_duration,webinar_link) values('$title','$description','$webdate','$webtime','$user_id','$webinar_datetime','$duration','0','$meeting_id','$meeting_pass','$duration','$webinar_link')";
		$result =mysqli_query($conn,$sql);
		if($result){
		    
		    $chkemail="select * from users where id='$user_id'";
    	    $chkresmail = mysqli_query($conn,$chkemail);
    	    $row = mysqli_fetch_array($chkresmail);
    	    $country = $row['country'];
    	    
    	    $message = "Your Webinar Created Successfully";
		    
		    $ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Webinar Created Successfully.', '$message', '1', '$todaysdate')";
    		$result_inbox = mysqli_query($conn,$ins_inbox);
    		
    		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Webinar Created Successfully.', '$message', '1', '$todaysdate')";
    		$result_unotify = mysqli_query($conn,$ins_unotify);
    		
			$outp=1;
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Webinar Created Sucesfullt";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->user_id;
$transaction_status = $outp;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
		}
		else{
			$outp=0;
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to Create Webinar";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->user_id;
$transaction_status = $outp;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
		    
		}
		
	
		echo($outp);

	$conn->close();	
	
?>