<?php

$ipn_key = '';              // your IPN callback key

// get all the POST variables
$signature = $_POST['sig'];       // HMAC-SHA256 signature
$ref = $_POST['ref'];             // reference tag
$wallet = $_POST['wallet'];       // receiving wallet address
$amount = $_POST['amount'];       // amount
$fee = $_POST['fee'];             // fee
$currency = $_POST['currency'];   // currency
$txid = $_POST['txid'];           // blockchain transaction id
$zxid = $_POST['zxid'];           // ZixiPay transaction id
$time = $_POST['time'];           // time of transaction

$post = http_build_query($_POST);       // create http query out of POST variables
$payload = explode('&sig=', $post)[0];  // take the signture 


// check if the callback has a signature and the signature is correct
if ($signature) {
  if (hash_hmac('sha256', $payload, $ipn_key) != $signature)      // if the signature is wrong
    return FALSE;     // do nothing and return false or it could be anything that suites your platform
}

// here will be the code to do something on your platfrom with the above POST variables  

?>
