<?php	
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
//require 'PHPMailer/src/PHPMailerAutoload.php';
require '../PHPMailer/src/SMTP.php';

/*	$email = 'slgsolutionsblr@gmail.com';
	$password = 'Mydarling07012016#';
	$to_email = 'vabtechno@gmail.com';
	$message = 'Test Message EximBin';
	$subject = 'Test EximBIN';
		
	$mail = new PHPMailer(); // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
	$mail->Host = "mail.google.com";
	$mail->Port = 25; // or 587
	$mail->IsHTML(true);
	$mail->Username = $email;
	$mail->Password = $password;
	$mail->SetFrom($email);
	$mail->Subject = $subject;
	$mail->Body = $message;
	$mail->AddAddress($to_email);
*/

            $email = 'noreply@eximbni.com';
            $password = '@team&1234';
            $to_email1 = 'ganesh2609y@gmail.com';
            $to_cc = 'ganesh.vab@gmail.com';
            $subject = "Post Lead OTP Verification";

            $mail = new PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "mail.eximbni.com";
            $mail->Port = 587; // or 587
            $mail->IsHTML(true);
            $mail->Username = $email;
            $mail->Password = $password;
            $mail->SetFrom($email);
            $mail->Subject = $subject;
            $mail->Body = $subject;
            $mail->AddAddress($to_email1);
            $mail->AddCC($to_cc);
	 if(!$mail->Send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	 } else {
		echo "Message has been sent";
	 }
?> 