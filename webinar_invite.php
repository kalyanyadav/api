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
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require '../PHPMailer/src/Exception.php';
	require '../PHPMailer/src/PHPMailer.php';
	//require 'PHPMailer/src/PHPMailerAutoload.php';
	require '../PHPMailer/src/SMTP.php';
	
	    $otp = mt_rand(1111,9999); 
	    $todaysdate = date("Y-m-d H:i:00");

	    $to_mail = "";
	    $user_name = $request->user_name;
	    $webinar_title=$request->webinar_title;
	    $start_date = $request->start_date;
	    $start_time =$request->start_time;
	    $duration = $request->duration;
	    $description = $request->description;
	    $webinar_id = $request->webinar_id;
	    $link = $request->link;
	    $meeting_id = $request->meeting_id;
	    $meeting_pass = $request->meeting_pass;
	    $users = $request->users;

	
		$message = "Your webinar is schedule on ".$start_date." ".$start_time.".<br>";
		$message .=	"<b>Webinar Details</b> <br>";
		$message .=	"Webinar Created By : ".$user_name."<br>";
		$message .= "Webinar Title : ".$webinar_title."<br>";
		$message .= "Webinar Date : ".$start_date."<br>";
		$message .= "Webinar Time : ".$start_time."<br>";
		$message .= "Webinar Duration : ".$duration."<br>";
		$message .= "Webinar Link : ".$meeting_id."<br>";
		$message .= "Webinar Link : ".$meeting_pass."<br>";
		$message .= "Webinar Link : ".$link."<br>";
		$message .= "Description : ".$description."<br>";
                //$to_mail = "";
		foreach($users as $val)
	    {
			$strexp = explode(",",$val);
			$invitee_id = $strexp[0];
			$invitee_email = $strexp[1];
 			
 			$sql = "insert into webinar_invitees (webinar_id,invitee_id,status) values('$webinar_id','$invitee_id','0')";
			$result =mysqli_query($conn,$sql);
			if($result){
				
				$chkusr="select * from users where name='$user_name'";
        	    $chkres = mysqli_query($conn,$chkusr);
        	    $count = mysqli_num_rows($chkres);
        	    if($count>0)
        	    {
        	        $row = mysqli_fetch_array($chkres);
				    $country = $row['country'];
				    $user_id = $row['id'];
				    
				    $title = "You have received webinar invitation from $user_name ";
				    
    				$email    = 'noreply@eximbni.com';
                    $password = '@team&1234';
            		$to_email = $invitee_email;
            		$message = $message;
            		$subject = $title;
            			
            		 $mail = new PHPMailer(); // create a new object
                    $mail->IsSMTP(); // enable SMTP
                    $mail->SMTPDebug  = 0; // debugging: 1 = errors and messages, 2 = messages only
                    $mail->SMTPAuth   = true; // authentication enabled
                    $mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
                    $mail->Host       = "mail.eximbni.com";
                    $mail->Port       = 587; // or 587
                    $mail->IsHTML(true);
                    $mail->Username = $email;
                    $mail->Password = $password;
                    $mail->SetFrom($email);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->AddAddress($to_email);
                    $mail->Send();
    				$outp = 1;

				    
    				$ins_inbox= "INSERT INTO `inbox` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', '$title', '$message', '1', '$todaysdate')";
            		$result_inbox = mysqli_query($conn,$ins_inbox);
            		
            		$ins_unotify= "INSERT INTO `user_notification` (`user_id`, `country_id`, `title`, `notification`, `type`, `created`) VALUES ('$user_id', '$country', '$title', '$message', '1', '$todaysdate')";
            		$result_unotify = mysqli_query($conn,$ins_unotify);
        	    }

			}else{
				$outp = 0;
			}

	  }

	$outp = json_encode($outp);
	echo($outp);
	
	$conn->close();
	
?> 