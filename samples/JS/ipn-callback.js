var express = require('express');
var bodyParser = require('body-parser');
var crypto = require('crypto');
var querystring = require('querystring');

var app = express();

var ipn_key = '';      // enter your IPN callback key here, could be left blank if you don't want a signed IPN callback 
var ipn_url = '';      // enter your IPN callback url key here 

app.use(bodyParser.urlencoded({ extended: true }));

app.post(ipn_url, (req, res) => {
    res.sendStatus(200);
    params = req.body;
    if(ipn_key) {	// if we required a signature
	sig = params.sig;
    	delete params.sig;
    	if (sig != crypto.createHmac("sha256", ipn_key).update(querystring.stringify(params)).digest("hex"))
		// signature does not match: do nothing or it could be anything that suites your platform
		console.log('Signature does not match');
	    	return;
    }
	
	// FIRST check if this IS NOT a duplicate callback by making sure no previous transaction has been processed with the same zxid (ZixiPay transaction id)

	// here will be the code to process the incoming transaction on your platfrom with the POST variables
});

app.listen(443, () => console.log(`Started server at http://localhost:8080!`));
