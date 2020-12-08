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
	$user_id= $_GET["user_id"];
    $sql= "select ipaddress,from_time,to_time from users where id='$user_id'";
	$res = mysqli_query($conn,$sql);
	if($res){
	    "connected";
	    while($row = mysqli_fetch_assoc($res)){
	        $ipaddr = $row["ipaddress"];
	        $from_time = $row["from_time"];
	        $to_time = $row["to_time"];
	    }
	    
	   $ip = $ipaddr;  //$_SERVER['REMOTE_ADDR']
$ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);
$ipInfo = json_decode($ipInfo);
$timezone = $ipInfo->timezone;
date_default_timezone_set($timezone);
$utime= date('H:i');

if($utime >$from_time && $utime< $to_time){
    $outp=1;
}
else{
    $outp=0;
}
	}
	else{
	    $outp=0;
	}
	echo $outp;
	
	?>