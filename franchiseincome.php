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

	$fr_id=$request->fr_id;

	$start_date = $request->start_date;

	$end_date = $request->end_date;


			$getleads = "select fa.*, u.name as username from frachise_accounts fa, users u where fa.payment_for='franchise deposit' and fa.franchise_id='$fr_id' and fa.payment_date >= '$start_date' and fa.payment_date <= '$end_date' and fa.user_id=u.id";

			$getres = mysqli_query($conn,$getleads);

			if($getres){

				while($rows=mysqli_fetch_assoc($getres)){

					$outp[]=$rows;

					}

				}

			else{

			$outp="0";

		}

		$outp= json_encode($outp);

		echo($outp);

		$conn->close();

	

?>