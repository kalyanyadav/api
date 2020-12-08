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
	$chapter_id=$_GET["chapter_id"];
	$date = date("Y-m-d");
	$country_id = $_GET["country_id"];
	if($chapter_id<10){
	    $chapter_id = "0".$chapter_id;
	}
	else{
	    $chapter_id=$chapter_id;
	}
	$val = mysqli_query($conn, 'select 1 from hsncodes_'.$country_id.' LIMIT 1');
	if($val !== FALSE)
	{
	   	$table_name="hsncodes_".$country_id;
	}
	else
	{
	    $table_name="testhsncodes";
	}

        $sql = "select * from $table_name where hscode like '$chapter_id%' and LENGTH(hscode) >=6 ";
       	$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $hsncode[] = $row["hscode"]; 
}
//print_r($hsncode);
		}
		foreach($hsncode as $val){
	$query = "select *,(select count(*) from leads where hsn_id='$val' and status=1 and posted_by IN (SELECT id FROM users WHERE status='1' ) and expiry_date>='$date' and lead_type='Sell')as sellleads,(select count(*) from leads where hsn_id='$val' and status=1 and posted_by IN (SELECT id FROM users WHERE status='1' ) and expiry_date>='$date')as leads_count,(select count(*) from leads where hsn_id='$val' and status=1 and expiry_date>='$date' and lead_type='Buy')as buyleads from $table_name where hscode='$val' GROUP BY id ORDER BY id DESC ";
	$response = mysqli_query($conn,$query);
	if($response){
		while($row=mysqli_fetch_assoc($response)){
			$outp[]=$row;
		
		}
	}
}

       
	//	$outp = arsort($outp);
         
$outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);		 

		
		echo($outp);
	$conn->close();	
?>