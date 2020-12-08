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
 	
//  	require_once('transactions.php');
//     $transaction_name = basename($_SERVER['PHP_SELF']); 	
//  	log_function($transaction_name, 'world');
 	
 	
	$postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

	include("config.php"); 
	$country_id = $_GET["country_id"];
	$date = date("y-m-d");
        $sql = "SELECT * from categories";
		$result =mysqli_query($conn,$sql);
		$count = mysqli_num_rows($result);
		if($count >0){
		
while ($row = mysqli_fetch_assoc($result)) {
 $category_id[] = $row["id"]; 
}
foreach($category_id as $val){
	$query = "select c.*, (select count(*) from leads where categories_id='$val' and lead_type='Buy' and status=1  and posted_by IN (SELECT id FROM users WHERE status='1' ) and expiry_date>='$date' )as buy_leads, (select count(*) from leads where categories_id='$val' and lead_type='sell' and status=1 and posted_by IN (SELECT id FROM users WHERE status='1' ) and expiry_date>='$date') as sell_leads from categories c, leads l where c.id='$val' group by c.category_name";
	$response = mysqli_query($conn,$query);
	if($response){
		while($row=mysqli_fetch_assoc($response)){
			$outp[]=$row;
		}
	}
}
    $outp= json_encode($outp);
		
		}
		else{
			$outp="0";
		}
		
	$conn->close();
		
		echo($outp);
	
?>