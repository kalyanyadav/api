


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
	//$lead_id = $request->lead_id;
	
	//$country_id = $_GET['country_id'];
		$user_id = $request->user_id;
		$lead_id = $request->lead_id;
        
       $sql = "insert into purchased_leads(user_id,lead_id,status) values ('$user_id', '$lead_id','3')";
        $res = mysqli_query($conn,$sql);
        if($res){
            $outp=1;
        }
        else{
            $outp=0;
        }
		
	$outp = json_encode($outp);
	echo $outp;
	$conn->close();

?>
