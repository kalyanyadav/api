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
	//$lead_id = $request->lead_id;
	$asd = array();
	$country_id = $_GET['country_id'];
	$mobile = $_GET['mobile'];
	
	$getchapters = "select * from franchise_users where mobile='$mobile'";
	$reschapters = mysqli_query($conn,$getchapters);
	if(mysqli_num_rows($reschapters)>0){
	    while($grow = mysqli_fetch_assoc($reschapters)){
	        $chapter_id[]=$grow['chapter_id'];
	        
	    }
	    //print_r($chapter_id);
	}
	foreach($chapter_id as $val){
	     $sql="select * from franchise_request where country_id ='$country_id' and chapter='$val' and status=0 group by user_id";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
            while ($row = mysqli_fetch_array($result)) {
            $ruid[] = $row['user_id'];
			$rid []= $row['id'];
									
	}
		}
		foreach($ruid as $key){
			$getusers = "select u.name, u.id as user_id, u.business_name, fr.id, fr.franchise_type_id, fr.description, fr.chapter, fr.request_date from users u, franchise_request fr where u.id='$key' and fr.user_id=u.id";
			$getres = mysqli_query($conn,$getusers);
			if($getres){
				while($rows=mysqli_fetch_assoc($getres)){
					$outp[]=$rows;
			}
				}
			else{
			$outp=0;
		}
			
		}
		}
	    
	
       
		
		
	$outp = json_encode($outp);
	echo $outp;
	$conn->close();

?>
