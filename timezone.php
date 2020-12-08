<?php
$ip ="52.77.77.150"; //$_SERVER['REMOTE_ADDR'];
$ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);
$ipInfo = json_decode($ipInfo);
$timezone = $ipInfo->timezone;
date_default_timezone_set($timezone);
echo date_default_timezone_get();
echo date('Y/m/d H:i:s');

?>