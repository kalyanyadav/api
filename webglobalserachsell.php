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
	$search_value=$_GET["serach_value"];
	$search_type=$_GET["serach_type"];
	$search_type;
	$user_id = $_GET["user_id"];
	
	if($search_type=='category'){
		$sql = "SELECT * from categories where category_name LIKE '%$search_value%'";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count <=0){
			$outp=2;
		}
		else{
			while($crow=mysqli_fetch_assoc($result)){
				$cid[]=$crow["id"];
			}
			//print_r($cid);
			foreach($cid as $key){
			$getleads = "select * from leads where categories_id = '$key' and expiry_date >'$date_today' and lead_type='Sell'";
			
			$getres = mysqli_query($conn,$getleads);
			if($getres){
				while($rows=mysqli_fetch_assoc($getres)){
					$outp[]=$rows;
					}
				}
			}
		}
	}
	elseif($search_type=='chapter'){
		$csql = "SELECT * from chapters where ch_description LIKE '%$search_value%'";
		$cresult =mysqli_query($conn,$csql);
		if(mysqli_num_rows($cresult) <= 0){
			$outp=2;
		}
		else{
			while($chrow=mysqli_fetch_assoc($cresult)){
				$chid[]=$chrow["id"];
			}
			foreach($chid as $key){
			$getleads = "select * from leads where chapter_id = '$key' and expiry_date >'$date_today' and lead_type='Sell'";
			
			$getres = mysqli_query($conn,$getleads);
			if($getres){
				while($rows=mysqli_fetch_assoc($getres)){
					$outp[]=$rows;
					}
				}
			}
		}
	}
	else{
		$hsql = "SELECT * from testhsncodes where english LIKE '%$search_value%' and level=4";
		$hresult =mysqli_query($conn,$hsql);
		if(mysqli_num_rows($hresult) <= 0){
			$outp=2;
		}
		else{
			while($hrow=mysqli_fetch_assoc($hresult)){
				$hid[]=$hrow["hscode"];
			}
			foreach($hid as $key){
			$getleads = "select * from leads where hsn_id = '$key' and expiry_date >'$date_today' and lead_type='Sell'";
			
			$getres = mysqli_query($conn,$getleads);
			if($getres){
				while($rows=mysqli_fetch_assoc($getres)){
					$outp[]=$rows;
					}
				}
			}
		}
	}
   
		
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>