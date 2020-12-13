<?php
$uid = '';          // ZixiPay User ID
$apikey = '';       // ZixiPay API Access Key

// Merchant API call parameters 
$prms = array(
    'currency'  => 'BTC',             // currency
    'ref'       => 'anyreference',    // any unique reference from your platfrom
    'uid'       => $uid,
    'ts'        => time()
);

// getpaymentwallet call
$result = ZixiPay_ApiCall('getpaymentwallet', $prms, $apikey);
print_r($result);


/**
 * ZixipayAPI endpoint call
 *
 * $endpoint: string, endpoint you want to call
 * $params: array, enpoint parameters
 * $uid: string, User ID
 * $apikey: string, API Access Key
 */
function ZixiPay_ApiCall($endpoint, $params, $apikey) {
	
	$apiurl = 'https://api.zixipay.com/apiv2/';   // API endpoint URL

	$payload = http_build_query($params);					// create http payload

	$signature = hash_hmac('sha256', $payload, $apikey);	// generate signature

	$params['sig'] = $signature;							// add signature to the end of api parameters array

	$ch = curl_init();

	curl_setopt_array($ch, array(
		CURLOPT_POST			=> true,
		CURLOPT_HEADER			=> false,
		CURLOPT_ENCODING		=> 'gzip',
		CURLOPT_RETURNTRANSFER		=> true,
		CURLOPT_URL			=> $apiurl . $endpoint,
		CURLOPT_POSTFIELDS		=> $params
	));

	$result = curl_exec($ch);

	curl_close($ch);

	if (!$result)
		return false;

	return json_decode($result, true);
}
?>
