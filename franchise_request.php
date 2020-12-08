<?php
date_default_timezone_set('Asia/Kolkata');
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
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
	//require '../PHPMailer/src/PHPMailerAutoload.php';
	require '../PHPMailer/src/SMTP.php';

	$randkey =  rand(111111,999999);	
	$user_id = $request->user_id;
	$franchise_type_id = $request->franchise_type;  
	$country_id = $request->country_id; 
	$state_id = $request->state_id; 
	$description = $request->description;
	$business_background = $request->business_background;
	$url = $request->url;
	$countryof_origin = $request->countryof_origin;
	$business_info = $request->business_info;
	$infrastructure = $request->infrastructure;
	$ownership_type = $request->ownership_type;
	$anual_turnover = $request->anual_turnover;
	$willing_tosetup = $request->willing_tosetup;

    $status = 0;
	
	$todaysdate = date('Y:m:d H:i:00');

    $getcountry_code = "select iso_code_2 from countries where country_id='$country_id'";
    $rescountry_code = mysqli_query($conn, $getcountry_code);
    if ($rescountry_code) {
        $row_country = mysqli_fetch_array($rescountry_code);
        $country_code = $row_country["iso_code_2"];
        
    }	
	
    $franchise_code = "select code from franchise_type where id='$franchise_type_id'";
    $franchise_code = mysqli_query($conn, $franchise_code);
    if ($franchise_code) {
        $row_franchise = mysqli_fetch_array($franchise_code);
        $code_franchise = $row_franchise["code"];
        
    }	


	if($country_id ==''){
	    $country_id =0;
	}
	
	if($franchise_type_id=='1'){
	    $request_id = $code_franchise."-".$country_id."-".$randkey;
	}elseif($franchise_type_id=='2'){
	    $request_id = $code_franchise."-".$country_id."-".$state_id."-".$randkey;
	}else{
	    $request_id = "";
	}
	
	
	    if($request_id !="")
	    {

    	    $sql="INSERT INTO franchise_request(user_id,franchise_type_id,country_id,state_id,description,status,request_id,business_background,url,countryof_origin,business_info,infrastructure,ownership_type,anual_turnover,willing_tosetup)
    	    values('$user_id','$franchise_type_id','$country_id','$state_id','$description','$status','$request_id','$business_background','$url','$countryof_origin','$business_info','$infrastructure','$ownership_type','$anual_turnover','$willing_tosetup')";
    	
    		$res = mysqli_query($conn,$sql);
			if($res){
				
				$get_user = "select email from users where id='$user_id'";
				$res_get_user = mysqli_query($conn,$get_user);
				$row_get_user = mysqli_fetch_assoc($res_get_user);
				$to_email = $row_get_user['email']; 
				
				$message = "Franchise request submited successfully to EximBNI. and your Request ID is ".$request_id .". Please keep this for any future reference. We are happy to acknowledge that  your request for franchise  is received by us, and our sales team will get back to you shortly. Kindly keep ref_id $request_id handy for future reference and any communication";
				
				$ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country_id', 'Franchise Request Submit Successful.', '$message', '1', '$todaysdate')";
        		$result_inbox = mysqli_query($conn,$ins_inbox);
        		
        		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country_id', 'Franchise Request Submit Successful.', '$message', '1', '$todaysdate')";
        		$result_unotify = mysqli_query($conn,$ins_unotify);
				
                //email sending
				$email = 'info@eximbin.com';
                $password = 'EximBni.2020';
				$to_email = $to_email;
				$to_cc = 'miioslimited@gmail.com';
                $to_bcc = 'muralimiios@gmail.com';
				//$message = "Thank You for submiting franchise request. Our team will be contact you very soon.";
				$subject = "Franchise request submited successfully";
					
				$mail = new PHPMailer(); // create a new object
				$mail->IsSMTP(); // enable SMTP
				$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
				$mail->SMTPAuth = true; // authentication enabled
				$mail->SMTPSecure = 'SSL'; // secure transfer enabled REQUIRED for Gmail
        		$mail->Host = "mail.eximbin.com";
        		$mail->Port = 25; // or 587
				$mail->IsHTML(true);
				$mail->Username = $email;
				$mail->Password = $password;
				$mail->SetFrom($email);
				$mail->Subject = $subject;
				$mail->Body = $message;
				$mail->AddAddress($to_email);
				$mail->AddCC($to_cc);
				$mail->AddBCC($to_bcc);
				$mail->Send();
				//email sending
				
                $outp=$request_id;
			} 
			else{
			$outp= 0;
			}
		}else{
		    $outp =0;
		}
	$outp=json_encode($outp);
	echo $outp;
	
	$conn->close();

?>
