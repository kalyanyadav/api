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
	include("fcmpush.php");
    $user_id = $request->user_id;
    $other_id = $request->other_id;
    $chatroom = $request->group_name;
    
    
    $chkchatroom = "select * from group_chatrooms where chatroom='$chatroom' ";
    $res = mysqli_query($conn,$chlchatroom);
    if(mysqli_num_rows($res)>0){
        $outp = "Room already created";
        
    }
    else{
        foreach($other_id as $key => $value)
        {
            $chapters[$key] = $value->id;
        } 
		
		$usercreate = "insert into group_chatrooms (chatroom,user_id,created_by,status) values ('$chatroom','$user_id','$user_id','1')";
        $userrescreate = mysqli_query($conn,$usercreate);

        foreach($chapters as $val){
        
        $create = "insert into group_chatrooms (chatroom,user_id,created_by,status) values ('$chatroom','$val','$user_id','1')";
        $rescreate = mysqli_query($conn,$create);
        if($rescreate){
            
              $outp=1;
              
                $getuser = "select * from user where id='$user_id'";
                $resuser = mysqli_query($conn,$getuser);
                if($resuser){
                    while($rows= mysqli_fetch_assoc($resuser)){
                        $country = $rows["country"];
                    }  
                }
                
                $getother = "select * from user where id='$other_id'";
                $resother = mysqli_query($conn,$getother);
                if($resother){
                    while($rowsother= mysqli_fetch_assoc($resother)){
                        $othername = $rowsother["name"];
                    }  
                }
                    
                    $message = "You have created group chat with" .$othername.""; 
                    $ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Chat Created Successfully.', '$message', '1', '$todaysdate')";
            		$result_inbox = mysqli_query($conn,$ins_inbox);
            		
            		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', 'Chat Created Successfully.', '$message', '1', '$todaysdate')";
            		$result_unotify = mysqli_query($conn,$ins_unotify);
            		
            		$pushmessage = $message;
            		$title ="New Chat Created";
            		$fcmid ="select * from users where id='$other_id' and device_id !='' and is_online !='1'";
                    $resid = mysqli_query($conn,$fcmid);
                    if($resid){
                        while($furow = mysqli_fetch_assoc($resid)){
                            $id[]=$furow["device_id"];
                            

                        }
                    }
                    fcm($push_message,$id,$title);
                    
                    $pushmessage = "You have created Chat with" .$username."";
            		$title ="New Chat Created";
            		$fcmid ="select * from users where id='$user_id' and device_id !='' and is_online !='1'";
                    $resid = mysqli_query($conn,$fcmid);
                    if($resid){
                        while($furow = mysqli_fetch_assoc($resid)){
                            $id[]=$furow["device_id"];
                        }
                    }
                    fcm($push_message,$id,$title);
			
            }
            else{
                $outp = 0;
            }
        }
    }
        
	
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>
