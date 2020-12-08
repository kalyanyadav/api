<?php
if (isset($_SERVER['HTTP_ORIGIN']))
{
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
    
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
include ("config.php");

    $lead_id = $request->id;
    $description = $request->description;
    $uom = $request->uom;
    $loading_port = $request->loading_port; 
    
    $destination_port = $request->destination_port;
    $port_type = $request->port_type;  
    $price_inusd = $request->price_inusd;
    $price_option = $request->price_option;
    $quantity = $request->quantity;
    $inspection_auth = $request->inspection_auth;
    $special_instruc = $request->special_instruc;
    $posted_date = date("Y-m-d H:i:s");
    
    
    $uqty = $request->uqty;
    if($uqty==''){
        $uqty=$quantity;
    }
    $uprice = $request->uprice;
    if($uprice==''){
        $uprice=$price_inusd;
    }
    $udescription = $request->udescription;
    if($udescription==''){
        $udescription=$description;
    }
    
    $uMyAuth = $request->uMyAuth;
    if($uMyAuth==''){
        $uMyAuth=$inspection_auth;
    }
    $uMyInst = $request->uMyInst;
    if($uMyInst==''){
        $uMyInst=$special_instruc;
    }

    $uMyRemark = $request->uMyRemark;

    $expiry_date = $request->expiry_date;

    
    $uloading_port = $request->uloading_port;

    if($uloading_port==''){
        $uloading_port = $loading_port;
    }else{
       $uloading_port = $uloading_port; 
    }
    

    $udestination_port = $request->udestination_port;
    if($udestination_port==''){
        $udestination_port = $destination_port;
    }else{
        $udestination_port = $udestination_port;
    } 
     
    $uMyDate = $request->uMyDate; 
    
    if($uMyDate !='undefined-undefined-undefined'){
        $uMyDate = $uMyDate;
    }else{
        $uMyDate = $expiry_date;
    }
    
    /*
    $currency = $request->currency;
    
    $leadref_id = $request->leadref_id;
    $uloading_country = $request->uloading_country; 
    $udestination_country = $request->udestination_country;
    
    */

     $sql_insert_lm = "INSERT INTO `lead_modifications` (`lead_id`, `description`, `uom`, `loading_port`, `destination_port`, `port_type`, `price`, `price_option`, `quantity`, `inspection_auth`, `special_instruc`, `expiry_date`,`created_date`) VALUES ('$lead_id', '$description', '$uom', '$loading_port', '$destination_port', '$port_type', '$price_inusd','$price_option', '$quantity', '$inspection_auth', '$special_instruc','$expiry_date','$posted_date' )";
     $qry_insert_lm = mysqli_query($conn, $sql_insert_lm);
     if($qry_insert_lm){
         
        $update_leads ="UPDATE leads SET lead_age='Modified Lead', modified_date = '$posted_date', quantity = '$uqty', price_inusd = '$uprice', description= '$udescription', loading_port ='$uloading_port', destination_port = '$udestination_port',inspection_auth = '$uMyAuth', special_instruc = '$uMyInst',expiry_date='$uMyDate', status='2'  WHERE id = '$lead_id'";
        $update_qry = mysqli_query($conn,$update_leads);
        if($update_qry){
            $outp = 1;
            
        }else{
           $outp = $update_leads; 
        }
         
        //$outp = 1; 
         
     }else{
         $outp = $sql_insert_lm;
     }
     
    $outp = json_encode($outp, JSON_INVALID_UTF8_IGNORE);
    echo $outp;
    
    $conn->close(); 

?>
