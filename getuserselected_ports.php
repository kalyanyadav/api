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

  $email_id = $_GET['email'];


      $getsql = "SELECT up.user_id, u.name,u.mobile,u.user_specification as selected_othertype,up.port_code FROM user_portdetails up, users u where up.user_id = u.id AND u.email='$email_id' AND u.other_check='1' ";

      $getresult =mysqli_query($conn,$getsql);

      $getcount = mysqli_num_rows($getresult);



      if($getcount > 0){

        while ($portrow = mysqli_fetch_assoc($getresult)) {

           $outp[] =  $portrow;

        }   
  
      }else{

        $outp = 0;

      }



   

  $outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);    

 echo($outp);

  $conn->close(); 



?>