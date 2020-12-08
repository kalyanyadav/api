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
	    $today = date('Y-m-d');
        $user_id = $_GET["user_id"];
        $hscode = $_GET["hscode"];
        $ressql = mysqli_query($conn, "SELECT count(*) as cnt FROM `myfav_hscodes` WHERE user_id='$user_id' AND hscode='$hscode'");
        $resqur = mysqli_fetch_array($ressql);
        $sqlcount = $resqur['cnt'];
        if($sqlcount > 0){
           $outp=2; 
        }else{
            
            $sql = "insert into myfav_hscodes (user_id, hscode, crated_date) values ('$user_id','$hscode','$today')";
            $res = mysqli_query($conn,$sql);
            if($res){

                $message = "You are Successful added $hscode HSCode as Favorite in your favorite list ";
                
                $ins_inbox= "INSERT INTO `inbox` (`user_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', 'Successful added $hscode HSCode as Favorite.', '$message', '1', '$today')";
                $result_inbox = mysqli_query($conn,$ins_inbox);
                
                $ins_unotify= "INSERT INTO `user_notification` (`user_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', 'Successful added $hscode HSCode as Favorite.', '$message', '1', '$today')";
                $result_unotify = mysqli_query($conn,$ins_unotify);

                $outp=1;
                
/* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "HS Code Added to Favorites";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->mobile;
$transaction_status = 1;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */


            }
            else{
                $outp=0;
                
                /* Transaction Log Code start  */
require_once('transactions.php');
$transaction_name = basename($_SERVER['PHP_SELF']); 
$transaction_desc = "Failed to add HS Code to Favorites";
$transaction_source = $_SERVER['HTTP_USER_AGENT'];
$transaction_user = $request->mobile;
$transaction_status = 0;
log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status );
/* Transaction Log Code End  */
            }
        
        }

        $outp = json_encode($outp);
        echo $outp;
?> 