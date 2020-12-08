<?php
include("config.php");
$sql = "SELECT * FROM `testhsncodes` limit 2000";
$res = mysqli_query($conn,$sql);
if($res){
	while($row=mysqli_fetch_assoc($res)){
		$outp[] =$row;
	}
	echo "<pre>";
	print_r($outp);
}
$outp=json_encode($outp);
echo $outp."<br>";

?>