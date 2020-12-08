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
	   $sql = "select * from subscription_chapter where user_id='$user_id' ";
	   //echo $sql;
	    $res = mysqli_query($conn,$sql);
	    if(mysqli_num_rows($res)>0){
	        while($row=mysqli_fetch_assoc($res)){
	            $chapter_id[]=$row["chapter_id"];
	        }
			//print_r($chapter_id);
	        foreach($chapter_id as $val){
	            $getusers="select u.* from users u , subscription_chapter s  where s.chapter_id='$val' and s.user_id != '$user_id' and s.user_id=u.id group by u.id";
	           // $getusers="select u.*, s.* from users u , subscription_chapter s  where s.chapter_id='$val' and s.user_id != '$user_id' and s.user_id=u.id group by u.id";
	            //echo $getusers;
				$resusers = mysqli_query($conn,$getusers);
	            if($resusers){
	                while($gros=mysqli_fetch_assoc($resusers)){
	                    $outp[]=$gros;
	                }
	            }
	            else{
	                $outp=0;
	            }
	        }
	    }
        
	
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>
