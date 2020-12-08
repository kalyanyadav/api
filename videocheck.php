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
	$name=$request->name;
    $meeting_id = $request->meeting_id;
    $password = $request->password;
   $chkmeeting = "select * from webinar where meeting_id='$meeting_id' and meeting_pass='$password'";
    $res = mysqli_query($conn,$chkmeeting);
    if(mysqli_num_rows($res)==1){
        $checkstatus = "select * from webinar where meeting_id='$meeting_id' and meeting_pass='$password'and status=1";
        $reschk = mysqli_query($conn,$checkstatus);
        if(mysqli_num_rows($reschk)==1){
           $outp=1;
        }
        else{
            $outp=3;
        }
    }
    else{
       $outp=2;
    }
    
    $outp=json_encode($outp);
    echo $outp;
    ?>