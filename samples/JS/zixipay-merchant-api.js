var axios = require('axios');
var crypto = require('crypto');
var querystring = require('querystring');

var uid = '';          // ZixiPay User ID
var api_key = '';       // ZixiPay API Access Key

prms = {
	uid: uid,
	ts: Math.floor(Date.now() / 1000)
};

// getpaymentwallet call
ZixiPay_ApiCall('getpaymentwallet', prms, api_key).then((r) => {
	if(r.result == 'ok')
		console.log(r.payload);
});


/**
 * Zixipay API endpoint call
 */
async function ZixiPay_ApiCall(endpoint, params, apikey) {
	
	api_url = 'https://api.zixipay.com/apiv2/';   // ZixiPay API endpoint URL

	params.sig = crypto.createHmac("sha256", apikey).update(querystring.stringify(params)).digest("hex");

	return await axios.post(api_url+endpoint, querystring.stringify(params), { headers: { "Content-Type": "application/x-www-form-urlencoded" } })
		.then((r) => {
			return r.data;
		})
		.catch((r) => {
			//handle error
			return false;
	});
}
