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
    $hsn_id=$_GET["hsn_id"];
	$hsn_id = substr($hsn_id,0,6);
        /*$sql = "SELECT l.*, u.name,c.chapter_name, cg.category_name, uo.uom, co.name from leads l, users u, chapters c, categories cg, uoms uo, countries co where l.lead_type ='sell' and l.status = '0' and l.posted_by = u.id and l.hsn_id = h.id and l.chapter_id = c.id and l.categories_id = cg.id and l.product_id = p.id and l.uom_id = uo.id and l.country_id = co.country_id and l.country_id='$country_id'";*/
        //$sql="select l.*, p.product, u.uom, v.* from leads l, products p, uoms u, lead_display_countries v where l.product_id=p.id  and l.uom_id=u.id and l.lead_type = 'Sell' and l.leadref_id=v.leadref_id and  v.country_id = '$country_id' and l.posted_by !='$user_id' and l.hsn_id='$hsn_id'";
		//$sql= "select l.*,  u.uom from leads l,  uoms u,  subscription_chapter sc  where l.hsn_id like '$hsn_id%' and l.uom_id=u.id and l.lead_type = 'Sell' and l.posted_by != '$user_id' and sc.user_id = l.posted_by and l.status=1 and l.expiry_date>'$date_today' group by l.id";
        $sql = "select *,(select count(*) from myfav_hscodes where user_id='$user_id' and hscode=leads.hsn_id) as hsstate from leads where hsn_id like '$hsn_id%' and lead_type='Sell' and status=1 AND posted_by IN (SELECT id FROM users WHERE status='1')";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $arr[] = $row; 
}
    $outp= json_encode($arr,JSON_INVALID_UTF8_IGNORE);
		
		}
		else{
			$outp="0";
			//$outp=$sql;
		}
		
	$conn->close();
		
		echo($outp);
	
?>