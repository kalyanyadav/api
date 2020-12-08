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
	$year = date("Y",strtotime("-1 years"));
	$yearmonth = date("Y-m",strtotime("-1 years"));
	$rangeDate = $yearmonth."-01";
	$country = $_GET['country_id'];
	
        $sql="SELECT YEAR(created) AS y, MONTH(created) AS m, COUNT(DISTINCT id) as chartPoint FROM users_1 WHERE YEAR(created) > '$year' GROUP BY y, m";
   		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
            while ($row = mysqli_fetch_assoc($result)) {
                if($row['m'] < 10){
                    $months = "0".$row['m'];
                }else{
                    $months = $row['m']; 
                }
                
             $arr[] = $row['y']."-".$months; 
            }
            
		}
		else{
			$arr="0";
		}
		 
		$sql11="SELECT YEAR(created) AS y, MONTH(created) AS m, COUNT(DISTINCT id) as chartPoint FROM users_1 WHERE YEAR(created) > '$year' AND `user_type` <> '' GROUP BY y, m";
   		$result11 =mysqli_query($conn,$sql11);
		$count11 = mysqli_num_rows($result11);
		if($count11 >0){
		
            while ($row11 = mysqli_fetch_assoc($result11)) {
             $arr11[] = $row11['chartPoint']; 
            }
            
		}
		else{
			$arr11="0";
		}
				 
		$sqlseller="SELECT YEAR(created) AS y, MONTH(created) AS m, COUNT(DISTINCT id) as chartPoint FROM users_1  WHERE YEAR(created) > '$year' AND `user_type` ='seller' and country_id =  $country GROUP BY y, m";
   		$resultseller =mysqli_query($conn,$sqlseller);
		$countseller = mysqli_num_rows($resultseller);
		if($countseller >0){
		
            while ($rowseller = mysqli_fetch_assoc($resultseller)) {
             $arrseller[] = $rowseller['chartPoint']; 
            }
            
		}
		else{
			$arrseller="0";
		}
		
				 
		$sqlbuyer="SELECT YEAR(created) AS y, MONTH(created) AS m, COUNT(DISTINCT id) as chartPoint FROM users_1 WHERE YEAR(created) > '$year' AND `user_type` ='buyer' and country_id =  $country GROUP BY y, m";
   		$resultbuyer =mysqli_query($conn,$sqlbuyer);
		$countbuyer = mysqli_num_rows($resultbuyer);
		if($countbuyer >0){
		
            while ($rowbuyer = mysqli_fetch_assoc($resultbuyer)) {
             $arrbuyer[] = $rowbuyer['chartPoint']; 
            }
            
		}
		else{
			$arrbuyer="0";
		}
		
				 
		$sqlboth="SELECT YEAR(created) AS y, MONTH(created) AS m, COUNT(DISTINCT id) as chartPoint FROM users_1 WHERE YEAR(created) > '$year' AND `user_type` ='both' and country_id =  $country GROUP BY y, m";
   		$resultboth =mysqli_query($conn,$sqlboth);
		$countboth = mysqli_num_rows($resultboth);
		if($countboth >0){
		
            while ($rowboth = mysqli_fetch_assoc($resultboth)) {
             $arrboth[] = $rowboth['chartPoint']; 
            }
            
		}
		else{
			$arrboth="0";
		}
		
				 
		$sqlother="SELECT YEAR(created) AS y, MONTH(created) AS m, COUNT(DISTINCT id) as chartPoint FROM users_1 WHERE YEAR(created) > '$year' AND `user_type` ='other' and country_id = $country  GROUP BY y, m";
   		$resultother =mysqli_query($conn,$sqlother);
		$countother = mysqli_num_rows($resultother);
		if($countother >0){
		
            while ($rowother = mysqli_fetch_assoc($resultother)) {
             $arrother[] = $rowother['chartPoint']; 
            }
            
		}
		else{
			$arrother="0";
		}
		
	$arrs = array("labels"=>$arr, "points" => $arr11, "seller" => $arrseller, "buyer" => $arrbuyer, "both" => $arrboth, "other" => $arrother );
//	print_r($arrs);
	$outp = json_encode($arrs);
			
	$conn->close();
		
		echo($outp);
	
?>