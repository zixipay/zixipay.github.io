<?php

$ipn_key = '';      // enter your IPN callback key here, could be left blank if you don't want a signed IPN callback 

// get all the POST variables
$signature = $_POST['sig'];       // HMAC-SHA256 signature
$ref = $_POST['ref'];             // reference tag
$wallet = $_POST['wallet'];       // receiving wallet address
$amount = $_POST['amount'];       // amount received
$fee = $_POST['fee'];             // fee
$currency = $_POST['currency'];   // currency
$exchange = $_POST['exchange'];   // has auto-echange to USDZ been done?
$xamount = $_POST['xamount'];     // actual incoming payment amount if auto-exchange to USDZ has been done
$xcurrency = $_POST['xcurrency']; // actual incoming currency if auto-exchange to USDZ has been done
$xrate = $_POST['xrate'];         // applied exchange rate if auto-exchange to USDZ has been done
$txid = $_POST['txid'];           // blockchain transaction id
$zxid = $_POST['zxid'];           // ZixiPay transaction id
$time = $_POST['time'];           // time of transaction

$post = http_build_query($_POST);       // create http query out of the POST variables
$payload = explode('&sig=', $post)[0];  // remove the signture part from the POST varaiables


// check if the callback needs to be signed and the signature is correct
if ($ipn_key) {
  if (hash_hmac('sha256', $payload, $ipn_key) != $signature)      // if the signature is wrong
    return FALSE;     // do nothing and return false or it could be anything that suites your platform
}

// here will be the code to do something on your platfrom with the above POST variables  

?>
