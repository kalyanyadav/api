<?php 
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache for 1 day
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
	$user_id=$_GET["user_id"];
	$hsn_code = $_GET["chapter_id"];
	$todaysdate = date("Y-m-d");
	$myObj = array('');
	
	    $sql1 = "SELECT * from users as u , `subscription_chapter` as s  WHERE s.user_id = u.id AND s.`chapter_id` = '$hsn_code' AND u.id != '$user_id' AND u.status='1' group by s.user_id";
	    $res1 = mysqli_query($conn,$sql1);
	    $active_users = mysqli_num_rows($res1);
	    
	    $sql = "SELECT * from users as u , `subscription_chapter` as s  WHERE s.user_id = u.id AND s.`chapter_id` = '$hsn_code'  AND u.id != '$user_id' AND u.status='1' AND u.chat_status ='online' group by s.user_id";
	    $res = mysqli_query($conn,$sql);
	    $online_users = mysqli_num_rows($res);	
	    
        //$myObj->active_users = $active_users;
        //$myObj->online_users = $online_users;
	    
	    $myObj['active_users'] = $active_users;
	    $myObj['online_users'] = $online_users;
	    
		$outp= json_encode($myObj);
		echo($outp);
		$conn->close();
	
?>
