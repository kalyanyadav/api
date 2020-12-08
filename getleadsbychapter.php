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
		$chapter_id = $_GET['chapter_id'];
		$lead_type = $_GET['lead_type'];
		
		if($chapter_id==0){
		    $chapter_condition = "";
		}else{
		   $chapter_condition = "l.chapter_id='$chapter_id' AND "; 
		}
		
		
        $sql = "SELECT l.id as id, l.leadref_id, l.lead_type, l.chapter_id, l.description, l.hsn_id, l.posted_date, l.expiry_date, l.quantity FROM leads l WHERE $chapter_condition l.lead_type='$lead_type' and l.status=1";
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
		
	$conn->close();
		
		echo($outp);
	
?>