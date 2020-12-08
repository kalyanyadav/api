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
    $lead_id = $request->lead_id;
    $remind_date = $request->remind_date;

	if($user_id !="" && $lead_id !=""){

        $ressql = mysqli_query($conn, "SELECT count(*) as cnt FROM `lead_reminders` WHERE user_id='$user_id' AND lead_id='$lead_id'");
        $resqur = mysqli_fetch_array($ressql);
        $sqlcount = $resqur['cnt'];
        if($sqlcount > 0){
            
            $ressql1 = mysqli_query($conn, "UPDATE `lead_reminders` SET `remind_date` = '$remind_date' WHERE user_id='$user_id' AND lead_id='$lead_id'");
    		if($ressql1){
    			$outp=2;
    		}
    		else{
    			$outp=0;
    		}
            
        }else{

    	 	$query="INSERT INTO lead_reminders (user_id,lead_id,remind_date) values ('$user_id','$lead_id','$remind_date')";
    		$result = mysqli_query($conn,$query);
    		if($result){

                $ressql1 = mysqli_query($conn, "SELECT leadref_id FROM `leads` WHERE id='$lead_id'");
                $resqur1 = mysqli_fetch_array($ressql1);
                $leadref_id = $resqur1['leadref_id'];

                $message = "You are Successful reminded the $leadref_id for $remind_date this date.";
                
                $ins_inbox= "INSERT INTO `inbox` (`user_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', 'Reminder added for $leadref_id Successfully.', '$message', '1', '$today')";
                $result_inbox = mysqli_query($conn,$ins_inbox);
                
                $ins_unotify= "INSERT INTO `user_notification` (`user_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', 'Reminder added for $leadref_id Successfully.', '$message', '1', '$today')";
                $result_unotify = mysqli_query($conn,$ins_unotify);

    			$outp=1;
    		}
    		else{
    			$outp=$query;
    		}
        }
	}else{
		$outp=0;
	}

	$outp = json_encode($outp);
		
	echo($outp);
	
	$conn->close();
	
?> 