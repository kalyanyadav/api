<?php

    if(isset($_SERVER['HTTP_ORIGIN'])) {
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
	date_default_timezone_set('Asia/Kolkata');
	$date = date("Y-m-d");
	$user_id = $_GET["user_id"];

        $sql = "SELECT web.id, web.title,web.meeting_id, web.webinar_date, web.webinar_time, web.webinar_start, web.webinar_end, web.status, web.description,(SELECT count(id) FROM joined_meeting WHERE joined_meeting.webinar_id = web.id ) as joinCount,(SELECT count(id) FROM `webinar_invitees` WHERE webinar_invitees.webinar_id = web.id) as inviteCount from webinar web where posted_by='$user_id' AND status > 1 ORDER BY web.id DESC";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
			while ($row = mysqli_fetch_assoc($result)) {
			 $outp[] = $row; 
            }
        }else{
			$outp= 0;
		}
        
        $outp = json_encode($outp);
        echo($outp);
    	$conn->close();

?>