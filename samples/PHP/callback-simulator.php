<?php

// set both of the following variables before running your tests
$callbackurl = '';			// callback URL  
$ipnkey = '';				// IPN callback key


$ipnparams = array(
	'ref'		=> 'SomeRandomRefString',					// your transaction ref
	'wallet'	=> 'TQmB7S6GtXcwoR64ZbFmXFjSa3EgBz9nqf',	// wallet address which recieved the payment
	'amount'	=> '1000',		// amount
	'fee'		=> '5',			// fee
	'currency'	=> 'USDT',		// currency
	'txid'		=> '49af4314ab3adcb9b04dc182cb0dd815ecdd01dec1216e856a4b8cb07fc098f4',   // blockchain hash/transaction id
	'zxid'		=> 'TDABCDEFGHIJKLMNOPQRST',			// ZixiPay transaction is
	'time'		=> time()								// time
);


if ($ipnkey) {
	$payload = http_build_query($ipnparams);				// create http payload
	$signature = hash_hmac('sha256', $payload, $ipnkey);	// generate signature
	$ipnparams['sig'] = $signature;							// add signature to the end of api parameters array
}

$ch = curl_init();

curl_setopt_array($ch, array(
		CURLOPT_CONNECTTIMEOUT	    => 3,
		CURLOPT_TIMEOUT		    => 5,
		CURLOPT_POST                => true,
		CURLOPT_HEADER              => false,
		CURLOPT_HTTPHEADER	    => array('Content-Type: application/x-www-form-urlencoded'),
		CURLOPT_ENCODING            => '',
		CURLOPT_URL		    => $callbackurl,
		CURLOPT_POSTFIELDS          => http_build_query($ipnparams)
));

$result = curl_exec($ch);
curl_close($ch);

if ($result === true)
	echo 'Callback was successfull!' . PHP_EOL;
else
	echo 'Callback failed!' . PHP_EOL;

