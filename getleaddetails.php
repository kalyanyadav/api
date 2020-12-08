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
	$lead_id = $_GET['lead_id'];
    //$sql = "SELECT * FROM leads where id = '$lead_id'";
    $sql = "select l.*, u.uom from leads l, uoms u where l.id = '$lead_id' and l.uom_id = u.id";
     $result=mysqli_query($conn,$sql);
      
      if($result){
       while ($row = mysqli_fetch_assoc($result)) {
         $outp[] = $row; 
        }
        $outp= json_encode($outp);
      }
		else{
			$outp="0";
		}
		
	$conn->close();
		
		echo($outp);
	
?> 