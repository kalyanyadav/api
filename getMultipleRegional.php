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
	$get_continents = $request->continent_id;
	
 	foreach($get_continents as $val){
	    
        $sql = "SELECT * FROM `continents` WHERE `main_continentid` ='$val' ORDER BY continent ASC";
		$result = mysqli_query($conn,$sql);
        while ($row = mysqli_fetch_assoc($result)) {
        //print_r ($row);
         $arr[]= $row; 
        }	    
	    
	}
	

        
        $outp= json_encode($arr);
		
		echo $outp;
		 
	$conn->close();	

	
?> 