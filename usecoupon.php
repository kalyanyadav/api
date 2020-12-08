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
	$franchise_id = $request->franchise_id;
	$coupon_code = $request->coupon_code;
	$pack_id = $request->pack_id;
	$created_date = date("Y-m-d");
	$status = 1;
	
	$sql ="insert into coupon_codes (franchise_id, coupon_code,pack_id,created_date,status) values('$franchise_id','$coupon_code','$pack_id','$created_date','$status')";
	$res = mysqli_query($conn,$sql);
	if($res){
	    $getcoupons = "select coupons from franchise_users where id ='$franchise_id'";
	    $resget = mysqli_query($conn,$getcoupons);
	    if($resget){
	        while($row=mysqli_fetch_assoc($resget)){
	            $coupons = $row['coupons'];
	        }
	        
	        if($coupons > 0 && $coupons !=''){
    	        $ncoupons = $coupons-1;
    	        $upd = "update franchise_users set coupons='$ncoupons' where id ='$franchise_id'";
    	        $resupd = mysqli_query($conn,$upd);
    	        if($resupd){
    	            $outp=1;
    	        }	            
	        }

	    }
	    
	    $outp = 1;
	}
	else{
	    $outp=$sql;
	}
	
	$outp= json_encode($outp);
	echo$outp;
	$conn->close();
	?>