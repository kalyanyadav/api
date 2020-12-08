<?php
#prep the bundle
function fcm($push_message,$id,$title){
	 foreach ($id as $ids) {
     $msg = array
          (
		'body' 	=> $push_message,
		'title'	=> $title
             	
          );
	$fields = array
			(
				'to'=> $ids,
				'notification'	=> $msg
			);
	
	
	$headers = array
			(
				'Authorization: key=' ."AAAA2GEXdK8:APA91bGb6NbrpaYhK_BYZ_rZi6YY-PneWYoxtSoaRVoImUOZdXKrgXvKVAMjXSU4uHK7hP6vEN8fH_abRPF6_Jblx0AQQVt0HoxQFjEXz_jE39jfqewb1R9HwptEW3WqzvuSOtxtzSnG",
				'Content-Type: application/json'
			);
			
#Send Reponse To FireBase Server	
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		$result= array($result);
		curl_close( $ch );
}
}

?>