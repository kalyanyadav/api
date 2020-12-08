<?php
session_start();
include("config.php");
$token = $token = bin2hex(random_bytes(64));
echo $token;
$sql = "insert into auth_table (token) values('$token')";
$res = mysqli_query($conn,$sql);
if($res){
$_SESSION['$token']= $token;

}
?>