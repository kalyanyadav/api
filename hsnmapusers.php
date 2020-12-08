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
    $country_id = $request->country_id;
    $hsn_code = $request->hsn_code;
    $user_type = $request->user_type;
        
		$sql = "select * from subscription_hscodes where hsn_code=$hsn_code";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
			while ($row = mysqli_fetch_assoc($result)) {
			$user_id[] = $row["user_id"];
				 //print_r ($row);
			}
			foreach($user_id as $key){
				
				$get = "select * from users where id='$key'";
				$resget = mysqli_query($conn,$get);
				if($resget){
					while($users = mysqli_fetch_assoc($resget)){
						$outp[]=$users;
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
