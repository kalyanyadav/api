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
    $outp  = array();
	include("config.php"); 
	$hs_code= $_GET["hscode"];
	$hscode = explode(',', $hs_code);
	$country_id = $_GET['country_id'];
	
	$val = mysqli_query($conn, 'select 1 from hsncodes_'.$country_id.' LIMIT 1');
	if($val !== FALSE)
	{
	   	$country_id="hsncodes_".$country_id;
	}
	else
	{
	    $country_id="";
	}
    //echo $country_id ;
			foreach($hscode as $val){
			    
				if($country_id){
					
					$sql1 = "select hscode from $country_id where hscode like '$val%' group by hscode";
					$result1 =mysqli_query($conn,$sql1);
					$count1 = mysqli_num_rows($result1);
					if($count1 >0 || $count1==0){
					//unset($outp);
						while ($row1 = mysqli_fetch_assoc($result1)) {
	 						$outp[] = $row1; 
						}

					}else{
						$outp=$sql1;
					}

				}else{

					$sql1 = "select hscode from testhsncodes where father='$val' and LENGTH(hscode) =6";
					$result1 =mysqli_query($conn,$sql1);
					$count1 = mysqli_num_rows($result1);
					if($count1 >0 || $count1==0){
			
						while ($row1 = mysqli_fetch_assoc($result1)) {
	 						$outp[] = $row1; 
						}

					}else{
						$outp="0";
					}
				}
				
			 	
			}
         
$outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);		 

		
		echo($outp);
	$conn->close();	
?>