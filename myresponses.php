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
	//$lead_id = $request->lead_id;
	$lead_id = $_GET['lead_id'];
        $sql="select r.*,u.name, u.business_name, u.mobile, u.email, c.name as country from responses r, users u, countries c where lead_id ='$lead_id' and u.country_id = c.country_id  and u.id = r.response_posted_by GROUP BY r.id";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
            while ($row = mysqli_fetch_assoc($result)) {
            $outp[] = $row;
      }
		}
     
	
		else{
			$outp=0;
		}
		
	$outp = json_encode($outp);
echo $outp;
$conn->close();
		
		
?>
