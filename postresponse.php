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
	
		$firstname=$request->firstname;
		$lastname = $request->lastname;
	    $email = $request->email;
	    $telephone = $request->telephone;
	    $password = $request->password;
	    $customer_group_id = 1;
	    $store_id = 0;
	    $apartmen_name = $request->apartment_name;
	    $address1 = $request->address1;
	    $salt = substr(md5(uniqid(rand(), true)), 0, 9);
	    $password = sha1($salt . sha1($salt . sha1($password)));
	    $token = $request->token;
	    //echo$password . "<br>";
	    //echo $salt;
	    $date_added= date("Y-m-d H:i:s");
	    $ipaddress = $_SERVER['REMOTE_ADDR'];
		$query="INSERT INTO oc_customer (customer_group_id,store_id,firstname,lastname,email,telephone,password,salt,custom_field,ip,status,approved,safe,token,fax,date_added,apartment_name) values('1','0','$firstname','$lastname','$email','$telephone','$password','$salt','0','$ipaddress','1','1','1','$token','0','$date_added''$aprtment_name')";
		echo $squery;
		$result = mysqli_query($conn,$query);
		if($result){
		   $getuser = "select * from oc_customer order by id desc limit 1";
		   $getres  = mysqli_query($conn,$getuser);
		   if($getres){
		       while($row = mysqli_fetch_assoc($getres)){
		           $customer_id = $row["customer_id"];
		       }
		       $updaddress = "insert into oc_address (customer_id,firstname,lastname,address_1) values ('$customer_id','$firstname','$lastname','$address_1'";
		   $updres=mysqli_query($conn,$udpaddress);
		   if($updres){
		       $getcust_id="select * from oc_address order by id desc limit 1";
		       $cust_res = mysqli_query($conn,$getcust_id);
		       if($cust_res){
		           while($row = mysqli_fetch_assoc($updes)){
		           $address_id = $row["address_id"];
		       }
		       }
		       $updcust = "update oc_customer set address_id='$address_id' where customer_id=$customer_id";
		       $upd_res = mysqli_query($conn,$updcust);
		       if($upd_res){
		           $outp = 1;
		       }
		   }
		   }
		   
		}
		else{
			$outp=0;
		}
		
		echo($outp);
	
	$conn->close();
	
?> 