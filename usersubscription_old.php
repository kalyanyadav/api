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

//$userid = $request->user_id;
//$subscription_id = $request->pack_id;
//$duration = $request->duration;
//$country_id = $request->country_id;
//$state_id = $request->state_id;


$userid = $request->user_id; // '74';
$subscription_id = $request->pack_id; //'45';
$duration = $request->duration; // '30';
$country_id = $request->country_id; //'99';
$state_id = $request->state_id; //'1476';
$credits = $request->credits;
if ($duration == 30)
{
    $credits = $credits / 12;
}
else
{
    $credits = $credits;
}
$subscription_start = date("Y-m-d");
$txn_date = date("Y-m-d");
$subscription_end = date('Y-m-d', strtotime("+" . $duration . " days"));

$query = "update users set subscription_id='$subscription_id', subscription_start='$subscription_start', subscription_end='$subscription_end' where id='$userid'";
$result = mysqli_query($conn, $query);
if ($result)
{
    //echo"success update <br>";
    
}
else
{
    //echo"fail to update <br>";
    
}
//update chapters for user
$chapters = $request->chapters; //'[{"id":"2","chapter_name":"Chapter 02"},{"id":"4","chapter_name":"Chapter 04"},{"id":"5","chapter_name":"Chapter 05"}]';
//$chapters = json_decode($chapters);
foreach ($chapters as $key => $value)
{
    $chapters[$key] = $value->id;
}
foreach ($chapters as $val)
{
    $sqlc = "insert into subscription_chapter (user_id,chapter_id,status) values ('$userid','$val','1')";
    $resc = mysqli_query($conn, $sqlc);
    //echo $sqlc."<pre>";
    if ($resc)
    {
        $outp = 1;

    }
    else
    {
        $outp = 0;
    }

}
//update Hscodes for user
$hscodes = $request->hscodes; //'[{"id":"2","chapter_name":"Chapter 02"},{"id":"4","chapter_name":"Chapter 04"},{"id":"5","chapter_name":"Chapter 05"}]';
//$chapters = json_decode($chapters);
foreach ($hscodes as $key => $value)
{
    $hscodes[$key] = $value->hscode;
}

foreach ($hscodes as $val)
{
    $sqlc = "insert into subscription_hscodes (user_id,hsn_code,status) values ('$userid','$val','1')";
    $resc = mysqli_query($conn, $sqlc);
    //echo $sqlc."<pre>";
    if ($resc)
    {
        $outp = 1;

    }
    else
    {
        $outp = 0;
    }
}

//get subscription amount
$get_sub_amt = "select plan_name, plan_cost,rfq from subscriptions where id='$subscription_id'";
$res_sub_amt = mysqli_query($conn, $get_sub_amt);
$row_sub_amt = mysqli_fetch_array($res_sub_amt);
$sub_amount = $row_sub_amt['plan_cost'];
$packagename = $row_sub_amt['plan_name'];
$rfq = $row_sub_amt['rfq'];
if($rfq == 'NO'){
   $rfq_credits ='0'; 
}else{
   $rfq_credits =$rfq;  
}

$credit = "insert into wallet (user_id,credits,rfq_credits,subscription_id) values ('$userid','$credits','$rfq_credits','$subscription_id')";
$rescredit = mysqli_query($conn, $credit);

//Add transaction
$txn_id = rand(00000000, 99999999);
$txn_id1 = rand(00000000, 99999999);
$txn_id2 = rand(00000000, 99999999);

$admin_income = "INSERT INTO `admin_income`(`user_id`, `txn_amount`, `txn_type`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$userid','$sub_amount','amount','subscription','$txn_id','$txn_date','1')";
$res_admin_income = mysqli_query($conn, $admin_income);
if ($res_admin_income)
{
    $msg = "Admin Income Inserted";
}
else
{
    //echo "Not inserted";
    mysqli_error($conn);
}
$add_txn1 = "INSERT INTO `txn_table`(`user_id`, `txn_type`, `txn_amount`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$userid','credits','$credits','Wallet Credit','$txn_id1','$txn_date','1')";
$res_add_txn1 = mysqli_query($conn, $add_txn1);

$add_txn2 = "INSERT INTO `txn_table`(`user_id`, `txn_type`, `txn_amount`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$userid','credits','$rfq_credits','RFQ Credit','$txn_id2','$txn_date','1')";
$res_add_txn2 = mysqli_query($conn, $add_txn2);

