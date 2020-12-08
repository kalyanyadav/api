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
	//include("fcmpush.php");
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require '../PHPMailer/src/Exception.php';
	require '../PHPMailer/src/PHPMailer.php';
	//require '../PHPMailer/src/PHPMailerAutoload.php';
	require '../PHPMailer/src/SMTP.php';
	
	$txn_date = date("Y-m-d");
	$user_id = $request->user_id;
	$chapter_id =$request->chapter_id;
	$lead_id = $request->lead_id;
	$reference_id = $request->leadref_id;
	$credit_s = $request->credits;
	$todaysdate = date("Y-m-d H:i:00");
	
	//$user_id = $_GET['user_id'];
	//$chapter_id = $_GET['chapter_id'];
    //$lead_id = $_GET['lead_id'];
	   	
	$chekchapter = "select * from subscription_chapter where user_id='$user_id' and chapter_id='$chapter_id'";
	$rescheck = mysqli_query($conn,$chekchapter);
	if(mysqli_num_rows($rescheck)>=0){
	        $chkw = "select * from wallet where user_id='$user_id'";
	        $resw = mysqli_query($conn,$chkw);
	        if($resw){
	            while($row= mysqli_fetch_assoc($resw)){
	                $credits = $row["credits"];
	            }
	            if($credits<=0){
	                $outp="insufficient Credits";
					
					//Mail Sending for insufficient credits
					$get_user = "select email,country_id from users where id='$user_id'";
					$res_get_user = mysqli_query($conn,$get_user);
					$row_get_user = mysqli_fetch_assoc($res_get_user);
					$to_email = $row_get_user['email'];
					$country = $row_get_user['country_id'];
				
					$email = 'noreply@eximbni.com';
	                $password = '@team&1234';
					$to_email = $to_email;
					$message = "Your credits are insufficient to buy this ";
					$subject = "Insufficient Credits";
						
					$mail = new PHPMailer(); // create a new object
					$mail->IsSMTP(); // enable SMTP
					$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
					$mail->SMTPAuth = true; // authentication enabled
					$mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
					$mail->Host = "mail.eximbni.com";
					$mail->Port = 587; // or 587
					$mail->IsHTML(true);
					$mail->Username = $email;
					$mail->Password = $password;
					$mail->SetFrom($email);
					$mail->Subject = $subject;
					$mail->Body = $message;
					$mail->AddAddress($to_email);
					$mail->Send();
					//Mail Sending for insufficient credits
	            }
	            else{
	            $bcredits = ($credits) - ($credit_s);
	           $updw = "update wallet set credits='$bcredits' where user_id='$user_id'";
	           $resupd = mysqli_query($conn,$updw);
	            if($resupd){
	                
	                //Mail Sending for lead purchase
					$get_user = "select email,country_id from users where id='$user_id'";
					$res_get_user = mysqli_query($conn,$get_user);
					$row_get_user = mysqli_fetch_assoc($res_get_user);
					$to_email = $row_get_user['email'];
					$country = $row_get_user['country_id'];
				
					$email = 'info@eximbin.com';
	                $password = 'EximBni.2020';
					$to_email = $to_email;
					$message = "Your have successfully purchased a lead with Lead Reference ID :- $reference_id";
					$subject = "Lead Purchase Successfully";
						
					$mail = new PHPMailer(); // create a new object
					$mail->IsSMTP(); // enable SMTP
					$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
					$mail->SMTPAuth = true; // authentication enabled
					$mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
					$mail->Host = "mail.eximbin.com";
					$mail->Port = 587; // or 587
					$mail->IsHTML(true);
					$mail->Username = $email;
					$mail->Password = $password;
					$mail->SetFrom($email);
					$mail->Subject = $subject;
					$mail->Body = $message;
					$mail->AddAddress($to_email);
					$mail->Send();
					//Mail Sending for lead purchase
					
					$purlead ="insert into purchased_leads(user_id,lead_id,reference_id) values ('$user_id', '$lead_id','$reference_id')";
					$respur = mysqli_query($conn,$purlead);
					
	    
        /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = $transaction_desc = "Lead Purchased Sucesfully, With Reference ID".$reference_id;
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
	                
	                $getcre= "select * from wallet where user_id='$user_id'";
	                $resget = mysqli_query($conn,$getcre);
	                
	                if($resget){
				
	                while($row= mysqli_fetch_assoc($resget)){
	                $outp[] = $row;
	            }
	                }
					
					//Add transaction
					$txn_id = rand(00000000,99999999);
					$admin_income = "INSERT INTO `admin_income`(`user_id`, `txn_amount`, `txn_type`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$user_id','$credit_s','credits','lead_purchase','$txn_id','$txn_date','1')";
					$res_admin_income = mysqli_query($conn, $admin_income);	
					if($res_admin_income)
					{
						$message= "Admin Income Inserted";
					}			
					else
					{
						$message= "Not inserted";
					
					}
					
					
					$add_txn = "INSERT INTO `txn_table`(`user_id`, `txn_type`, `txn_amount`, `txn_for`, `txn_id`, `txn_date`, `status`,lead_ref_id) VALUES ('$user_id','debits','$credit_s','lead_purchase','$txn_id','$txn_date','1','$reference_id')";
					$res_add_txn = mysqli_query($conn, $add_txn);	
					if($res_add_txn)
					{
						$message= "Transaction Inserted";
					}			
					else
					{
						$message= "Transaction Not inserted";
						
					}
					//Add transaction
					
					$get_lead = "select lead_type from leads where id='$lead_id'";
					$res_get_lead = mysqli_query($conn,$get_lead);
					$row_get_lead = mysqli_fetch_assoc($res_get_lead);
					$lead_type = $row_get_lead['lead_type'];
					
					$message = "You have purchased $lead_type lead with Lead ID: $reference_id.";
					$ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `lead_id`, `lead_type`, `created`) VALUES ('$user_id', '$country', 'Lead Purchase Successfully.', '$message', '1', '$lead_id', '$lead_type', '$todaysdate')";
            		$result_inbox = mysqli_query($conn,$ins_inbox);
            		
            		$ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `lead_id`, `lead_type`, `created`) VALUES ('$user_id', '$country', 'Lead Purchase Successfully(Email).', '$message', '1', '$lead_id', '$lead_type', '$todaysdate')";
            		$result_inbox = mysqli_query($conn,$ins_inbox);
            		
            		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Lead Purchase Successfully.', '$message', '1', '$todaysdate')";
            		$result_unotify = mysqli_query($conn,$ins_unotify);
					
					//fcm notification
					$title="Lead Purchase Successfully.";
					$push_message = "You successfully buy lead with Lead Reference ID:- $reference_id";
            		$fcmid ="select device_id from users where id='$user_id' ";
            	    $resid = mysqli_query($conn,$fcmid);
            	    if($resid){
            	        while($row = mysqli_fetch_assoc($resid)){
            	            $id[]=$row["device_id"];
            	        }
            	    }
        
            	    //fcm($push_message,$id,$title);
	            }
	        }        
	}
     
       
		else{
			$outp="0";
			

 /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed To Purchase Lead  With Reference ID".$reference_id;
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
		}
	}
		
		$outp = json_encode($outp);

		
		echo($outp);
			$conn->close();
	
?>
	
	
	
