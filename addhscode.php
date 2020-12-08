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
	
		/* $user_id = 1; //$request->user_id;
		$country_id = 1; //$request->country_id;
		$category = "Test";//$requet->category;
		$chapter = "Test";//$requet->chapter;
		$product = "Test";//$requet->product;
		$description = "Test";//$requet->product_description; */
	
		$user_id = $request->user_id;
		$country_id = $request->country_id;
		$category = $request->category;
		$chapter = $request->chapter;
		$description = $request->productdescription;
		$moreproductdetails = $request->moreproductdetails;
	    
	    $query="insert into hscode_requests(user_id,country_id,category,chapter,product,description) values ('$user_id','$country_id','$category','$chapter','$description','$moreproductdetails')";
		//echo $query;
		$result = mysqli_query($conn,$query);
		if($result){
		    $outp=1; 
		    
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "User Added HSN Code";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->mobile;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */

		    //Get Country Franchise Email
		    $get_franch = "SELECT email FROM `franchise_users` WHERE country_id='$country_id' AND franchise_type='CF'";
		    $res_franch = mysqli_query($conn, $get_franch);
		    $row_franch = mysqli_fetch_assoc($res_franch);
		    $cnt_franch_email = $row_franch['email'];
		    
			//Get Email
				$get_user = "select email from users where id='$user_id'";
				$res_get_user = mysqli_query($conn,$get_user);
				$row_get_user = mysqli_fetch_assoc($res_get_user);
				$to_email = $row_get_user['email'];
			
			//email sending
				$email = 'info@eximbin.com';
	            $password = 'EximBni.2020';
				$to_email = $to_email;
				$message = "Thank You for submiting request to add HS Code. Our team will be contact you very soon.";
				$subject = "Your Request Submited Successfully.";
					
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
				$mail->AddCC($cnt_franch_email);
				// $mail->AddBCC($to_email);
				$mail->Send();
			//email sending
			
			
		}
		else{
		    $outp=$query;
		    
		    /* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to add HSN Code";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->mobile;
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
		}
	  
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 