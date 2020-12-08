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

    $user_id = $request->user_id;
    $other_id = $request->other_id;
    $chatroom = $request->group_name;
    
    $outp = count($other_id);
    $chkchatroom = "select * from chatrooms where chatroom='$chatroom' ";
    $res = mysqli_query($conn,$chlchatroom);
    if(mysqli_num_rows($res)>0){
        $outp = "Room already created";
    }
    else{



        $chkw = "select * from wallet where user_id='$user_id'";
        $resw = mysqli_query($conn,$chkw);
        if($resw){
            while($row= mysqli_fetch_assoc($resw)){
                $credits = $row["credits"];
            }

            if($credits<=0){
                $outp="insufficient Credits";
            }else{
                $bcredits = $credits-1;
                $updw = "update wallet set credits='$bcredits' where user_id='$user_id'";
                $resupd = mysqli_query($conn,$updw);
                if($resupd){
                    foreach($other_id as $key => $value)
                    {
                        $chapters[$key] = $value->user_id;
                        $val =  $value;
                        
                        $create = "insert into group_chatrooms (chatroom,user_id,created_by,status) values ('$chatroom','$val','$user_id','1')";
                        $rescreate = mysqli_query($conn,$create);
                        if($rescreate){
                            
                              $outp=1;
                			
                            }
                            else{
                                $outp = 0;
                            }
                    }
                }else{
                    $outp = 0;
                }

            }

        }



    }
        
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>
