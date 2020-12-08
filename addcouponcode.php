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
	
		$coupon_code = rand(00000000,99999999);
	
		/* $user_id = 1;//$request->user_id;
		$pack_id = 1;//$request->pack_id;
		$coupon_code = $coupon_code; */
		
		$user_id = $request->user_id;
		$pack_id = $request->pack_id;
		$coupon_code = $coupon_code;
	    
	    $query="insert into coupon_codes(user_id,pack_id,coupon_code) values ('$user_id','$pack_id','$coupon_code')";
		//echo $query;
		$result = mysqli_query($conn,$query);
		if($result){
			$outp=$couponcode;
		}
		else{
			$outp=0;
		}
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 