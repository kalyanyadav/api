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
       	$id = $_GET['id'];
       	$sql="select l.*,u.uom, us.id as users_id, us.name, us.email, us.mobile, us.business_name, co.name as countryname from leads l, uoms u, users us, countries co where l.uom_id = u.id and l.posted_by=us.id and us.country_id=co.country_id and l.id ='$id'";
    //echo $sql;
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $outp[] = $row; 
 
}
    
		
		}
		else{
			$outp=0;
		}
		
    $outp=json_encode($outp,JSON_INVALID_UTF8_IGNORE);
		
		echo($outp);
	$conn->close();	
?>