$add_txn = "INSERT INTO `txn_table`(`user_id`, `txn_type`, `txn_amount`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$userid','amount','$sub_amount','subscription','$txn_id','$txn_date','1')";
$res_add_txn = mysqli_query($conn, $add_txn);
if ($res_add_txn)
{
    $msg = "Transaction Inserted";
}
else
{
    //echo "Transaction Not inserted";
    mysqli_error($conn);
}

            $message = "You have selected package $packagename and $credits credit added into your wallet";

            $ins_inbox = "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$userid', '$country_id', 'Added Package Subscription Credit.', '$message', '1', '$txn_date')";
            $result_inbox = mysqli_query($conn, $ins_inbox);

            $ins_unotify = "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$userid', '$country_id', 'Added Package Subscription Credit.', '$message', '1', '$txn_date')";
            $result_unotify = mysqli_query($conn, $ins_unotify);


//Add transaction
$getpackamount = "select * from subscriptions where id='$subscription_id'";
$respak = mysqli_query($conn, $getpackamount);
if ($respak)
{
    while ($rowpack = mysqli_fetch_assoc($respak))
    {
        $amount = $rowpack["plan_cost"];
        $chapterss = $rowpack["chapters"];
    }
}


//Identifying Commission


$cfrcode = "CF-" . $country_id;
$getcf = "select * from franchise_users where frcode='$cfrcode'";
$resgetcf = mysqli_query($conn, $getcf);
if (mysqli_num_rows($resgetcf) > 0)
{
    while ($rowcf = mysqli_fetch_assoc($resgetcf))
    {
        $franchise_id = $rowcf["id"];
        $frcommission = $rowcf["commission"];
    }

    $totalcommission = $amount * $frcommission / 100;
    
  
}
else {
    $totalcommission = $amount * 4.5;
}

//Identifying Commission code ends here

//state franchise
$sfrcode = "SF-" . $country_id . "-" . $state_id;
$getsf = "select * from franchise_users where frcode= '$sfrcode'";
$resgetsf = mysqli_query($conn, $getsf);
if (mysqli_num_rows($resgetsf) > 0)
{
    while ($rowsf = mysqli_fetch_assoc($resgetsf))
    {
        $sfranchise_id = $rowsf["id"];
        $sfrcommission = $rowsf["commission"];
    }

    $sfamount = $totalcommission * $sfrcommission / 100;
    $payment_date = date("Y-m-d");
    $sfcom = "insert into frachise_accounts (user_id,franchise_id,amount,payment_for,payment_date,status) values ('$user_id','$sfranchise_id','$sfamount','subscription','$payment_date','0')";
    $rssf = mysqli_query($conn, $sfcom);

}

//State Franchise Ends Here

//country Franchise Commission


$cfrcode = "CF-" . $country_id;
$getcf = "select * from franchise_users where frcode= '$cfrcode'";
$resgetcf = mysqli_query($conn, $getcf);
if (mysqli_num_rows($resgetcf) > 0)
{
    while ($rowcf = mysqli_fetch_assoc($resgetcf))
    {
        $franchise_id = $rowcf["id"];
        $frcommission = $rowcf["commission"];
    }
    if(!$sfamount){
        $sfamount=0;
    }
    $cfamount = $totalcommission-$sfamount;
    
    $payment_date = date("Y-m-d");
    $cfcom = "insert into frachise_accounts (user_id,franchise_id,amount,payment_for,payment_date,status) values ('$user_id','$franchise_id','$cfamount','subscription','$payment_date','0')";
    $rscf = mysqli_query($conn, $cfcom);

}

//country franchise code ends here

$payment_date = date("Y-m-d");
//R&D Amount code starts here

$rnd_amount = $amount*0.1;
$insrnd = "insert into rndfund (user_id,state, amount,country, payment_for,payment_date,status) values ('$userid','$state_id','$rnd_amount','$country_id','subscription','$payment_date','1') ";
$resrnd = mysqli_query($conn,$insrnd);

//R&D Amount code ends here

//EximBNI Account code starts here
$exim_amount  = $amount-($cfamount+$sfamount+$rnd_amount);
 $eximcom = "insert into eximfund (user_id,state_id,country_id,payment_for,amount,status,payment_date) values ('$userid','$state_id','$country_id','Subscription','$exim_amount','1','$payment_date')";
    $resexim = mysqli_query($conn, $eximcom);
//EximBni code Ends Here

$outp = json_encode($outp);
echo $outp;

$conn->close();

?>
