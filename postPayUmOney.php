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
 	


	 
	function getCallbackUrl()
	{
	  $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	 /* return  $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . 'response.php';*/
	  
	 return  $protocol.'eximbni.com/eximbniweb/response.php';
	 
	}

	$postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

	include("config.php"); 
	
	
	 $key = 'BxmWSQ2r';
	 $salt = '0ZpAk7JVG4';
	 $txnid = "Txn" . rand(10000,99999999);
	 $amount = '10'; //$request->amount;
	 $pinfo = 'P01,P02';
	 $fname = 'Santosh B'; //$request->fname; 
	 $email = 'santoshb@gmail.com'; //$request->email;
	 $mobile = '7777777777'; //$request->mobile;
	 $udf5 = 'BOLT_KIT_PHP7';
	 $hash= hash('sha512', $key.'|'.$xnid.'|'.$amount.'|'.$pinfo.'|'.$fname.'|'.$email.'|||||'.$udf5.'||||||'.$dsalt); 
	 $surl = getCallbackUrl();

?>
<script id="bolt" src="https://sboxcheckout-static.citruspay.com/bolt/run/bolt.min.js" bolt-
color="e34524" bolt-logo="http://boltiswatching.com/wp-content/uploads/2015/09/Bolt-Logo-e14421724859591.png"></script>



<script type="text/javascript">
$( document ).ready(function() {
    console.log( "ready!" );
    //var SearchInput = $('#email');
 launchBOLT();
//$('#mobile').focus();

});


function launchBOLT()
{
  bolt.launch({
  key: $('#key').val(),
  txnid: $('#txnid').val(), 
  hash: $('#hash').val(),
  amount: $('#amount').val(),
  firstname: $('#fname').val(),
  email: $('#email').val(),
  phone: $('#mobile').val(),
  productinfo: $('#pinfo').val(),
  udf5: $('#udf5').val(),
  surl : $('#surl').val(),
  furl: $('#surl').val(),
  mode: 'dropout' 
},{ responseHandler: function(BOLT){
  if(BOLT.response.txnStatus != 'CANCEL')
  {
    //Salt is passd here for demo purpose only. For practical use keep salt at server side only.
    var fr = '<form action=\"'+$('#surl').val()+'\" method=\"post\">' +
    '<input type=\"hidden\" name=\"key\" value=\"'+BOLT.response.key+'\" />' +
    '<input type=\"hidden\" name=\"salt\" value=\"'+$('#salt').val()+'\" />' +
    '<input type=\"hidden\" name=\"txnid\" value=\"'+BOLT.response.txnid+'\" />' +
    '<input type=\"hidden\" name=\"amount\" value=\"'+BOLT.response.amount+'\" />' +
    '<input type=\"hidden\" name=\"productinfo\" value=\"'+BOLT.response.productinfo+'\" />' +
    '<input type=\"hidden\" name=\"firstname\" value=\"'+BOLT.response.firstname+'\" />' +
    '<input type=\"hidden\" name=\"email\" value=\"'+BOLT.response.email+'\" />' +
    '<input type=\"hidden\" name=\"udf5\" value=\"'+BOLT.response.udf5+'\" />' +
    '<input type=\"hidden\" name=\"mihpayid\" value=\"'+BOLT.response.mihpayid+'\" />' +
    '<input type=\"hidden\" name=\"status\" value=\"'+BOLT.response.status+'\" />' +
    '<input type=\"hidden\" name=\"hash\" value=\"'+BOLT.response.hash+'\" />' +
    '</form>';
    var form = jQuery(fr);
   // jQuery('body').append(form);                
    form.submit();
  }

},
  catchException: function(BOLT){
   // alert( BOLT.message );
     console.log("Ganesh catchException : ", BOLT.message ); 
  }
});
}
//--
</script> 

<?php
	    $outp = json_encode($outps);
	    echo $outp;
	
	$conn->close();
	
?>
