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
	
		$compname = $request->compname;
		$userid = $request->userid;
		$address = $request->address;
	   	$query="update users set business_name='$compname',business_address='$address' where id='$userid'";
		//echo $query;
		$result = mysqli_query($conn,$query);
		if($result){
		    $sql = "SELECT * FROM users where id='$userid'";
		$result =mysqli_query($conn,$sql);
	
while ($row = mysqli_fetch_assoc($result)) {
 $outp[]= $row; 
}
		}
		else{
		    $outp=0;
		}
	    
		$outp = json_encode($outp);
		echo($outp);
	
	$conn->close();
	
?> 