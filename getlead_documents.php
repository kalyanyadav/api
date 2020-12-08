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
	$lead_id = $_GET["lead_id"];
	$user_id = $_GET["user_id"];
        $sql = "SELECT doc_name,doc_path FROM `lead_documents` WHERE lead_id='$lead_id' AND user_id='$user_id' AND status='1'";
		$result = mysqli_query($conn,$sql);
        $count = mysqli_num_rows($result);
		if($count >0){
            while ($row = mysqli_fetch_assoc($result)) {
                //print_r ($row);
                $outp[]= $row; 
            }
		}else{
		   $outp =0; 
		}
        $outp= json_encode($outp);
		
		echo $outp;
		
	$conn->close();	

	
?> 