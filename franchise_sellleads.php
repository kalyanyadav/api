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
	$country_id=$_GET["country_id"];

		$sql = "select * from lead_display_countries where country_id = '$country_id'";
		
		
		
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
			while ($row = mysqli_fetch_assoc($result)) {
			$lead_id[] = $row['lead_id'];
				//print_r ($row);
			}
			
			foreach($lead_id as $key){
			$getleads = "select l.id, l.lead_cost, u.name, u.business_name from leads l, users u where l.id = '$key' and l.lead_type = 'Sell' and l.status = 1 and l.posted_by =u.id";
			
			
			$getres = mysqli_query($conn,$getleads);
			if($getres){
				while($rows=mysqli_fetch_assoc($getres)){
					$outp[]=$rows;
					}
				}
			}
		}
		else{
			$outp="0";
		}
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>