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
	  use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
//require 'PHPMailer/src/PHPMailerAutoload.php';
require '../PHPMailer/src/SMTP.php';
	    $mobile =$request->mobile;
	    $otp =$request->otp;
	    $leadref_id = $request->leadref_id;
	    $impexpmobile = $request->impexpmobile;
	    $impexpotp = $request->impexpotp;
	    $getemail = "select email from users where mobile='$mobile'";
	    $resmail = mysqli_query($conn,$getmail);
	    if($resmail){
	        while($mrow= mysqli_fetch_assoc($resmail)){
	            $to_email = $mrow['email'];
	        }
	    }
	    
	    $chkotp="select * from otp where mobile='$mobile' and otp = '$otp' and status = 1";
        $chkres = mysqli_query($conn,$chkotp);
	    $count = mysqli_num_rows($chkres);
	    if($count==1){
	        
	        if($impexpmobile){
	            
        	    $chkotp1="select * from otp where mobile='$impexpmobile' and otp = '$impexpotp' and status = 1";
                $chkres1 = mysqli_query($conn,$chkotp1);
        	    $count1 = mysqli_num_rows($chkres1);
        	    if($count1==1){
        	        
        	        $upd_leads = "UPDATE `leads` SET status='2' WHERE `leadref_id` = '$leadref_id' and status ='0'";
            	    $qry_leads = mysqli_query($conn,$upd_leads);        	        
        	       $updotp1 = "delete from `otp` WHERE `mobile` = '$impexpmobile' and otp ='$impexpotp'";
    	            $updres1 = mysqli_query($conn,$updotp1);
    	            
    	            $updotp = "delete from `otp` WHERE `mobile` = '$mobile' and otp ='$otp'";
    	            $updres = mysqli_query($conn,$updotp);
        	        
        	        $outp=1;
    $email = 'noreply@eximbni.com';
    $password = '@team&1234';
    $to_email = $to_email;
    $message = "You Posted Lead with Referance ID: " . $leadref_id . " is under admin verification. once been verified you requirement will be visble to public. A confirmation mail will be sent to you with in 72 hours.";
    $subject = "Thank You For Posting Lead";

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
        	    }
        	    else{
        	        $outp="OTP Sent to Importer/ Exporter is not matching";
        	    }	            
	            
	        }
	        
	        else{
	            
        	        $upd_leads = "UPDATE `leads` SET status='2' WHERE `leadref_id` = '$leadref_id' and status ='0'";
            	    $qry_leads = mysqli_query($conn,$upd_leads);	            
	            
                    $updotp = "delete from `otp` WHERE `mobile` = '$mobile' and otp ='$otp'";
    	            $updres = mysqli_query($conn,$updotp);
	             $outp=1;
	  /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to post Lead";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $mobile;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */  
	             
	        }

             
	    }
	    else{
	        
	        $outp = "OTP Sent to you is not matching";
	        
	        /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Invalid OTP ENTRY";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */  
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 