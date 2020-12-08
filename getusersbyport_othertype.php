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
/*
  $port_code = "VAAH"; //$request->port_code;

  $type_id = "2";//$request->type_id;*/

  $port_code = $request->port_code;

  $type_id = $request->type_id;


      $getsql = "SELECT user_id FROM `user_portdetails` where port_code ='$port_code' AND status='1' GROUP BY user_id";

      $getresult =mysqli_query($conn,$getsql);

      $getcount = mysqli_num_rows($getresult);



      if($getcount > 0){

        while ($portrow = mysqli_fetch_assoc($getresult)) {

          $portusers[] = $portrow['user_id']; 

        }   
  //print_r($portusers);
         foreach($portusers as $val){
    
            if($val){
            $sqlusers = "SELECT * FROM users WHERE user_specification like '%$type_id%' AND id='$val'";
			$resultusers =mysqli_query($conn,$sqlusers);
			$getcountusers = mysqli_num_rows($resultusers);

  
      if($getcountusers > 0 ){
    //echo $sqlusers."<br>";
              while ($row = mysqli_fetch_assoc($resultusers)) {

               $outp[] = $row; 

              }

      } 

      }
        } 

      }else{

        $outp = 0;

      }

  $outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);    

 echo($outp);

  $conn->close(); 



?>