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
	  
	    $webinar_id =$request->webinar_id;
	    $update_id =$request->update_id;
	    $updated_datetime = date('Y-m-d H:i:00');
	    
			$chekwebinar = "select * from webinar where id='$webinar_id'";
        	$reschk = mysqli_query($conn,$chekwebinar);
        	
	       if($reschk){
	           
	           	if($update_id ==1){
	           		$updwebinar = "update webinar set webinar_start='$updated_datetime' WHERE id='$webinar_id'";
	           	}elseif($update_id ==2){
	           		$updwebinar = "update webinar set webinar_end='$updated_datetime', status = '2'  WHERE id='$webinar_id'";
	           	}else{
	           		$updwebinar = "update webinar set status = '3' WHERE id='$webinar_id'";
	           	}
    	        
    	        $updres = mysqli_query($conn,$updwebinar);
    	        
    	        if($updres){
    	            
                    $outp= 1;
    	       	}else{
    	       		$outp = $updwebinar;
    	       	}
    	        
	         }
	        else{
	        
	             $outp = 0;
	        }
	    
			$outp = json_encode($outp);
			echo($outp);
	
			$conn->close();
	
?> 