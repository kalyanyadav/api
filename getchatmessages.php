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
	$chatroom = $_GET['chatroom'];
	$addchat = "select c.*,u.business_name from chatmessages c, users u  where c.chatroom='$chatroom' and c.posted_by = u.id";
	$res = mysqli_query($conn,$addchat);
	if($res){
	    while($row=mysqli_fetch_assoc($res)){
	        $outp[]=$row;
	    }
	}
	else{
	    $outp=$addchat;
	}
	$outp=json_encode($outp);
	echo $outp;
	
	?>