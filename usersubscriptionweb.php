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
	
		//$userid = $request->user_id;
		//$subscription_id = $request->pack_id;
		//$duration = $request->duration;
		//$country_id = $request->country_id;
		//$state_id = $request->state_id;
		
		$userid =$request->user_id;// '74';
		$subscription_id = $request->pack_id;//'45';
		$duration = $request->duration;// '30';
		$country_id = $request->country_id; //'99';
		$state_id = $request->state_id;//'1476';
		$credits = $request->credits;
		if($duration==30){
		    $credits = $credits/12;
		}
		else{
		    $credits=$credits;
		}
		$subscription_start = date("Y-m-d");
		$txn_date = date("d-m-Y");
		$subscription_end = date('Y-m-d', strtotime("+".$duration." days"));
 		$chapter_id =$request->chapters;
 		$hscodes_id =$request->hscode;
 		//echo count($chapter_id);
		/*echo $req = "userid : ".$userid." subscription_id : ".$subscription_id." duration: ".$duration." country_id: ".$country_id." state_id: ".$state_id." credits : ".$credits." subscription_start: ".$subscription_start." subscription_end".$subscription_end." chapter_id: ".count($chapter_id)." hscodes_id: ".count($hscodes_id)." chapters : ".count($chapters)." hscodes : ".count($hscodes);

		*/

	    $query="update users set subscription_id='$subscription_id', subscription_start='$subscription_start', subscription_end='$subscription_end' where id='$userid'";
		$result = mysqli_query($conn,$query);
		if($result){
		    //echo"success update <br>";
		}
		else{
		    //echo"fail to update <br>";
		}
		//update chapters for user

        for($chi = 0; $chi < count($chapter_id); $chi++){
        	$val = $chapter_id[$chi];

			$sqlc = "insert into subscription_chapter (user_id,chapter_id,status) values ('$userid','$val','1')";
			$resc = mysqli_query($conn,$sqlc);
			
			if($resc){
	        	$outp =1;
	    
			}else{
		   	 	$outp=0;
			}
        }

        //update Hscodes for user
