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
	//require 'PHPMailer/src/PHPMailerAutoload.php';
	require '../PHPMailer/src/SMTP.php';
	
	    $otp = mt_rand(1111,9999);
		$fullname=$request->fullname;
		$fullname = stripslashes($fullname);// sql injections
	    $uemail = $request->email;
	    $email = stripslashes($email);
	    $mobile =$request->mobile;
	    $mobile = stripslashes($mobile);
	    $password = $request->password;
	    $password = stripslashes($password);
	    $password = md5($password);
	    $country = $request->country;
	    $country_code = $request->country_code;
	    $fmobile=$country_code.$mobile;
	    $state = $request->state;
	    $company = $request->company;
	    $company = stripslashes($company);
	    $address = $request->address;
	    $user_type= $request->user_type;
	    $date_added= date("Y-m-d H:i:s");
	    $zipcode= $request->zipcode;
	    $sponcer_id = $request->sponcerid;
	    $permitted_chars = 'abcdefghijklmnopqrstuvwxyz'; // Output: 54esmdr0qf
        $two = substr(str_shuffle($permitted_chars), 0, 2);
        $four = substr(str_shuffle($permitted_chars), 0, 4);
	    $ref_code=$two.$country_code.$four;
	    $long = $request->long;
	    $lat = $request->lat;
	    $facebook = $request->facebook;
	    $linkdin = $request->linkdin;
	    $twitter = $request->twitter;
	    $whatsapp = $request->whatapp;
	    $vchat = $request->vchat;
	    $skype = $request->skype;
	    $expyears = $request->expyears;
	    $tax_no = $request->tax_number;
	    $stars = $request->stars;
	    $ieccode = $request->ieccode;
	    $gstno = $request->gstno;
	    $otherno =$request->otherno;
	    $other_tax =$request->other_tax;
	    $isSubscribtion =$request->issubscribtion;
	    $isTaxnotpplicable =$request->isTaxnotpplicable;
	    $todaysdate = date("Y-m-d H:i:00");
	    $model =$request->model;
	    $manufacturer=$request->manufacturer;
	    $version = $request->version;
	    $serialno=$request->serialno;
	    $platform =$request->platform;
	    $from_time=$request->from_time;
	    $to_time=$request->to_time;
 
	    
	    $sender ='EXIMBNI';
        $mob =$mobile;
        $auth='D!~3133g8mKCYNGU7';
        $country_code=$country_code;
        
        $url = 
	    $device_id = $request->device_id;
	    $ipaddress = $_SERVER['REMOTE_ADDR'];
	    $chkmobile="select * from users where mobile='$fmobile'";
	    $chkres = mysqli_query($conn,$chkmobile);
	    $count = mysqli_num_rows($chkres);
	    if($count>0){
	        $outp=2;
	    }
	    else{
	    $chkemail="select * from users where email='$uemail'";
	    $chkresmail = mysqli_query($conn,$chkemail);
	    $mcount = mysqli_num_rows($chkresmail);
	    if($mcount>0){
	        $outp=3;
	    }
	    else{
	       
		$query="INSERT INTO users (name,username,email,mobile,country_id, state_id, zipcode, longitude, latitude, business_name,business_address,password,ipaddress,user_type,facebook,linkdin,twitter,whatsapp,vchat,skype,expyears,stars,ieccode,gstno,otherno,other_tax,isSubscribtion,isTaxnotpplicable,ref_code,sponcer_id,model,platform,version,manufacturer,serialno,from_time,to_time,device_id) values('$fullname','$uemail','$uemail','$fmobile','$country','$state','$zipcode','$long','$lat','$company','$address','$password','$ipaddress','$user_type','$facebook','$linkdin','$twitter','$whatsapp','$vchat','$skype','$expyears','$stars','$ieccode','$gstno','$otherno','$other_tax','$isSubscribtion','$isTaxnotpplicable','$ref_code','$sponcer_id','$model','$platform','$version','$manufacturer','$serialno','$from_time','$to_time','$device_id')";
		$result = mysqli_query($conn,$query);
		if($result){
		    $last_id = mysqli_insert_id($conn);
		$msg = "Your EXIMBNI verification OTP code is ".$otp.". Please DO NOT share this OTP with anyone.";
		$msg = urlencode($msg);

        /*$india_sql = mysqli_query($conn,"SELECT country_id FROM `countries` WHERE `name` = 'india' ORDER BY `name` ASC");
        $india_res = mysqli_fetch_array($india_sql);
        $india_id = $india_res['country_id'];*/

		
	        if($country==101){
	             $otpurl="http://prioritysms.tulsitainfotech.com/api/mt/SendSMS?user=eximbin&password=eximbin&senderid=EXIMBN&channel=Trans&DCS=0&flashsms=0&number=".$mob."&text=".$msg."&route=15";
                 $res=file_get_contents($otpurl);
	        }
		   
	        else{
	            $otpurl="https://global.datagenit.com/API/sms-api.php?auth=D!~3133g8mKCYNGU7&msisdn=".$mobile."&senderid=EXIMBNI&message=".$msg."&countrycode=".$country_code;
	            $res=file_get_contents($otpurl);
	        }
            
	        $otpins = "insert into otp (mobile, otp) values('$fmobile','$otp')";
		    $resotp =mysqli_query($conn,$otpins);
		
		$message = "Thank You For registration with EXIMBNI. You verification OTP is $otp. Please do not share this OTP with anyone.";
		
		$ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$last_id', '$country', 'EXIMBNI Registration Successful.', '$message', '1', '$todaysdate')";
		$result_inbox = mysqli_query($conn,$ins_inbox);
		
		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$last_id', '$country', 'EXIMBNI Registration Successful.', '$message', '1', '$todaysdate')";
		$result_unotify = mysqli_query($conn,$ins_unotify);
		
		$push_message = $message;
		$fcmid ="select device_id from users where id='$last_id' ";
	    $resid = mysqli_query($conn,$fcmid);
	    if($resid){
	        while($row = mysqli_fetch_assoc($resid)){
	            $id[]=$row["device_id"];
	        }
	    }
	    //fcm($push_message,$id);
		
		$email = 'info123@eximbin.com';
	    $password = 'EximBni.2020';
		$to_email = $uemail;
		$message = $message;
		$subject = "EXIMBNI Registration Successful.";
		
		$ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$last_id', '$country', 'EXIMBNI Registration Successful(Email).', '$message', '1', '$todaysdate')";
		$result_inbox = mysqli_query($conn,$ins_inbox);
			
		$mail = new PHPMailer(); // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = "mail.eximbin.com";
		$mail->Port = 25; // or 587
		$mail->IsHTML(true);
		$mail->Username = $email;
		$mail->Password = $password;
		$mail->SetFrom($email);
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->AddAddress($to_email);
		$mail->Send();
		
		//Email To MIIOS LIMITED
		$to_email = "miioslimited123@gmail.com";
		$message = $fullname.", Mob No.:-".$mobile." is registered with EXIM BNI. PlatForm:-".$platform."-".$version." Phone-Model:- ". $manufacturer."-".$model;
		$subject = "NEW Registration  in EXIM BNI";
		
		$mail = new PHPMailer(); // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = "mail.eximbin.com";
		$mail->Port = 25; // or 587
		$mail->IsHTML(true);
		$mail->Username = $email;
		$mail->Password = $password;
		$mail->SetFrom($email);
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->AddAddress($to_email);
		$mail->Send();
		//Email To MIIOS LIMITED
		
		$sql = "SELECT * FROM users where mobile='$fmobile'";
		$resql =mysqli_query($conn,$sql);
    
        while ($row = mysqli_fetch_assoc($resql)) {
        $outp[]= $row; 
        //$outp = $otpurl;
        } 
		
        
	}
		else{
		    $outp=$query;
		}
	    }
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 