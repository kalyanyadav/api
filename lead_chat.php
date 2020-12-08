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
    $chatroom = $user_id.$other_id;
    $rchatroom = $other_id.$user_id;
    $txn_date = date("Y-m-d");
    $todaysdate = date("Y-m-d H:i:00");
    
    $chkchatroom = "select * from chatrooms where chatroom='$chatroom' or chatroom='$rchatroom'";
    $res = mysqli_query($conn,$chkchatroom);
    if(mysqli_num_rows($res)>0){
        $outp = 2;
    }
    else{
  
          $create = "insert into chatrooms (chatroom,user_id,created_by,status) values ('$chatroom','$user_id','$user_id','1')";
          $rescreate = mysqli_query($conn,$create);
          if($rescreate){
             $create1 = "insert into chatrooms (chatroom,user_id,created_by,status) values ('$chatroom','$other_id','$user_id','1')";
             $rescreate1 = mysqli_query($conn,$create1);
            if($rescreate1){
              $outp=1;
            }
            else{
              $outp =$create1;
            }
          }
          
         
  }
  
    $outp= json_encode($outp);
    echo($outp);
    $conn->close();
  
?>
