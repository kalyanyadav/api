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
	$date = date("y-m-d");
	    $category_id=$_GET['category_id'];
	    $country_id = $_GET["country_id"];
        $sql = "SELECT * from chapters where category_id='$category_id'";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $chapter_id[] = $row["id"]; 
}

foreach($chapter_id as $val){
	$query = "select c.*, (select count(*) from leads where chapter_id='$val' and status=1  and posted_by IN (SELECT id FROM users WHERE status='1' ) and expiry_date>='$date' and lead_type='Sell')as sellleads,(select count(*) from leads where chapter_id='$val' and status=1  and posted_by IN (SELECT id FROM users WHERE status='1' ) and expiry_date>='$date' and lead_type='Buy')as buyleads from chapters c, leads l where c.id='$val' group by c.id;";
	$response = mysqli_query($conn,$query);
	if($response){
		while($row=mysqli_fetch_assoc($response)){
			$outp[]=$row;
		}
	}
}
    $outp= json_encode($outp);
		
		}
		else{
			$outp="0";
		}
		
	  $conn->close();
		
		echo($outp);
	
?>