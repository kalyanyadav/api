<?php 
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache for 1 day
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

        
		//$sql = "select u.latitude as lat, u.longitude as lng, u.name, u.user_type, fu.franchise_type as ftype  from users u, franchise_users fu  where u.id = fu.user_id AND u.longitude !='' and u.latitude !=''";
		$sql = "select u.latitude as lat, u.longitude as lng, u.name, u.user_type, fu.franchise_type_id as ftype  from users u, franchise_request fu  where u.id = fu.user_id AND u.longitude !='' and u.latitude !=''";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
			while ($row = mysqli_fetch_assoc($result)) {
			$outp[] = $row;
				 //print_r ($row);
			}
					
		}
		else{
			$outp="0";
		}
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>
