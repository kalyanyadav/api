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
	$country_id=$_GET["country_id"];
	$user_id=$_GET["user_id"];
    $date_today = date("Y-m-d");
    
    $chkpck = "select * from users where id = '$user_id'";
    $rspck = mysqli_query($conn,$chkpck);
    if($rspck){
        while($crow = mysqli_fetch_assoc($rspck)){
            $pck_id = $crow["subscription_id"];
        }
    }
    
    if($pck_id==40){
       $getleads = "select l.*,u.uom,(select count(*) from myfav_hscodes where user_id='$user_id' and hscode=l.hsn_id) as hsstate, (select remind_date from lead_reminders where user_id='$user_id' and lead_id=l.id) as laterstate,v.country_id from leads l, uoms u,lead_display_countries v,  subscription_chapter sc where l.uom_id=u.id and l.lead_type = 'Buy' and v.country_id = '$country_id' and v.lead_id=l.id  and l.posted_by != '$user_id' and l.status=1 and l.expiry_date >= '$date_today' and l.id NOT IN (SELECT lead_id from purchased_leads where user_id = '$user_id')  AND l.posted_by IN (SELECT id FROM users WHERE status='1')  group by l.id";
		$getres = mysqli_query($conn,$getleads);
		if($getres){
			while($rows=mysqli_fetch_assoc($getres)){
				$outp[]=$rows;

/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Buy Leads Listed Sucesfully";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $user_id;
$$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */

				}
			}
    }
    else
    {
        //$sql="select l.*, p.product, u.uom, v.country_id from leads l, products p, uoms u, lead_display_countries v  where l.product_id=p.id  and l.uom_id=u.id and l.lead_type = 'Sell' and l.leadref_id=v.leadref_id and  v.country_id = '$country_id' and l.posted_by !='$user_id' and l.id NOT IN (SELECT lead_id from purchased_leads)";
		
		 $sql = "select * from subscription_chapter where user_id = '$user_id'";
		
		
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
			while ($row = mysqli_fetch_assoc($result)) {
			$chapter_id[] = $row['chapter_id'];
				 //print_r ($row);
			}
			
			
			foreach($chapter_id as $key){
			    
			//$getleads = "select l.*,u.uom, v.country_id from leads l, uoms u, lead_display_countries v, subscription_chapter sc  where l.uom_id=u.id and l.lead_type = 'Buy' and l.id=v.lead_id and l.chapter_id = '$key' and v.country_id = '$country_id' and l.posted_by != '$user_id' and sc.user_id = l.posted_by and l.status=1 and l.expiry_date>'$date_today' and l.id NOT IN (SELECT lead_id from purchased_leads where user_id = '$user_id') group by l.id ";
			 $getleads = "select l.*,u.uom,(select count(*) from myfav_hscodes where user_id='$user_id' and hscode=l.hsn_id) as hsstate, (select remind_date from lead_reminders where user_id='$user_id' and lead_id=l.id) as laterstate,v.country_id from leads l, uoms u,lead_display_countries v,  subscription_chapter sc where l.uom_id=u.id and l.lead_type = 'Buy' and l.chapter_id = '$key' and v.country_id = '$country_id' and v.lead_id=l.id  and l.posted_by != '$user_id' and l.status=1 and l.expiry_date >= '$date_today' and l.id NOT IN (SELECT lead_id from purchased_leads where user_id = '$user_id')  AND l.posted_by IN (SELECT id FROM users WHERE status='1')  group by l.id";
		      $getres = mysqli_query($conn,$getleads);
			if($getres){
			    while($rows=mysqli_fetch_assoc($getres)){
					$outp[]=$rows;
					}
					
				}
			}
		}
		else{
			$outp=0;
			
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to lsit Buy leads";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $user_id;
$$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End */
		}
    }
	
		$outp= json_encode($outp,JSON_INVALID_UTF8_IGNORE);
		echo($outp);
		$conn->close();
	
?>