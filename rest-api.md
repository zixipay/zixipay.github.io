# ZixiPay API Version 2

- [ZixiPay API Version 2](#zixipay-api-version-2)
  - [General API Information](#general-api-information)
  - [HTTP Return Codes](#http-return-codes)
  - [Error Response](#error-response)
  - [Endpoints Limit](#endpoints-limit)
    - [Rate and IP Limit](#rate-and-ip-limit)
  - [Endpoints security](#endpoints-security)
    - [Authorized IP](#authorized-ip)
    - [User ID](#user-id)
    - [Timestamp](#timestamp)
    - [Signature](#signature)
    - [Endpoint Call Examples for `getwallet`](#endpoint-call-examples-for-getwallet)
      - [Unix Shell](#unix-shell)
      - [PHP Code](#php-code)
  - [API Endpoints](#api-endpoints)
    - [Wallet Data Endpoints](#wallet-data-endpoints)
      - [getbalances](#getbalances)
      - [getwallet](#getwallet)
      - [getrates](#getrates)
      - [getfees](#getfees)
      - [statement](#statement)
    - [Transactional Endpoints](#transactional-endpoints)
      - [withdraw](#withdraw)
      - [transfer](#transfer)
      - [exchange](#exchange)

## General API Information
* The base endpoint url is: **https://api.zixipay.com**
* All endpoints return a JSON object.
* All API endpoints are accessible by HTTP `POST` method.
* For `POST` endpoints, the parameters must be sent as `request body` in urlencoded format.
* HTTP header `Content-Type` should be explicitly set to `application/x-www-form-urlencoded`.
* Parameters may be sent in any order.
* `gzip` is enabled on all endpoints and is recommended to be used on the client side.
* All endpoints are authenticated and require 3 mandatory parameters (`uid`, `ts` and `sig`). Details in the [Endpoint security](#endpoints-security) section.
* Data is returned in **descending** order. Newest first, oldest last.
## HTTP Return Codes
* HTTP `200` API call was correct.
* HTTP `4XX` return code is used when the WAF (Web Application Firewall) has been violated.
* HTTP `503` return code is used when rate limit has been hit.
## Error Response
* Any endpoint can return an error.
* When an endpoint returns an error, the `result` will be NULL and the `payload` will contain the reason.

**Error Response:**
```javascript
{
  "result":"",
  "payload":"here will be the reason"
}
```
## Endpoints Limit
### Rate and IP Limit
* API endpoints calls are limited to 2 requests/second per IP address.
* Surpassing the rate limit wil result in a 503 HTTP return code.
* When a 503 is received, the caller needs to stop and not spam the API.
* **Repeated violation of the rate limit will result in an automatic IP ban.**
* **The rate limit on the endpoints are based on the caller's IP address and not the API endpoint.**
## Endpoints security
### Authorized IP
* Access to all endpoints is restricted to the predefined IP address(es) in your ZixiPay Wallet settings `API Access Authorized IP`.
### User ID
* All endpoints require parameter `uid` to be sent in the `request body` which is your ZixiPay Wallet `User ID`.
* The `uid` is **not case sensitive** but needs to be sent in lower case.
### Timestamp
* All endpoints require parameter `ts` to be sent in the `request body` which is the Unix time when the request was created and sent.
* API calls will be rejected if the `ts` is not within 60 seconds window from the endpoint server's time.
### Signature
* All endpoints require parameter `sig` to be sent **at the end** of the `request body`.
* Endpoints use `HMAC-SHA256` signatures. The `HMAC-SHA256 signature` is a keyed `HMAC-SHA256` operation.
  Use your ZixiPay Wallet `API Access Key` as the key and the `request body` as the value for the HMAC operation.
* The `sig` is **not case sensitive** but needs to be sent in lower case.
### Endpoint Call Examples for `getwallet`

Parameter | Value
------------ | ------------
currency | BTC
User ID | kkdyrcuxj9jvc6f76fgw
API Key | Dq7MRukyFMxvs33944gsrsBGLLThVUQPqcScMJGv
Timestamp|1587228352


#### Unix Shell
Example of sending a valid signed payload from the
Linux command line using `echo`, `openssl` and `curl`.

* **requestbody:** currency=BTC&uid=kkdyrcuxj9jvc6f76fgw&ts=1587228352
* **HMAC-SHA256 signature**

    ```
    $ echo -n "currency=BTC&uid=kkdyrcuxj9jvc6f76fgw&ts=1587228352" | openssl dgst -sha256 -hmac "Dq7MRukyFMxvs33944gsrsBGLLThVUQPqcScMJGv"
    (stdin)= 338d827fd18856140fef360c5b58bce575b486ca864cfabd986c222b75593966
    ```


* **curl command**

    ```
    $ curl -X POST 'https://api.zixipay.com/apiv2/getwallet' -d 'currency=BTC&uid=kkdyrcuxj9jvc6f76fgw&ts=1587228352&sig=338d827fd18856140fef360c5b58bce575b486ca864cfabd986c222b75593966'
    ```
#### PHP Code
Example of sending a valid signed payload in PHP.

  ```
  $baseurl = 'https://api.zixipay.com';
  $endpoint = '/apiv2/getwallet';
  $uid = 'kkdyrcuxj9jvc6f76fgw';
  $apikey = 'Dq7MRukyFMxvs33944gsrsBGLLThVUQPqcScMJGv';

  $params = array(
      'currency'  => 'BTC',
      'uid'       => $uid,
      'ts'        => time()
    );
  
  $signature = hash_hmac('sha256', http_build_query($params), $apikey);   // generate signature

  $params += ['sig' => $signature];   // add the signature to the end of parameters array

  $curl = curl_init();
  curl_setopt_array($curl, array(
      CURLOPT_HEADER => false,
      CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
      CURLOPT_ENCODING => 'gzip',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_URL => $baseurl . $endpoint,
      CURLOPT_POSTFIELDS => http_build_query($params)
    ));
  
  $result = curl_exec($curl);
  ```
## API Endpoints
### Wallet Data Endpoints
#### getbalances
```
POST /apiv2/getbalances
```
Get all wallet balances.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":[
    {
      "name":"Zixi Dollar",
      "code":"USDZ",
      "balance":"980326.02"
    },
    {
      "name":"Zixi Euro",
      "code":"EURZ",
      "balance":"500099.00"
    },
    {
      "name":"Bitcoin",
      "code":"BTC",
      "balance":"142.50249196"
    },
    {
      "name":"Ethereum",
      "code":"ETH",
      "balance":"634.07842095"
    },
    {
      "name":"Litecoin",
      "code":"LTC",
      "balance":"3642.96052382"
    },
    {
      "name":"Tron",
      "code":"TRX",
      "balance":"9993266.93"
    },
    {
      "name":"USD Coin",
      "code":"USDC",
      "balance":"5019973.99"
    },
    {
      "name":"Tether",
      "code":"USDT",
      "balance":"13679689.56"
    }
  ]
}
```
#### getwallet
```
POST /apiv2/getwallet
```
Get the wallet address of a specific currency.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
currency | string | YES |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH, TRX, USDC or USDT)
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":[
    {
      "name":"Tether TRC20",   // wallet name
      "code":"USDT",          // currency symbol
      "address":"THaxGiBjvTCyXLdfAnroqZ3F9DE3MWzVqu", // wallet address
      "confirm":20             // number of confirmations required
    },
    {
      "name":"Tether ERC20",
      "code":"USDT",
      "address":"0x0ed8991afc868c45ffbcd4afdf7ebc273cf38ed2",
      "confirm":3
    }
  ]
}
```
#### getrates
```
POST /apiv2/getrates
```
Get live exchange rates

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature


**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":[
    {
      "code":"USDZ",
      "decimal":"2",
      "rate":"1.00"
    },
    {
      "code":"EURZ",
      "decimal":"2",
      "rate":"0.97"
    },
    {
      "code":"BTC",
      "decimal":"8",
      "rate":"19400.91"
    },
    {
      "code":"ETH",
      "decimal":"8",
      "rate":"1316.93"
    },
    {
      "code":"LTC",
      "decimal":"8",
      "rate":"54.13"
    },
    {
      "code":"TRX",
      "decimal":"2",
      "rate":"0.064"
    },
    {
      "code":"USDC",
      "decimal":"2",
      "rate":"1.00"
    },
    {
      "code":"USDT",
      "decimal":"2",
      "rate":"1.00"
    }
  ]
}
```
#### getfees
```
POST /apiv2/getfees
```
Get all the applicable fees and limits.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**Note 1:** The returned ```fee``` is always in the relevant currency except for the ```exchange``` section which is in percent.

**Note 2:** A returned value of **zero** in ```min_amount``` or ```max_amount``` means there is no limlits.

**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":{
    "deposit":[
      {
        "name":"Zixi Dollar",
        "symbol":"USDZ",
        "min_amount":0.1,
        "max_amount":0,
        "fee":0
      },
      {
        "name":"Zixi Euro",
        "symbol":"EURZ",
        "min_amount":0.1,
        "max_amount":0,
        "fee":0
      },
      {
        "name":"Bitcoin",
        "symbol":"BTC",
        "min_amount":0.0001,
        "max_amount":0,
        "fee":0
      },
      {
        "name":"Ethereum",
        "symbol":"ETH",
        "min_amount":0.001,
        "max_amount":0,
        "fee":0
      },
      {
        "name":"Litecoin",
        "symbol":"LTC",
        "min_amount":0.01,
        "max_amount":0,
        "fee":0
      },
      {
        "name":"Tron",
        "symbol":"TRX",
        "min_amount":1,
        "max_amount":0,
        "fee":0
      },
      {
        "name":"USD Coin",
        "symbol":"USDC",
        "min_amount":0.1,
        "max_amount":0,
        "fee":0
      },
      {
        "name":"Tether",
        "symbol":"USDT",
        "min_amount":0.1,
        "max_amount":0,
        "fee":0
      }
    ],
    "withdrawal":[
      {
        "name":"Zixi Dollar",
        "symbol":"USDZ",
        "min_amount":0.01,
        "max_amount":0,
        "fee":1
      },
      {
        "name":"Zixi Euro",
        "symbol":"EURZ",
        "min_amount":0.01,
        "max_amount":0,
        "fee":1
      },
      {
        "name":"Bitcoin",
        "symbol":"BTC",
        "min_amount":0.0001,
        "max_amount":0,
        "fee":0.0005
      },
      {
        "name":"Ethereum",
        "symbol":"ETH",
        "min_amount":0.001,
        "max_amount":0,
        "fee":0.005
      },
      {
        "name":"Litecoin",
        "symbol":"LTC",
        "min_amount":0.001,
        "max_amount":0,
        "fee":0.001
      },
      {
        "name":"Tron",
        "symbol":"TRX",
        "min_amount":1,
        "max_amount":0,
        "fee":1
      },
      {
        "name":"USD Coin-TRC20",
        "symbol":"USDC",
        "min_amount":0.01,
        "max_amount":0,
        "fee":1
      },
      {
        "name":"USD Coin-ERC20",
        "symbol":"USDC",
        "min_amount":0.01,
        "max_amount":0,
        "fee":20
      },
      {
        "name":"Tether-TRC20",
        "symbol":"USDT",
        "min_amount":0.01,
        "max_amount":0,
        "fee":1
      },
      {
        "name":"Tether-ERC20",
        "symbol":"USDT",
        "min_amount":0.01,
        "max_amount":0,
        "fee":20
      }
    ],
    "internaltransfer":[
      {
        "name":"Zixi Dollar",
        "symbol":"USDZ",
        "min_amount":0.01,
        "max_amount":0,
        "fee":1
      },
      {
        "name":"Zixi Euro",
        "symbol":"EURZ",
        "min_amount":0.01,
        "max_amount":0,
        "fee":1
      },
      {
        "name":"Bitcoin",
        "symbol":"BTC",
        "min_amount":0.0001,
        "max_amount":0,
        "fee":0.0001
      },
      {
        "name":"Ethereum",
        "symbol":"ETH",
        "min_amount":0.001,
        "max_amount":0,
        "fee":0.001
      },
      {
        "name":"Litecoin",
        "symbol":"LTC",
        "min_amount":0.001,
        "max_amount":0,
        "fee":0.0001
      },
      {
        "name":"Tron",
        "symbol":"TRX",
        "min_amount":1,
        "max_amount":0,
        "fee":1
      },
      {
        "name":"USD Coin",
        "symbol":"USDC",
        "min_amount":0.01,
        "max_amount":0,
        "fee":1
      },
      {
        "name":"Tether",
        "symbol":"USDT",
        "min_amount":0.01,
        "max_amount":0,
        "fee":1
      }
    ],
    "exchange":[
      {
        "name":"Zixi Euro",
        "symbol":"EURZ",
        "min_amount":1,
        "max_amount":500000,
        "fee":"1.00"
      },
      {
        "name":"Bitcoin",
        "symbol":"BTC",
        "min_amount":0.0001,
        "max_amount":3,
        "fee":"3.00"
      },
      {
        "name":"Ethereum",
        "symbol":"ETH",
        "min_amount":0.001,
        "max_amount":40,
        "fee":"3.00"
      },
      {
        "name":"Litecoin",
        "symbol":"LTC",
        "min_amount":0.01,
        "max_amount":1000,
        "fee":"3.00"
      },
      {
        "name":"Tron",
        "symbol":"TRX",
        "min_amount":100,
        "max_amount":500000,
        "fee":"3.00"
      },
      {
        "name":"USD Coin",
        "symbol":"USDC",
        "min_amount":1,
        "max_amount":500000,
        "fee":"1.00"
      },
      {
        "name":"Tether",
        "symbol":"USDT",
        "min_amount":1,
        "max_amount":500000,
        "fee":"1.00"
      }
    ]
  }
}
```
#### statement
```
POST /apiv2/statement
```

Get transaction(s) history with optional filters. Maximum number of transactions returned is 2000.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
txid | string | NO |ZixiPay transaction ID
timefrom | string | NO |from date in YYYY-MM-DD format
timeto | string | NO |to date in YYYY-MM-DD format
currency | string | NO |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH, TRX, USDC or USDT)
status | string | NO |Transaction status (pending, processed, cancelled or blocked)
type | string | NO |Transaction type (deposit, withdrawal, transfer, exchange or payment)
sender | string | NO |sender, could be user id or email address
recipient | string | NO |recipient, could be user id, email address or crypto wallet address
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":{
    "records":1,        // number of records
    "transactions":[    // array of transactions
      {
        "txid":"TD4598653469096786",    // ZixiPay transaction id
        "time":"2020-02-17 14:21:32",   // date and time of the transaction
        "type":"deposit",               // type of the transaction
        "status":"processed",           // status of the transaction
        "amount":"218.54",              // amount of the transaction
        "fee":"0.00",                   // fee of the transaction
        "currency":"USDT",              // currency of the transaction
        "sender":"Tether",              // sender
        "recipient":"My Wallet",        // recipient
        "details":"",                   // transaction details
        "extras":"",                    // transaction extra details
        "merchant_ref":""               // merchant API ref
        "hash":""                       // blockchain hash of the transaction
      }
    ]
  }
}
```
### Transactional Endpoints
#### withdraw
```
POST /apiv2/withdraw
```
Withdraw any of the supported cryptocurrencies.

This endpoint is used for sending funds to another wallet address on the blockchain.

A successful call to the ```withdraw``` endpoint, will create a pending transaction waiting in queue to be sent on the relevant blockchain which on average takes about 2-3 minutes.
As soon as the transaction has been successfully sent on the relevant blockchain, the status of the transaction will change from **pending** to **processed** and the transaction id/hash will be added to the details of the transaction.
In order to get the transaction hash/txid, simply call the ```statement``` endpoint with the ZixiPay's transaction ID as the filter to get the details of the transaction in about 3 minutes after calling the ```withdraw``` endpoint.


**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
amount | decimal | YES | amount to be withdrawn
currency | string | YES |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH, TRX, USDC or USDT)
recipient | string | YES | recipient's wallet address (for USDT/USDC withdrawals: TRC20 or ERC20 addresses are acceptable)
feein | binary | NO | fee inclusive, deduct the fee from the withdrawal amount<br />0: (default) don't deduct the fee from the withdrawal amount<br />1: deduct the fee from the withdrawal amount
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**IMPORTANT:** If the ```feein``` parameter is set to 0 (default), a total of ```amount``` + the applicable ```fee``` would be deducted from the wallet.


**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":[
    {
      "amount":"1500.00",           // amount of withdrawal
      "fee":"1.00",                 // processing fee
      "total":"1501.00",            // total
      "currency":"USDT",            // currency
      "status":"pending",           // status
      "txid":"TW6596823102569846"   // ZixiPay transaction ID
    }
  ]
}
```

#### transfer
```
POST /apiv2/transfer
```
Internal transfer any of the supported cryptocurrencies.

This endpoint is used for transferring funds to another ZixiPay wallet holder internally. Internal transfers are instant and irrevocable.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
amount | decimal | YES | amount to be transferred
currency | string | YES |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH, TRX, USDC or USDT)
recipient | string | YES | recipient's email address or user id
note | string | NO | internal comment for the transfer (optional)
passcode | number | NO | 4 digit pin if the transfer needs to be passcode protected (optional)
feein | binary | NO | fee inclusive, deduct the fee from the transfer amount<br />0: (default) don't deduct the fee from the transfer amount<br />1: deduct the fee from the transfer amount
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**IMPORTANT:** If the ```feein``` parameter is set to 0 (default), a total of ```amount``` + the applicable ```fee``` would be deducted from the wallet.


**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":[
    {
      "recipient":"somebody@anybody.com",   // recipient
      "amount":"100.00",                    // amount
      "fee":"0.50",                         // processing fee
      "currency":"USDT",                    // currency
      "status":"processed",                 // transaction status
      "txid":"TT2369512364598756",          // ZixiPay transaction id
      "passcode":""                         // transaction passcode
    }
  ]
}
```

#### exchange
```
POST /apiv2/exchange
```
Inter-wallet exchange.

This endpoint is used for exchanging funds between USDZ and other currencies. 

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
amount | decimal | YES | amount to be exchanged
from | string | YES | from currency, any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH, TRX, USDC or USDT)
to | string | YES | to currency, any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH, TRX, USDC or USDT)
feein | binary | NO | fee inclusive, deduct the fee from the exchange amount<br />0: (default) don't deduct the fee from the exchange amount<br />1: deduct the fee from the exchange amount
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**IMPORTANT:** If the ```feein``` parameter is set to 0 (default), a total of ```amount``` + the applicable ```fee``` would be deducted from the wallet.


**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":[
    {
      "fromamount": "10.00"                 // amount exchanged
      "fromcurrency": "USDZ"                // from currency
      "fromtxid": "TEYKJSVKGOOLHTOBDK3PN5"  // from exchange transaction id
      "toamount": "0.00027019"              // echanged amount
      "tocurrency": "BTC"                   // exchanged currency
      "totxid": "TE0K8NP01X2ZHKRV5FO544"    // exchanged transaction id
      "rate": "37010.30"                    // applied exchange rate
      "fee": "0.30000000"                   // fee
    }
  ]
}
```
