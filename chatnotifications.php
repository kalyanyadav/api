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
	include("fcmpush.php");
		$user_id=$request->user_id;
		$chatroom = $request->chatroom;
		$message = $request->message;
		$title = "Message From Exim BNI User";
		
		$getuser = "select * from chatrooms where chatroom='$chatroom' and user_id!='$user_id'";
		$resuser = mysqli_query($conn,$getuser);
		if(mysqli_num_rows($resuser)>0){
			while($row = mysqli_fetch_assoc($resuser)){
				$other_id[] = $row["user_id"];
			}
			
		}
		
		//fcm Starts
		foreach($other_id as $val){
			$sql1 = "select * from users where id='$val' and device_id !=''";
						$res1 = mysqli_query($conn,$sql1);
						$count = mysqli_num_rows($res1);
							if($count >0){
							while ($row = mysqli_fetch_assoc($res1)) {
								$id[]=$row['device_id']; 
							}
							$outp=1;
							}
							//print_r($id);
                               	fcm($message,$id,$title);
		}
			
			
			// FCM Ends;
			echo $outp;
	
	$conn->close();
	
?> 