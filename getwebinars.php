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

	//session_start();

	include("config.php");

	date_default_timezone_set('Asia/Kolkata');

	/*$token = $_SESSION['$token'];

	$auth = "select * from auth_table where token = '$token'";

	$res_auth = mysqli_query($conn,$auth);

	if(mysqli_num_rows($res_auth) <= 0){

		$outp= "You are not authoriserd to view this";

	}

	else{*/

	$date = date("Y-m-d");

	$user_id = $_GET["user_id"];



        $sql = "SELECT * from webinar where webinar_date >= '$date' AND posted_by='$user_id' AND status < '3' AND `webinar_end` IS NULL";

        //$sql = "SELECT webinar.* FROM `webinar_invitees` JOIN `webinar` WHERE `webinar_invitees`.`status` = '0' AND `webinar`.`status` = '0' AND (`webinar`.`posted_by` ='$user_id' OR `webinar_invitees`.`invitee_id` ='$user_id') AND `webinar`.`webinar_date` >= '$date'";

		$result =mysqli_query($conn,$sql);

		$count = mysqli_num_rows($result);

		if($count >0){

		

			while ($row = mysqli_fetch_assoc($result)) {

			 $outp[] = $row; 

			}

   

		

		}

		else{

			$outp= 0;

		}

	

	$outp = json_encode($outp);

		

		echo($outp);

	$conn->close();

?>