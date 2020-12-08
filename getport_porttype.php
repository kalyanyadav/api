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
  $porttype = $request->port_type;
  $country_id = $request->country_id;

        foreach($porttype as $key => $value)
        {
            $port_type[$key] = $value->name;
        } 
 
     foreach($port_type as $val){
        
        if($country_id){
            $sql = "SELECT * FROM port WHERE country_id='$country_id' AND status='1' AND port_type='$val' ORDER BY port ASC ";
        }else{
           $sql = "SELECT * FROM port WHERE status='1' AND port_type='$val' ORDER BY port ASC "; 
        }
      
      $result =mysqli_query($conn,$sql);
      $count = mysqli_num_rows($result);
       if($result){
    
        while ($row = mysqli_fetch_assoc($result)) {
         $outp[] = $row; 
        }
        //print_r($hsncode);
      }
      else{
        $outp = 0;
      } 
    } 
         
  $outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);    
 echo($outp);
  $conn->close(); 

?>