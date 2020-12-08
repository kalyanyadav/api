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
	//$user_id=$_GET["user_id"];
        
		$sql = "select * from chatrooms where user_id ='$user_id'";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
			while ($row = mysqli_fetch_assoc($result)) {
			$chatroom[] = $row["chatroom"];
				 //print_r ($row);
			}
			foreach($chatroom as $val){
			    $getrooms = "select cht.*,u.id, u.business_name,u.user_image,(select count(*) from chatmessages where chatroom='$val' and status=0 and posted_by !='$user_id') as unread_messages from chatrooms cht, users u where cht.chatroom='$val' and cht.user_id !='$user_id' and cht.user_id=u.id group by cht.user_id";
			    $resget = mysqli_query($conn,$getrooms);
			    if($resget){
			        while($grow= mysqli_fetch_assoc($resget)){
			            $outp[]=$grow;
			        }
			    }
			}
					
		}
		else{
			$outp=0;
		}
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>
