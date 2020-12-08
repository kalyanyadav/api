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
	$hscodes = $_GET["hscodes"];
	$country_id = $_GET["country_id"];
	
		$hscodes = substr($hscodes,0,6);
	

        	   	$tablename="hsncodes_".$country_id;
                
                $sql = "SELECT h.exp_policy, h.imp_policy, c.name as country, c.country_id as country_id FROM $tablename h, countries c WHERE c.country_id='$country_id' and h.hscode like '$hscodes%'";
				$result =mysqli_query($conn,$sql);
				$count = mysqli_num_rows($result);
				if($count >= 0 ){
			        while($row1 = mysqli_fetch_assoc($result)){
			            $outp[] = $row1; 
			        }
				}else{
    			    $outp = 0; 
				}        	   	

    	
    //print_r($outp);
		
	$outp= json_encode($outp,JSON_INVALID_UTF8_IGNORE);
	echo($outp);
	$conn->close();
	
?>