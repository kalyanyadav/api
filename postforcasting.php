
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
	
	    $user_id=$request->user_id;
	    $franchise_id =$request->franchise_id;
	    $month =$request->month;
	    $forcast =$request->forcast;
	    
		$chkotp="select * from franchise_forcasting where month='$month' and forcast = '$forcast' and franchise_id = '$franchise_id' and user_id = '$user_id' and status = 1";
        $chkres = mysqli_query($conn,$chkotp);
	    $count = mysqli_num_rows($chkres);
	    if($count==0){
	        
	        $insuser = "INSERT INTO `franchise_forcasting` (`franchise_id`, `user_id`, `month`, `forcast`) VALUES ('$franchise_id', '$user_id', '$month', '$forcast')";
	        $insusr = mysqli_query($conn,$insuser);
	        if($insusr){
	          $outp=1;  
	        }else{
	          $outp=0;  
	        }
	    }
	    else{
	        
	        $outp=2;
	    }
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 