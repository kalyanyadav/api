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
		$sql = "select l.*, u.* from leads l, uoms u where l.lead_type='Sell' and l.status ='1' and l.uom_id = u.id AND l.posted_by IN (SELECT id FROM users WHERE status='1' )";
		$result = mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
		while ($row = mysqli_fetch_assoc($result)) {
		 $arr[] = $row; 
		}
		$outp= json_encode($arr,JSON_INVALID_UTF8_IGNORE);
		
		}
		else{
			$outp="0";
		} 
		
	//$conn->close();
		
		echo $outp;
	
?>