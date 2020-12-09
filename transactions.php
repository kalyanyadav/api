<?php

function log_function($transaction_name, $transaction_desc, $transaction_source, $transaction_user, $transaction_status)
    {
        include("config.php");
        
        preg_match("/iPhone|Android|iPad|iPod|webOS|Windows|Macintosh|Chrome OS/", $transaction_source, $matches);
        $os = current($matches);
        
        
        $t=time();
        $created = date("Y-m-d");
        $d = date("Y-m-d-h-i-s-");
        $unix = $d.$t;
        $transaction_id = $unix;
        $transaction_date = date('d-m-Y H:i:s a');
        $querry = "INSERT INTO `transaction_log` (`transaction_id`, `transaction_name`, `transaction_source`, `transaction_desc`, `transaction_user`, `transaction_status`, `transaction_date`,`created`) 
        VALUES ('$transaction_id', '$transaction_name', '$os', '$transaction_desc', '$transaction_user', $transaction_status, '$transaction_date','$created')";
        $result = mysqli_query($conn,$querry);
        
    }
    
?>