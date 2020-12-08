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
		
		$user_id = $request->user_id;
	    $category_id = $request->category_id;
	    $chapter_id = $request->chapter_id;
	    $hsncode_id = $request->hsncode_id;
	    $uom_id = $request->uom_id;
	    $country = $request->country;
	    $description = $request->description;
	    $seller_id = $request->seller_id;
	    $target_price = $request->target_price;
	    $req_quantity = $request->req_quantity;
    	$mobile = $request->mobile;
	    $ref_id = $request->ref_id;
	    $todaysdate = date('Y-m-d H:i:00');
	    $txn_date = date("Y-m-d");
	    $txn_id = rand(00000000, 99999999);
	    
	    
	    $sql_wallet = "SELECT rfq_credits FROM wallet WHERE user_id = '$user_id'";
	    $qry_wallet = mysqli_query($conn,$sql_wallet);
	    $num_wallet = mysqli_num_rows($qry_wallet);
	    if($num_wallet > 0){
	       $res_wallet = mysqli_fetch_array($qry_wallet);
	       $rfq_credits = $res_wallet['rfq_credits'];
	       if($rfq_credits > 0 ){
	           
            $update_rfCredit = $rfq_credits - 1;
	    
	        $upt_wallet = "UPDATE wallet SET rfq_credits = '$update_rfCredit' WHERE user_id = '$user_id'";
	        $upt_qry = mysqli_query($conn,$upt_wallet);
	        
	    
        	    $query="INSERT INTO `rfq`(`posted_by`, `mobile`, `seller_id`, `category_id`, `chapter_id`, `hscode`, `description`, `target_price`, `req_quantity`, `req_unit`, `country`, `posted_date`, `ref_id`, `Status`) VALUES 
        	    ('$user_id','$mobile','$seller_id','$category_id','$chapter_id','$hsncode_id','$description','$target_price','$req_quantity','$uom_id','$country','$todaysdate','$ref_id','1')";
        	    $result = mysqli_query($conn,$query);
        		if($result){
        		    
        		    //Get Email of seller
        				$get_seller = "select email from users where id='$seller_id'";
        				$res_get_seller = mysqli_query($conn,$get_seller);
        				$row_get_seller = mysqli_fetch_assoc($res_get_seller);
        				$seller_email = $row_get_seller['email'];
        				
        			//Get Email 
        				$get_user = "select email from users where id='$user_id'";
        				$res_get_user = mysqli_query($conn,$get_user);
        				$row_get_user = mysqli_fetch_assoc($res_get_user);
        				$to_email = $row_get_user['email'];	
        		    
        		        $miios_email = "miioslimited@gmail.com";
        		        $murali_email = "muralimiios@gmail.com";
        		        
        		    //email sending
        				$email = 'info@eximbin.com';
        	            $password = 'EximBni.2020';
        				$to_email = $to_email;
        				$message = "Thank You for submitting  RFQ. Our team will contact you very soon for verification. Your Referance ID:- $ref_id";
                        $subject = "Your RFQ Submitted Successfully.";
        					
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
        				$mail->AddCC($miios_email);
        				$mail->AddBCC($murali_email);
        				$mail->Send();
        			//email sending
        			
        			//email sending to seller
        				$email = 'info@eximbin.com';
        	            $password = 'EximBni.2020';
        				$to_email = $to_email;
        				$message = "New Request for RFQ submited. RFQ Referance ID:- $ref_id";
                        $subject = "New RFQ Request.";
        					
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
        				$mail->AddAddress($seller_email);
        				//$mail->AddCC($miios_email);
        				//$mail->AddBCC($murali_email);
        				$mail->Send();
        			//email sending
        		    
        		    
        		    $outp=1;
        		    
        		         /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "RFQ posted Sucesfully";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
        		}
        		else{
        			$outp=$query;
        			
        			     /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to post RFQ";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
        		}
		
		$add_txn2 = "INSERT INTO `txn_table`(`user_id`, `txn_type`, `txn_amount`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$user_id','debits','1','RFQ Debits','$txn_id','$txn_date','1')";
        $res_add_txn2 = mysqli_query($conn, $add_txn2);

            $message = "You are requested quatation. RFQ Referance ID:- $ref_id and one RFQ credit deducted from your RFQ wallet";

            $ins_inbox = "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'RFQ Referance ID:- $ref_id', '$message', '1', '$txn_date')";
            $result_inbox = mysqli_query($conn, $ins_inbox);

            $ins_unotify = "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'RFQ Referance ID:- $ref_id', '$message', '1', '$txn_date')";
            $result_unotify = mysqli_query($conn, $ins_unotify);
		
	       }else{
	          $outp=2; 
	       }
	    }else{
	       $outp=2;  
	    }		
		
		
		$outp = json_encode($outp);
		
		echo($outp);
	
	$conn->close();
	
?> 