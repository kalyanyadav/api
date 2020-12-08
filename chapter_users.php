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
	
	    $sql = "select * from subscription_chapter where chapter_id='$hsn_code' group by user_id";
	    $res = mysqli_query($conn,$sql);
	    if(mysqli_num_rows($res)>0){
	        while($row=mysqli_fetch_assoc($res)){
	            $chapter_id[]=$row["user_id"];
	        }
	        foreach($chapter_id as $val){
	            $getusers="select *, (SELECT count(*) FROM `leads` WHERE posted_by ='$val' AND posted_date >= DATE_SUB('$todaysdate', INTERVAL 1 MONTH)) as postedleads from users where id='$val' and id != '$user_id' and id not in(SELECT user_id  from chatrooms where user_id ='$val' and created_by='$user_id' ) group by id";
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
