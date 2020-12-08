<?php 
if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache for 1 day
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
	
	$startdate = $_GET['startdate'];
	$enddate = $_GET['enddate'];
	$user_id = $_GET['user_id'];
	$franchise_id = $_GET['franchise_id'];	
	
	$sqlf = "SELECT * FROM franchise_users WHERE id ='$franchise_id' AND status='1'";
	$resf =  mysqli_query($conn, $sqlf);
	$rowf = mysqli_fetch_array($resf);
	$franchise_type = $rowf['franchise_type'];
	$continent_id = $rowf['continent_id'];
	$region_id = $rowf['region_id'];
	$f_country_id = $rowf['country_id'];
	$f_zone_id = $rowf['zone_id'];
	$f_state_id = $rowf['state_id'];
	$f_chapter_id = $rowf['chapter_id'];
	$condition ='';
	
	if($franchise_type =='CF'){
	    
	    //$condition = "AND country_id ='$f_country_id' ";
	    
        $getusers="select id, name, user_type, mobile,email,business_name, business_address,country_id,state_id,isfranchise,subscription_start from users where id != '$user_id' AND country_id ='$f_country_id'  AND subscription_start BETWEEN '".$startdate."' AND '".$enddate."'  group by id";
	    $resusers = mysqli_query($conn,$getusers);
	    if($resusers){
	        while($gros=mysqli_fetch_assoc($resusers)){
	             $outp[]=$gros;

/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Franchise Users  listed Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->user_id;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */

	        }
	   }else{
	      $outp=0;
	      
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Fialed to fetch Franchise Users list";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->user_id;
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
	      
	    }	    
	    
	}elseif($franchise_type =='CCF'){
	    
	    $sql = "select * from subscription_chapter where chapter_id='$f_chapter_id' group by user_id";
	    $res = mysqli_query($conn,$sql);
	    if(mysqli_num_rows($res)>0){
	        while($row=mysqli_fetch_assoc($res)){
	            $chapter_id[]=$row["user_id"];
	        }
	        foreach($chapter_id as $val){
	         // $getusers="select id, name, user_type, mobile,email,business_name,business_address,country_id,state_id,isfranchise,subscription_start from users where id='$val' and id != '$user_id' AND country_id ='$f_country_id' AND subscription_start >= '' AND subscription_start <= '$enddate' group by id";
	        echo "<br>".   $getusers ="select id, name, user_type, mobile,email,business_name,business_address,country_id,state_id,isfranchise,subscription_start from users where id='$val' and id != '$user_id' AND country_id ='$f_country_id' AND subscription_start BETWEEN '".$startdate."' AND '".$enddate."' group by id";
	            $resusers = mysqli_query($conn,$getusers);
	            if($resusers){
	                while($gros=mysqli_fetch_assoc($resusers)){
	                    $outp[]=$gros;
	                }
	            }
	            else{
	                $outp=0;
	            }
	        }
	    }	    
	    
	    
	    	    
	}elseif($franchise_type =='RF'){
	    
	    $condition = "AND country_id ='$f_country_id' ";
	    $outp=0;
	    	    
	}elseif($franchise_type =='RCF'){
	    
	    $condition = "AND country_id ='$f_country_id' ";
	    $outp=0;
	    	    
	}elseif($franchise_type =='SF'){
	    
	   // $condition = "AND country_id ='$f_country_id' ";
	    	    
        $getusers="select id, name, user_type, mobile,email,business_name, business_address,country_id,state_id,isfranchise ,subscription_start from users where id != '$user_id' AND state_id ='$f_state_id'  AND subscription_start BETWEEN '".$startdate."' AND '".$enddate."' group by id";
	    $resusers = mysqli_query($conn,$getusers);
	    if($resusers){
	        while($gros=mysqli_fetch_assoc($resusers)){
	             $outp[]=$gros;
	        }
	   }else{
	      $outp=0;
	    }	    	    
	    	    
	    	    
	}elseif($franchise_type =='SCF'){
	    
	    $sql = "select * from subscription_chapter where chapter_id='$f_chapter_id' group by user_id";
	    $res = mysqli_query($conn,$sql);
	    if(mysqli_num_rows($res)>0){
	        while($row=mysqli_fetch_assoc($res)){
	            $chapter_id[]=$row["user_id"];
	        }
	        foreach($chapter_id as $val){
	            $getusers="select id, name, user_type, mobile,email,business_name, business_address,country_id,state_id,isfranchise,subscription_start from users where id='$val' and id != '$user_id' AND state_id ='$f_state_id' AND subscription_start BETWEEN '".$startdate."' AND '".$enddate."' group by id";
	            $resusers = mysqli_query($conn,$getusers);
	            if($resusers){
	                while($gros=mysqli_fetch_assoc($resusers)){
	                    $outp[]=$gros;
	                }
	            }
	            else{
	                $outp=0;
	            }
	        }
	    }
	    	    
	}
	

        
	
		
		$outp= json_encode($outp);
		echo($outp);
		$conn->close();
	
?>