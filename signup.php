<?php
if (isset($_SERVER['HTTP_ORIGIN']))
{
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
    
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
include ("config.php");
//include("fcmpush.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
//require 'PHPMailer/src/PHPMailerAutoload.php';
require '../PHPMailer/src/SMTP.php';

$otp = mt_rand(1111, 9999);
$fullname = $request->fullname;
$fullname = stripslashes($fullname); // sql injections
$uemail = $request->email;
$email = stripslashes($uemail);
$mobile = $request->mobile;
$mobile = stripslashes($mobile);
$password = $request->password;
$password = stripslashes($password);
$password = md5($password);
$country = $request->country;
$country_code = $request->country_code;
$fmobile = $country_code . $mobile;
$state = $request->state;
$company = $request->company;
$company = stripslashes($company);
$address = $request->address;
//$address = real_escape_string($address);
$user_type = $request->user_type;
$date_added = date("Y-m-d H:i:s");
$zipcode = $request->zipcode;
$sponcer_id = $request->sponcerid;
$permitted_chars = 'abcdefghijklmnopqrstuvwxyz'; // Output: 54esmdr0qf
$two = substr(str_shuffle($permitted_chars) , 0, 2);
$four = substr(str_shuffle($permitted_chars) , 0, 4);
$ref_code = $two . $country_code . $four;
$long = $request->long;
if($long==''){
    $long = "0.0";
}
else {
    $long= $long;
}
$lat = $request->lat;
if($lat==''){
    $lat='0.0';
}
else {
    $lat=$lat;
}
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
if($gstno=='none'){
    $gstno=0;
}
$otherno = $request->otherno;
if($otherno==''){
    $otherno=0;
}
$other_tax = $request->other_tax;
if($other_tax=''){
    $other_tax=0;
}
$isSubscribtion = $request->issubscribtion;
if($isSubscribtion=='none'){
    $isSubscribtion=0;
}
$isTaxnotpplicable = 2;
$todaysdate = date("Y-m-d H:i:00");
$model = $request->model;
if($model==''){
    $model=0;
}
$manufacturer = $request->manufacturer;
if($manufacturer){
    $manufacturer="web";
}
$version = $request->version;
if($version==''){
    $version = "none";
}
$serialno = $request->serialno;
if($serialno==''){
    $serialno="none";
}
$platform = $request->platform;

$from_time = "09:00";

$to_time = "17:00";

if ($user_type == 'Other')
{

    $othertpe = $request->othertpe;
    $porttype = $request->porttype;
    $license_no = $request->license_no;
    $port_codes = $request->port_codes;
    $subscription_id = '1';

}
else
{

    $othertpe = "null";
    $porttype = "null";
    $license_no = "null";
    $port_codes = "null";
    $subscription_id = '0';

}

$sender = 'EXIMBNI';
$mob = $mobile;
$auth = 'D!~3133g8mKCYNGU7';
$country_code = $country_code;
$url = ''; 
$device_id = $request->device_id;
$ipaddress = $_SERVER['REMOTE_ADDR'];
$chkmobile = "select * from users where mobile='$fmobile'";
$chkres = mysqli_query($conn, $chkmobile);
$count = mysqli_num_rows($chkres);
if ($count > 0)
{
    $outp = 2;
}
else
{
    $chkemail = "select * from users where email='$uemail'";
    $chkresmail = mysqli_query($conn, $chkemail);
    $mcount = mysqli_num_rows($chkresmail);
    if ($mcount > 0)
    {
        $outp = 3;
    }
    else
    {

         $query = "INSERT INTO users (name,username,email,mobile,country_id, state_id, zipcode, longitude, latitude, business_name,business_address, password,ipaddress, user_type, facebook, linkdin, twitter, whatsapp, vchat,skype, expyears, stars,ieccode, gstno, otherno, other_tax, isSubscribtion, isTaxnotpplicable, ref_code, sponcer_id, model, platform, version,manufacturer, serialno, from_time, to_time, device_id, user_specification,licences_no, subscription_id,created) values('$fullname','$email','$email','$fmobile','$country','$state','$zipcode','$long','$lat','$company','$address','$password','$ipaddress','$user_type','$facebook','$linkdin','$twitter','$whatsapp','$vchat','$skype','$expyears','$stars','$ieccode','$gstno','$otherno','$other_tax','$isSubscribtion','$isTaxnotpplicable','$ref_code','$sponcer_id','$model','$platform','$version','$manufacturer','$serialno','09:00','17:00','$device_id','$othertpe','$license_no','$subscription_id','$todaysdate')";
        
        $result = mysqli_query($conn, $query);
        if ($result)
        {
            $last_id = mysqli_insert_id($conn);

            if ($port_codes != 'null')
            {

                foreach ($port_codes as $key => $value)
                {

                    $port_code[$key] = $value->port_code;
                }

                foreach ($port_code as $val)
                {
                    $portsql = "INSERT INTO user_portdetails (user_id,country_id,port_code) VALUES('$last_id','$country','$val')";
                    $resport = mysqli_query($conn, $portsql);

                }

            }

            $msg = "Your EXIMBNI verification OTP code is " . $otp . ". Please DO NOT share this OTP with anyone.";
            //$msg = urlencode($msg);

            /*$india_sql = mysqli_query($conn,"SELECT country_id FROM `countries` WHERE `name` = 'india' ORDER BY `name` ASC");
            $india_res = mysqli_fetch_array($india_sql);
            $india_id = $india_res['country_id'];*/

            if ($country_code != 91)
            {
                $otpurl = "http://sms.datagenit.in/API/sms-api.php?auth=D!~3133g8mKCYNGU7&msisdn=" . $mobile . "&senderid=EXIMBNI&message=" . $msg . "&countrycode=" . $country_code;
                $res = file_get_contents($otpurl);
            }

            else
            {

                $otpurl = "http://sms.datagenit.in/API/sms-api.php?auth=D!~3727SzpGFICCeC&msisdn=" . $mob . "&senderid=SMSOTP&message=" . $msg;
				$res = file_get_contents($otpurl);
            }

            $otpins = "insert into otp (mobile, otp) values('$fmobile','$otp')";
            $resotp = mysqli_query($conn, $otpins);


            //$email = 'info@eximbin.com';
            $email = 'noreply@eximbni.com';
            $password = '@team&1234';
            $to_email = $uemail;
            //$to_cc = 'patilvrushabh1008@gmail.com';
            $message = $msg;
            $subject = "EXIMBNI Registration OTP Verification ";

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
            //$mail->AddCC($to_cc);
            $mail->Send(); 



            //Email To MIIOS LIMITED
            $message = "New Registrtion in EXIMBNI. <br> User name : <strong>" . $fullname . " </strong>,<br>  Moblie No :  <strong>+" . $fmobile . " </strong>, <br> Address : <strong>" . $address . " </strong>, <br> Business Name : <strong>" . $company . " </strong>, <br> Sponcer ID :  <strong>" . $sponcer_id . " </strong>, <br> Operating System :  <strong>" . $platform . " </strong>,<br>  Device Model :  <strong>" . $model." </strong>.";

            //$email = 'info@eximbin.com';
            $email = 'noreply@eximbni.com';
            $password = '@team&1234';
            $to_email = 'miioslimited@gmail.com';
            $to_cc = 'logins@eximbni.com';
            $to_bcc = 'muralimiios@gmail.com ';
            $message = $message;
            $subject = "New registration ";

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
            $mail->AddCC($to_cc);
            $mail->AddBCC($to_bcc);
            $mail->Send();
            //Email To MIIOS LIMITED
            //Email To MIIOS LIMITED
            $message = "Thank You For registration with EXIMBNI. You verification OTP is $otp. Please do not share this OTP with anyone.";

            $ins_inbox = "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$last_id', '$country', 'EXIMBNI Registration Successful.', '$message', '1', '$todaysdate')";
            $result_inbox = mysqli_query($conn, $ins_inbox);

            $ins_unotify = "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$last_id', '$country', 'EXIMBNI Registration Successful.', '$message', '1', '$todaysdate')";
            $result_unotify = mysqli_query($conn, $ins_unotify);

            $push_message = $message;
            $fcmid = "select device_id from users where id='$last_id' ";
            $resid = mysqli_query($conn, $fcmid);
            if ($resid)
            {
                while ($row = mysqli_fetch_assoc($resid))
                {
                    $id[] = $row["device_id"];
                }
            }
            //fcm($push_message,$id);
            $ins_inbox = "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$last_id', '$country', 'EXIMBNI Registration Successful(Email).', '$message', '1', '$todaysdate')";
            $result_inbox = mysqli_query($conn, $ins_inbox);

            $sql = "SELECT * FROM users where mobile='$fmobile'";
            $resql = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($resql))
            {
                $outp[] = $row;
                //$outp = $otpurl;
                
                
                /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "User Sign Up Sucesfull";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $email;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
                
            }

        }
        else
        {
            $outp =  0;
            
            /* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Signup Fialed";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $email;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */ 
        }
    }
}
$outp = json_encode($outp);
echo ($outp);

$conn->close();

?>
