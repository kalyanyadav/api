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
	$franchise_id = $_GET['franchise_id'];
        $sql = "SELECT id, target_type, month, quarter, year, target, achievement  FROM `franchise_target` WHERE `franchise_id` = '$franchise_id' AND status = '1' ORDER BY id DESC LIMIT 0,3";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
            while ($row = mysqli_fetch_assoc($result)) {
                $outp[] = $row; 
            }
        
		}
		else{
			$outp="0";
		}
		
	$outp= json_encode($outp);
		
	$conn->close();
		
		echo($outp);
	
?>