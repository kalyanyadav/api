<?php
$host = "eximbni.com";
$user = "exim_root";
$pass = "EximBni.2020";
$db = "eximbni";
$conn = mysqli_connect($host,$user,$pass,$db);
if($conn){
    echo "Successfully Connected";
}
else{
    echo "Something went wrong";
}

?>