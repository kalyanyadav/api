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
	
	$otp = mt_rand(1111,9999);
	$mobile = $request->mobile;
	$otpmethod = 'email'; $request->otpmethod;
	$todaysdate = date("Y-m-d H:i:00");
		$msg = "Your EXIMBNI verification OTP code is ".$otp.". Please DO NOT share this OTP with anyone.";
		$msg = urlencode($msg);
	//$mobile=$_GET['mobile'];
	$chekmobile = "select * from users where mobile='$mobile'";
	$reschk = mysqli_query($conn,$chekmobile);
	if(mysqli_num_rows($reschk)>0){
	 $rows = mysqli_fetch_array($reschk);
        	$user_id = $rows['id'];
        	$country = $rows['country_id'];
        	$uemail = $rows['email'];

	    $delotp = "delete from otp where mobile = '$mobile'";
	    $redel =  mysqli_query($conn,$delotp);
	    
	    $otpins = "insert into otp (mobile, otp) values('$mobile','$otp')";
		$resotp =mysqli_query($conn,$otpins);
		
		if($otpmethod =='mobile'){

    		if($country=='101'){
            $otpurl = "http://prioritysms.tulsitainfotech.com/api/mt/SendSMS?user=eximbin&password=eximbin&senderid=EXIMBN&channel=Trans&DCS=0&flashsms=0&number=" . $mobile . "&text=" . $msg . "&route=15";
            $res    = file_get_contents($otpurl);
                
                if($res){
                  $outp=1;  
                  
/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "OTP Sent Sucesfully";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $mobile;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
                }else{
                    $outp=$otpurl;
                    
                    /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to send OTP";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $mobile;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
                }
    		     
    		}
            else{
                $otpurl = "https://global.datagenit.com/API/sms-api.php?auth=D!~3133g8mKCYNGU7&msisdn=" . $mobile . "&senderid=EXIMBNI&message=" . $msg . "&countrycode=" . $country;
                $res    = file_get_contents($otpurl);
                if($res){
                  $outp=1;  
                }else{
                    $outp=$otpurl;
                }
            
            }
		    
		}else{
      
            //email sending
            $email    = 'noreply@eximbni.com';
            $password = '@team&1234';
            $to_email = $uemail;
            $subject = "Forgot Password";
            $message = "Your EXIMBNI verification OTP code is ".$otp.". Please DO NOT share this OTP with anyone.";
            $mail = new PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug  = 0; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth   = true; // authentication enabled
            $mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host       = "mail.eximbni.com";
            $mail->Port       = 587; // or 587
            $mail->IsHTML(true);
            $mail->Username = $email;
            $mail->Password = $password;
            $mail->SetFrom($email);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AddAddress($to_email);
            $mail->Send();
        
            $outp=1;   
            
            /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "OTP Sent Sucesfully";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $mobile;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
		}  		

	}
	else{
	    $outp=	$chekmobile;
	    
	    /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to send OTP";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $mobile;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
	}
		
		$outp = json_encode($outp);
		echo $outp;
	
	$conn->close();
	
?> 