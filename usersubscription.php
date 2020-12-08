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
$rfq_credits=0;
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
  $credit = "insert into wallet (user_id,credits,rfq_credits,subscription_id) values ('$userid','$credits','$rfq_credits','$subscription_id')";
$rescredit = mysqli_query($conn, $credit);
    if($rescredit){
        $outp=1;
    }
    else
{
   $outp=$credit;
    
}
}
$outp = json_encode($outp);
echo $outp;

$conn->close();

?>
