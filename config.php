<?php
$host = "localhost";
$user = "eximbni_admin";
$pass = "EximBni.2020";
$db = "eximbni_eximbni";
$conn = mysqli_connect($host,$user,$pass,$db);
if($conn){
  // echo "Database Connected Successfully"; 
}else{
  // echo "Database Not Connected Successfully"; 
}
?>