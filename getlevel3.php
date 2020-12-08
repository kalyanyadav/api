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
	$chapter_id = $_GET["chapter_id"];
	$getcat = "select * from chapters where id='$chapter_id'";
	$rescat = mysqli_query($conn,$getcat);
	if($rescat){
		while($row=mysqli_fetch_assoc($rescat)){
			$cat_id = $row["category_id"];
		}
	}
	
	if($chapter_id<10){
		$chapter_id="0".$chapter_id;
	}
	else{
		$chapter_id=$chapter_id;
	}
	if($cat_id<10){
		$cat_id="0".$cat_id;
	}
	else{
		$cat_id=$cat_id;
	}
	$hscode = $chapter_id;
	
	$level = "select * from testhsncodes where level=3 and father='$hscode'";
	$reslevel = mysqli_query($conn,$level);
	if(mysqli_num_rows($reslevel)>0){
		while($lrow=mysqli_fetch_assoc($reslevel)){
			$outp[]=$lrow;
			//$father[] = $lrow["hscode"];
			
		}
		/*foreach($father as $val)
		{
			$query = "select tc.*,(select count(*) from testhsncodes tc, leads l where tc.hscode = l.hsn_id and tc.father='$val'  and l.lead_type='Buy' and l.status=1 and l.expiry_date>='$date' ) as buyleads, (select count(*) from testhsncodes tc, leads l where tc.hscode = l.hsn_id and tc.father ='$val' and l.lead_type='sell' and l.status=1 and l.expiry_date>='$date') as sellleads  from testhsncodes tc, leads l where tc.hscode = l.hsn_id and tc.father='$val' group by tc.father";
			$response = mysqli_query($conn,$query);
			if($response){
				while($row=mysqli_fetch_assoc($response))
				{
					$outp[]=$row;
				}
			}
		}*/
	}
	else{
		$outp=0;
	}
	$outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);
		
	$conn->close();
		
		echo($outp);
	
?>
