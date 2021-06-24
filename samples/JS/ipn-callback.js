var express = require('express');
var bodyParser = require('body-parser');
var crypto = require('crypto');
var querystring = require('querystring');

var app = express();

app.use(bodyParser.urlencoded({ extended: true }));

app.post('/test', (req, res) => {
    res.sendStatus(200);
    params = req.body;
    sig = params.sig;
    delete params.sig;
    if (sig != crypto.createHmac("sha256", "E2UeapPQsbuxGaxa").update(querystring.stringify(params)).digest("hex"))
		  console.log('We got a match');
    else {
      // here 
    }
    
});

app.listen(443, () => console.log(`Started server at http://localhost:8080!`));
