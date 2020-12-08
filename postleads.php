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
$ip = $_SERVER['REMOTE_ADDR'];
$ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);
$ipInfo = json_decode($ipInfo);
$timezone = $ipInfo->timezone;
date_default_timezone_set($timezone);
include ("config.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
//require 'PHPMailer/src/PHPMailerAutoload.php';
require '../PHPMailer/src/SMTP.php';

$lead_type = $request->lead_type;

if ($lead_type == "Buy")
{
    $ext = "B";
}
else
{
    $ext = "S";
}

$otp = mt_rand(1111, 9999);
$impexpotp = mt_rand(1111, 9999);
$category_id = $request->category_id;
$chapter_id = $request->chapter_id;
$getchp = $chapter_id;
$hsn_id = $request->hsncode_id;
$product_id = ""; //$request->product_id;
$uom_id = $request->uom_id;
$quantity = $request->quantity;
$continent_id = $request->continent;
$user_country = $request->user_country;

$posted_by = $request->user_id;
$description = $request->description;
$expiry_date = $request->last_date;
$mobile = $request->mobile; //'8008001226';
$posted_date = date("Y-m-d H:i:s");
$document1 = $request->document1;
$document2 = $request->document2;
$document3 = $request->document3;
$document4 = $request->document4;
$price_inusd = $request->price_inusd;
$price_option = $request->price_option;
$loading_port_type = $request->loading_port_type;
$loading_port = $request->loading_port;
$destination_port_type = $request->destination_port_type;
$destination_port = $request->destination_port;
$special_instruc = $request->special_instruc;
$index_per = $request->index_discount;
$inspection_auth = $request->inspection_auth;
$business_address = $request->business_address;
$available_time = $request->available_time;
$available_time = $available_time . ':00';
$impexpmobile = $request->impexpmobile;
$currency = $request->currency;

$getrefid = "select * from leads where lead_type='$lead_type' and country_id='$user_country' and chapter_id='$chapter_id' order by id desc limit 1";
$resref = mysqli_query($conn, $getrefid);
if (mysqli_num_rows($resref) > 0)
{

    while ($row = mysqli_fetch_assoc($resref))
    {
        $sno = $row["sno"];
    }
    $nsno = $sno + 1;

}
else
{
    $nsno = 1;
}

$getcountry_code = "select iso_code_2 from countries where country_id='$user_country'";
$rescountry_code = mysqli_query($conn, $getcountry_code);
if ($rescountry_code)
{
    while ($row_country = mysqli_fetch_assoc($rescountry_code))
    {
        $country_code = $row_country["iso_code_2"];
    }
}

$priceoption = $price_option;

$query = "INSERT INTO leads (lead_type,posted_by,hsn_id,chapter_id,categories_id,product_id,uom_id, country_id, quantity,description,posted_date,expiry_date,sno,price_inusd,currency,price_option,index_per,special_instruc,inspection_auth,port_type,loading_port,destination_port) 
    values(
        '$lead_type',
        '$posted_by',
        '$hsn_id',
        '$chapter_id',
        '$category_id',
        '$product_id',
        '$uom_id',
        '$user_country',
        '$quantity',
        '$description',
        '$posted_date',
        '$expiry_date',
        '$nsno',
        '$price_inusd',
        '$currency',
        '$priceoption',
        '$index_per',
        '$special_instruc',
        '$inspection_auth',
        '$loading_port_type',
        '$loading_port',
        '$destination_port'
        )";

//echo $query;
$result = mysqli_query($conn, $query);
if ($result)
{
    $lastinserted_id = mysqli_insert_id($conn);

    if ($chapter_id < 10)
    {
        $chapter_id = '0' . $chapter_id;
    }

    $leadref_id = $ext . "-" . $country_code . "-" . $chapter_id . "-" . $lastinserted_id;
    mysqli_query($conn, "UPDATE `leads` SET `leadref_id` = '$leadref_id' WHERE id ='$lastinserted_id'");

    if ($business_address !== '')
    {

        mysqli_query($conn, "UPDATE `users` SET `business_address` = '$business_address' WHERE id ='$posted_by'");
    }

    if ($document1 != '')
    {
        $sqldoc1 = "INSERT INTO `lead_documents` (`user_id`, `lead_id`, `doc_name`, `doc_path`) VALUES ('$posted_by', '$lastinserted_id', 'Document1', '$document1')";
        mysqli_query($conn, $sqldoc1);
    }

    if ($document2 != '')
    {
        $sqldoc2 = "INSERT INTO `lead_documents` (`user_id`, `lead_id`, `doc_name`, `doc_path`) VALUES ('$posted_by', '$lastinserted_id', 'Document2', '$document2')";
        mysqli_query($conn, $sqldoc2);
    }

    if ($document3 != '')
    {
        $sqldoc3 = "INSERT INTO `lead_documents` (`user_id`, `lead_id`, `doc_name`, `doc_path`) VALUES ('$posted_by', '$lastinserted_id', 'Document3', '$document3')";
        mysqli_query($conn, $sqldoc3);
    }

    if ($document4 != '')
    {
        $sqldoc4 = "INSERT INTO `lead_documents` (`user_id`, `lead_id`, `doc_name`, `doc_path`) VALUES ('$posted_by', '$lastinserted_id', 'Document4', '$document4')";
        mysqli_query($conn, $sqldoc4);
    }

    
    $message = "You Posted Lead with Referance ID: " . $leadref_id . " and description: " . $description . " is under admin verification. once been verified you requirement will be visble to public. A confirmation mail will be sent to you with in 72 hours.";
    $msg = "Your EXIMBNI verification OTP code is " . $otp . ". Please DO NOT share this OTP with anyone.";
    
/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Lead posted Sucesfully and sent OTP to email";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 1;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */  		    

    $impexpmsg = "Your EXIMBNI verification OTP code is " . $impexpotp . ". Please DO NOT share this OTP with anyone.";

    $ins_inbox = "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$posted_by', '$user_country', 'Lead Posted Successfully(Email).', '$message', '1', '$posted_date')";
    $result_inbox = mysqli_query($conn, $ins_inbox);

    $chkmobile = "select * from users where id='$posted_by'";
    $chkres = mysqli_query($conn, $chkmobile);
    $rowmob = mysqli_fetch_array($chkres);
    $mob = $rowmob['mobile'];

    $india_sql = mysqli_query($conn, "SELECT country_id FROM `countries` WHERE `name` = 'india' ORDER BY `name` ASC");
    $india_res = mysqli_fetch_array($india_sql);
    $india_id = $india_res['country_id'];

   // $msg = urlencode($msg);
    //$impexpmsg = urlencode($impexpmsg);
    if ($impexpmobile)
    {

        if ($user_country == 101)
        {
            $otpurl1 = "http://sms.datagenit.com/API/sms-api.php?auth=D!~3133g8mKCYNGU7&msisdn=" . $impexpmobile . "&senderid=EXIMBNI&message=" . $impexpmsg;
            //$outp = 3;
           // $res = file_get_contents($otpurl1);
            $otpurl = "http://sms.datagenit.com/API/sms-api.php?auth=D!~3133g8mKCYNGU7&msisdn=" . $mob . "&senderid=EXIMBNI&message=" . $msg;
           // $res = file_get_contents($otpurl);
        }

        else
        {

            $otpurl1 = "http://sms.datagenit.com/API/sms-api.php?auth=D!~3133g8mKCYNGU7&msisdn=" . $impexpmobile . "&senderid=EXIMBNI&message=" . $impexpmsg. "&countrycode=" . $user_country;
            //$outp = 3;
            $res = file_get_contents($otpurl1);
            //$otpurl = "http://sms.datagenit.com/API/sms-api.php?auth=D!~3133g8mKCYNGU7&msisdn=" . $mob . "&senderid=EXIMBNI&message=" . $msg . "&countrycode=" . $user_country;
          //  $res = file_get_contents($otpurl);
        }

        $otpins1 = "insert into otp (mobile, otp) values('$impexpmobile','$impexpotp')";
        $resotp1 = mysqli_query($conn, $otpins1);

        $otpins = "insert into otp (mobile, otp) values('$mobile','$otp')";
        $resotp = mysqli_query($conn, $otpins);

    }
    else
    {

        if ($user_country == $india_id)
        {
            $otpurl = "http://sms.datagenit.in/API/sms-api.php?auth=D!~3727SzpGFICCeC&msisdn=" . $mob . "&senderid=SMSOTP&message=" . $msg;
          //  $res = file_get_contents($otpurl);
        }

        else
        {
            $otpurl = "http://sms.datagenit.com/API/sms-api.php?auth=D!~3133g8mKCYNGU7&msisdn=" . $mob . "&senderid=EXIMBNI&message=" . $msg . "&countrycode=" . $user_country;
          //  $res = file_get_contents($otpurl);
        }

        $otpins = "insert into otp (mobile, otp) values('$mobile','$otp')";
        $resotp = mysqli_query($conn, $otpins);

    }

    $out = $otpurl;

    $ins_inbox = "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$posted_by', '$user_country', 'Lead Posted Successfully.', '$message', '1', '$posted_date')";
    $result_inbox = mysqli_query($conn, $ins_inbox);

    $ins_unotify = "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$posted_by', '$user_country', 'Lead Posted Successfully.', '$message', '1', '$posted_date')";
    $result_unotify = mysqli_query($conn, $ins_unotify);

    $get_user = "select email from users where id='$posted_by'";
    $res_get_user = mysqli_query($conn, $get_user);
    $row_get_user = mysqli_fetch_assoc($res_get_user);
    $to_email = $row_get_user['email'];

    // ******************************************************* Start SEND OTP ON MAIL FOR BOTH Other user and Importer/Exporter *************************************************
    if ($impexpmobile)
    {

        $get_user1 = "select email from users where mobile='$impexpmobile'";
        $res_get_user1 = mysqli_query($conn, $get_user1);
        $row_get_user1 = mysqli_fetch_assoc($res_get_user1);
        $to_email1 = $row_get_user1['email'];

            //$email = 'info@eximbin.com';
            //$password = 'EximBni.2020';
           
            $email = 'noreply@eximbni.com';
            $password = '@team&1234';
            $to_email1 = $to_email1;
            $subject = "Post Lead OTP Verification";

            $mail = new PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "mail.eximbni.com";
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);
            $mail->Username = $email;
            $mail->Password = $password;
            $mail->SetFrom($email);
            $mail->Subject = $subject;
            $mail->Body = $impexpmsg;
            $mail->AddAddress($to_email1);
           // $mail->AddCC($to_cc);
            $mail->Send(); 



            //$email = 'info@eximbin.com';
            $email = 'noreply@eximbni.com';
            $password = '@team&1234';
            $to_email = $to_email;
            //$to_cc = 'muralimiios@gmail.com';
            $message = $msg;
            $subject = "Post Lead OTP Verification";

            $mail = new PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "mail.eximbni.com";
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);
            $mail->Username = $email;
            $mail->Password = $password;
            $mail->SetFrom($email);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AddAddress($to_email);
           // $mail->AddCC($to_cc);
            $mail->Send(); 



    }



            //$email = 'info@eximbin.com';
            $email = 'noreply@eximbni.com';
            $password = '@team&1234';
            $to_email = $to_email;
            //$to_cc = 'ganesh.vab@gmail.com';
            $message = $msg;
            $subject = "Post Lead OTP Verification";

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
           // $mail->AddCC($to_cc);
            $mail->Send(); 


    // ******************************************************* End SEND OTP ON MAIL FOR BOTH Other user and Importer/Exporter *************************************************
     $countries = $request->country;

    foreach ($countries as $key => $val)
    {
        $countries[$key] = $val->country_id;
    }

    foreach ($countries as $val2)
    {

        $sqlc = "insert into lead_display_countries (lead_id,country_id) values ('$lastinserted_id','$val2')";
        $resc = mysqli_query($conn, $sqlc);
        if ($resc)
        {
            $outp = $leadref_id; //$lastinserted_id;
        }
        else
        {
            $outp = 3;
        }

    }

    $outp = $leadref_id;//$lastinserted_id;
}
else
{
    $outp = 0;
    
    
/* Transaction Log Code start  */
        require_once('transactions.php');
        $transaction_name = basename($_SERVER['PHP_SELF']); 
        $transaction_desc = "Failed to post Lead";
        $transaction_source = $_SERVER['HTTP_USER_AGENT'];
        $transaction_user = $user_id;
        $transaction_status = 0;
        log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */  
}

$outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);
echo $outp;

$conn->close();

?>
