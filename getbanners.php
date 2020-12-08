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
	$user_id = $_GET['user_id'];
	$country_id = $_GET['country_id'];
    $respt = 0;
    
	
	$sqlbanner="select chapter_id from subscription_chapter where user_id='$user_id' AND status='1' ";
    $resquery = mysqli_query($conn,$sqlbanner);
    $bannercnt = mysqli_num_rows($resquery);
    if($bannercnt > 0){
        while($row = mysqli_fetch_array($resquery)){
            $chapter_id[] = $row['chapter_id'];
        }     
        

        foreach($chapter_id as $val){
		    
		    $get1 = "select b.banner_image from banner b, banner_display_countries bdc WHERE b.id = bdc.banner_id AND bdc.countries ='$country_id' AND bdc.chapter_id='$val' AND b.status='1' ";
    	    $res1 = mysqli_query($conn,$get1);
        	if($res1){
        		while($row1=mysqli_fetch_assoc($res1)){
        			$outp[]=$row1;
        		}
        		
        	}
        	
	    } 
	    
	    $respt = mysqli_num_rows($res1);
        
    }else{
        $respt = 0;
    }
    if($respt==0){ 
        $get = "select banner_image from banner where status='1' order by id ASC";
    	$res = mysqli_query($conn,$get);
    	if($res){
    		while($row=mysqli_fetch_assoc($res)){
    			$outp[]=$row;
    		}
    	}        
    }
	
	
   $outp = json_encode($outp);
		
		echo($outp);
	
		
		$conn->close();
?> 