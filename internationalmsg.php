<?php
$sender ='EXIMBNI';
$mob ='6026158558';
$auth='D!~3133g8mKCYNGU7';
$msg = urlencode("Int Test"); 
$country_code=1;

$url = 'https://global.datagenit.com/API/sms-api.php?auth='.$auth.'&msisdn='.$mob.'&senderid='.$sender.'&message='.$msg.'&countrycode='.$country_code;  // API URL

$result=SendSMS($url);  // call function that return response with code
echo $result;

//function define
function SendSMS($hostUrl){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $hostUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // change to 1 to verify cert
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
$result = curl_exec($ch);
return $result;
} 
?>