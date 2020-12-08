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
	$country_id = $_GET['country_id'];
	$chapter_id = $_GET['chapter_id'];
	$table_name = "hsncodes_".$country_id;
      
	$chk_query = "SELECT * FROM information_schema.tables WHERE table_schema = 'eximbinc_eximbni' AND table_name = '$table_name' LIMIT 1";
	$res_query = mysqli_query($conn,$chk_query);
	$cnt_query = mysqli_num_rows($res_query);
	if($cnt_query==1)
	{
		$table_name = $table_name;
	}
	else
	{
		$table_name = 'testhsncodes';
	}
    //echo $table_name;
		if($chapter_id <='9'){
			$chapter_id = '0'.$chapter_id;
		}
		
        $sql = "select * from testhsncodes where father='$chapter_id'"; //echo "<br>";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0 || $count==0){
		unset($outp);
			while ($row = mysqli_fetch_assoc($result)) {
				//echo "<br>".$row["hscode"]." Chapterid : ".$valchapter_id;
 				$hscode[] = $row["hscode"]; 
			}
			//echo "<br>";
			foreach($hscode as $val){
				if($country_id!=''){
				$sql1 = "select hscode from $table_name where hscode like '$val%' group by hscode";
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
				else{
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
         
		}else{
			$outp=0;
		}
     
	$outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);		 

		
	echo($outp);
	$conn->close();	
?>