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
    
    $country_id = $_GET['country_id'];

	include("config.php"); 
        $sql="SELECT c.name as country_name, u.id,u.name,u.business_name,c.latitude,c.longitude 
        FROM franchise_users fu, countries c, users u WHERE fu.country_id=c.country_id AND fu.country_id = $country_id  and fu.user_id = u.id AND u.status='1' AND fu.franchise_type ='SF' AND fu.status='1'";
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
		}
		
	$conn->close();
		
		echo($outp);
	
?>