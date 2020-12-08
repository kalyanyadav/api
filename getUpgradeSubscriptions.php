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
	
		$user_id = '19'; //$request->lead_id;
		$pre_subscriptions_id = '1';
		$upgrade_subscriptions_id = '2';
		$chaptersarray = array('2','4','6');
		
		$upgrade_startdate = date('Y-m-d H:m:s');
		$upgrade_enddate = date('Y-m-d H:m:s', strtotime('+1 years'));

		// To protect MySQL injection for Security purpose
		if($user_id !='' && $pre_subscriptions_id !=''){
			$selwallet = "SELECT `w`.`credits` as `precredits` FROM `wallet` as `w` WHERE `w`.`user_id` = '$user_id' and `w`.`subscription_id` = '$pre_subscriptions_id'";
			$querywallet = mysqli_query($conn, $selwallet);
			$reswallet = mysqli_fetch_array($querywallet);
			$walletprecredits = $reswallet['precredits'];
			
		}

		if($upgrade_subscriptions_id !=''){
			$selsubs = "SELECT `s`.`credits` as `upgradecredits` , `s`.`plan_name` as `plan_name`,`chapters` FROM `subscriptions` as `s` WHERE `s`.`id` = '$upgrade_subscriptions_id'";
			$querysubs = mysqli_query($conn, $selsubs);
			$ressubs = mysqli_fetch_array($querysubs);
			$subsupgradeplan_name = $ressubs['plan_name'];
			$walletupgradecredits = $ressubs['upgradecredits'];
			$subschapters = $ressubs['chapters'];
			
				
		}	

		$upgrade_walletcredits = $walletupgradecredits + $walletprecredits;

		//mysqli_query($conn, "DELETE FROM `subscription_chapter` WHERE `subscription_chapter`.`user_id` = '$user_id'");


		for($i=0; $i< $subschapters;$i++){

			$chaptersid = $chaptersarray[$i];
			if($chaptersid !=""){
				$insertsubs_chap = "INSERT INTO `subscription_chapter` (`user_id`, `chapter_id`) VALUES ('$user_id', '$chaptersid')";
				// mysqli_query($conn, "INSERT INTO `subscription_chapter` (`user_id`, `chapter_id`) VALUES ('$user_id', '$chaptersid')");
				
			}
			
		}

		$updatewallet = "UPDATE `wallet` SET `credits` = '$upgrade_walletcredits',`subscription_id` = '$upgrade_subscriptions_id',`wallet_start` = '$upgrade_startdate' , `wallet_end` = '$upgrade_enddate' ";
		// $chkresponse = mysqli_query($conn, "UPDATE `wallet` SET `credits` = '$upgrade_walletcredits',`subscription_id` = '$upgrade_subscriptions_id',`wallet_start` = '$upgrade_startdate' , `wallet_end` = '$upgrade_enddate' ");

		
/*
	    //$chkresponse = mysqli_query($conn,$insertresponse);
	    if($chkresponse){
	        $outp="1";
	    }
	    else{
			$outp="0";
	    }*/
        $outp = json_encode($updatewallet);
		
		echo($outp);
	
		
		$conn->close();
?> 