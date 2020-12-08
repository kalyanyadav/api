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
	$date_today = date('Y-m-d');
	$user_id=$_GET["user_id"];
	$hsn_id=$_GET["hsn_id"];
    $hsn_id = substr($hsn_id,0,6);    
		//$sql= "select l.* from leads l where l.hsn_id like '$hsn_id%' and l.lead_type = 'Buy' and l.status=1 and l.expiry_date>'$date_today' group by l.id";
		$sql = "select *,(select count(*) from myfav_hscodes where user_id='$user_id' and hscode=leads.hsn_id) as hsstate from leads where hsn_id like '$hsn_id%' and lead_type='Buy' and status=1";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $arr[] = $row; 
}
    $outp= json_encode($arr);
		
		}
		else{
			$outp="0";
			//$outp=$sql;
		}
		
	$conn->close();
		
		echo($outp);
	
?>