/*		$hscodes_id =$request->hscodes;//'[{"id":"2","chapter_name":"Chapter 02"},{"id":"4","chapter_name":"Chapter 

		$hscodes = explode(',', $hscodes_id);

        for($hsci = 0; $hsci < count($hscodes_id); $hsci++){
        	$hscival = $hscodes_id[$hsci];

			$geths = "select * from testhsncodes where level=4 and father='$hscival'";
			$reshs = mysqli_query($conn,$geths);
			if($reshs){
				while($hrow=mysqli_fetch_assoc($reshs)){
					$hscodes2[]=$hrow["hscode"];
				}
			}
        }
*/

        for($hsci2 = 0; $hsci2 < count($hscodes_id); $hsci2++){
        	$hscival2 = $hscodes_id[$hsci2];

			$sqlc = "insert into subscription_hscodes (user_id,hsn_code,status) values ('$userid','$hscival2','1')";
			$resc = mysqli_query($conn,$sqlc);
			//echo $sqlc."<pre>";
			if($resc){
	        	$outp =1;
	 		}
			else{
		    	$outp=0;
			}

        }

        
        $credit = "insert into wallet (user_id,credits,subscription_id) values ('$userid','$credits','$subscription_id')";
	    $rescredit = mysqli_query($conn,$credit);  

		//get subscription amount
		$get_sub_amt = "select plan_cost from subscriptions where id='$subscription_id'";
		$res_sub_amt = mysqli_query($conn, $get_sub_amt);
		$row_sub_amt = mysqli_fetch_array($res_sub_amt);
		$sub_amount = $row_sub_amt['plan_cost'];

		//Add transaction
		$txn_id = rand(00000000,99999999);
		$admin_income = "INSERT INTO `admin_income`(`user_id`, `txn_amount`, `txn_type`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$userid','$sub_amount','amount','subscription','$txn_id','$txn_date','1')";
		$res_admin_income = mysqli_query($conn, $admin_income);	
		if($res_admin_income)
		{
			echo "Admin Income Inserted";
		}			
		else
		{
			echo "Not inserted";
			mysqli_error($conn);
		}
		
		
		$add_txn = "INSERT INTO `txn_table`(`user_id`, `txn_type`, `txn_amount`, `txn_for`, `txn_id`, `txn_date`, `status`) VALUES ('$userid','amount','$sub_amount','subscription','$txn_id','$txn_date','1')";
		$res_add_txn = mysqli_query($conn, $add_txn);	
		if($res_add_txn)
		{
			echo "Transaction Inserted";
		}			
		else
		{
			echo "Transaction Not inserted";
			mysqli_error($conn);
		}
		//Add transaction

	   //country Franchise Commission
	   
		$getpackamount = "select * from subscriptions where id='$subscription_id'";
		$respak = mysqli_query($conn,$getpackamount);
		if($respak){
		    while($rowpack = mysqli_fetch_assoc($respak)){
		        $pamount = $rowpack["plan_cost"];
				$chapters = $rowpack["chapters"];
		    }
			$damount = $pamount/$chapters;
			}
	    $cfrcode = "cf".$country_id;
	    $getcf = "select * from franchise_users where frcode='$cfrcode'";
	    $resgetcf = mysqli_query($conn,$getcf);
	    if(mysqli_num_rows($resgetcf)>0){
	        while($rowcf=mysqli_fetch_assoc($resgetcf)){
	            $franchise_id=$rowcf["id"];
	            $frcommission = $rowcf["commission"];
	        }
	       
	        $cfamount = $pamount*$frcommission/100;
	        $payment_date = date("Y-m-d");
	        $cfcom = "insert into frachise_accounts (franchise_id, user_id,amount,payment_for,payment_date,status) values ('$franchise_id','$userid','$cfamount','subscription','$payment_date','0')";
	        $rscf = mysqli_query($conn,$cfcom);
			
			$getcf_wallet = "select * from franchise_wallet where franchise_id = '$franchise_id' ";
			$res_cfwallet = mysqli_query($conn,$getcf_wallet);
			if(mysqli_num_rows($res_cfwallet)>0){
				while($row_cf_wallet= mysqli_fetch_assoc($res_cfwallet)){
					$wallet_amount = $row_cf_wallet["wallet"];
				}
				$cf_new_amount = $wallet_amount+$cfamount;
				$upd_cf_wallet = "update franchise_wallet set wallet='$cf_new_amount' where franchise_id = '$franchise_id'";
				$res_upd_cf_wallet = mysqli_query($conn,$upd_cf_wallet);
				
							
			}
			else{
				$cf_wallet = "insert into franchise_wallet (franchise_id, wallet, status) values ('$franchise_id','$cfamount','1')";
				$res_cf_wallet = mysqli_query($conn,$cf_wallet);
			}
			
	        
	    }
		// Country Chapter Franchise
		 foreach ($chapter_id as $val){
		    $ccfrcode = "ccf".$country_id.$val;
		   $getccf = "select * from franchise_users where frcode='$ccfrcode' ";
	    $resgetccf = mysqli_query($conn,$getccf);
	    if(mysqli_num_rows($resgetccf)>0){
	        while($rowccf=mysqli_fetch_assoc($resgetccf)){
	            $ccfranchise_id[]=$rowccf["id"];
	            $ccfrcommission[]= $rowccf["commission"];
	        }
        }
         }
	       foreach (array_combine($ccfranchise_id, $ccfrcommission) as $ccfr_id => $ccfrcom){
			   $ccfamount = $damount*$ccfrcom/100;
	        $payment_date = date("Y-m-d");
	        $ccfcom = "insert into frachise_accounts (franchise_id,user_id,amount,payment_for,payment_date,status) values ('$ccfr_id','$userid','$ccfamount','subscription','$payment_date','0')";
	        $rccf = mysqli_query($conn,$ccfcom);
	        
			//ccF Wallet Update
			$getccf_wallet = "select * from franchise_wallet where franchise_id = '$ccfr_id'";
			$res_ccfwallet = mysqli_query($conn,$getccf_wallet);
			if(mysqli_num_rows($res_ccfwallet)>0){
				while($row_ccf_wallet= mysqli_fetch_assoc($res_ccfwallet)){
					$ccfwallet_amount = $row_ccf_wallet["wallet"];
				}
				$ccf_new_amount = $ccfwallet_amount+$ccfamount;
				$upd_ccf_wallet = "update franchise_wallet set wallet='$ccf_new_amount' where franchise_id = '$ccfr_id'";
				$res_upd_ccf_wallet = mysqli_query($conn,$upd_ccf_wallet);
				
			}
			else{
				$ccf_wallet = "insert into franchise_wallet (franchise_id, wallet, status) values ('$ccfr_id','$ccfamount','1')";
				$res_ccf_wallet = mysqli_query($conn,$ccf_wallet);
			}
		   }
	        
			
		 // State Chapter Franchise
	    foreach ($chapter_id as $sval){
		 $sfrcode = "sf".$country_id.$state_id.$sval;
		   $getsf = "select * from franchise_users where frcode='$sfrcode'";
	    $resgetsf = mysqli_query($conn,$getsf);
	    if(mysqli_num_rows($resgetsf)>0){
	        while($rowsf=mysqli_fetch_assoc($resgetsf)){
	            $sfranchise_id[]=$rowsf["id"];
	            $sfrcommission[]= $rowsf["commission"];
	        }
        }
        }
	       foreach (array_combine($sfranchise_id, $sfrcommission) as $sfr_id => $sfrcom){
	        $sfamount = $damount*$sfrcom/100;
	        $payment_date = date("Y-m-d");
	        $sfcom = "insert into frachise_accounts (franchise_id,user_id,amount,payment_for,payment_date,status) values ('$sfr_id','$userid','$sfamount','subscription','$payment_date','0')";
	        $rssf = mysqli_query($conn,$sfcom);
	        
			//SF Wallet Update
			$getsf_wallet = "select * from franchise_wallet where franchise_id = '$sfr_id'";
			$res_sfwallet = mysqli_query($conn,$getsf_wallet);
			if(mysqli_num_rows($res_sfwallet)>0){
				while($row_sf_wallet= mysqli_fetch_assoc($res_sfwallet)){
					$sfwallet_amount = $row_sf_wallet["wallet"];
				}
				$sf_new_amount = $sfwallet_amount+$sfamount;
				$upd_sf_wallet = "update franchise_wallet set wallet='$sf_new_amount' where franchise_id = '$sfr_id'";
				$res_upd_sf_wallet = mysqli_query($conn,$upd_sf_wallet);
				
			}
			else{
				$sf_wallet = "insert into franchise_wallet (franchise_id, wallet, status) values ('$sfr_id','$sfamount','1')";
				$res_sf_wallet = mysqli_query($conn,$sf_wallet);
			}
		   }
	   
	    $outp = json_encode($outp);
	    echo $outp;
	
	$conn->close();
	
?>
