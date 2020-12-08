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
	$chapter_id=$request->chapter_id;
	$country_id=$request->country_id;
$val = mysqli_query($conn, 'select 1 from hsncodes_'.$country_id.' LIMIT 1');
	if($val !== FALSE)
	{
	   	$table_name="hsncodes_".$country_id;
	}
	else
	{
	    $table_name="testhsncodes";
	}
			$sql = "select hscode,english,CONCAT('Chapter No:', chapter_id,' || HSCode:',hscode,' || ', english ) AS MyColumn from $table_name where  chapter_id = '$chapter_id'";
		
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $arr[] = $row; 
}
    $outp= json_encode($arr, JSON_INVALID_UTF8_IGNORE);
		
		}
         
		else{
			$outp= 0;
		}
         	

		
		echo($outp);
	$conn->close();	
?>