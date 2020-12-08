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
    		//$getleads = "select l.*,u.uom, v.country_id from leads l, uoms u, lead_display_countries v, subscription_chapter sc  where l.uom_id=u.id and l.lead_type = 'Sell' and l.id=v.lead_id and l.chapter_id = '$key' and v.country_id = '$country_id' and l.posted_by != '$user_id' and sc.user_id = l.posted_by and l.status=1 and l.expiry_date>'$date_today' and l.id NOT IN (SELECT lead_id from purchased_leads where user_id = '$user_id') group by l.id ";
			$getleads = "select l.*,u.uom, hsn.exp_policy, hsn.imp_policy,(select count(*) from myfav_hscodes where user_id='$user_id' and hscode=l.hsn_id) as hsstate from leads l, uoms u,  subscription_chapter sc, hsncodes_$country_id hsn where l.hsn_id=hsn.hscode and l.uom_id=u.id and l.lead_type = 'Sell' and l.chapter_id = '$key' and l.country_id = '$country_id' and l.posted_by != '$user_id' and sc.user_id != l.posted_by and l.status=1 and l.expiry_date>'$date_today' and l.id NOT IN (SELECT lead_id from purchased_leads where user_id = '$user_id') group by l.id ";
			
			$getleads;
			$getres = mysqli_query($conn,$getleads);
			if($getres){
				while($rows=mysqli_fetch_assoc($getres)){
					$outp[]=$rows;
					}
				}
			}
		}
		else{
			$outp="0";
		}
		
		$outp= json_encode($outp,JSON_INVALID_UTF8_IGNORE);
		echo($outp); 
		
		$conn->close();
	
?>