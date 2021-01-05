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
      - [statement](#statement)
    - [Transactional Endpoints](#transactional-endpoints)
      - [withdraw](#withdraw)
      - [transfer](#transfer)

## General API Information
* The base endpoint url is: **https://api.zixipay.com**
* All endpoints return a JSON object.
* All API endpoints are accessible by `POST` method.
* For `POST` endpoints, the parameters must be sent as `request body`.
* HTTP header `Content-Type` needs to be set as `application/x-www-form-urlencoded`.
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
* **The rate limit on the endpoints are based on the IP address and not the API endpoint.**
## Endpoints security
### Authorized IP
* Access to all endpoints is restricted to the predefined IP address in your ZixiPay Wallet `API Access Authorized IP`.
### User ID
* All endpoints require a parameter, `uid`, to be sent in the `request body` which should be your ZixiPay Wallet `User ID`.
* The `uid` is **not case sensitive** but needs to be sent in lower case.
### Timestamp
* All endpoints require a parameter, `ts`, to be sent in the `request body` which should be the Unix time when the request was created and sent.
* API calls will be rejected if the `ts` is not within 60 seconds window from the endpoint server's Unix time.
### Signature
* All endpoints require an additional parameter, `sig`, to be
  sent **at the end** of the `request body`.
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
      CURLOPT_ENCODING => 'gzip',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_URL => $baseurl . $endpoint,
      CURLOPT_POSTFIELDS => $params
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

**Response:**
```javascript
{
  "result":"ok",
  "payload":[           // array of wallets
    {
      "name":"Zixi Dollar",     // currency name
      "code":"USDZ",            // currency symbol
      "balance":"0.00"          // available balance
    },
    {
      "name":"Zixi Euro",
      "code":"EURZ",
      "balance":"0.00"
    },
    {
      "name":"Litecoin",
      "code":"LTC",
      "balance":"0.00000000"
    },
    {
      "name":"Bitcoin",
      "code":"BTC",
      "balance":"0.00000000"
    },
    {
      "name":"Ethereum",
      "code":"ETH",
      "balance":"0.00000000"
    },
    {
      "name":"Tether",
      "code":"USDT",
      "balance":"0.00"
    }
  ]
}
```
#### getwallet
```
POST /apiv2/getwallet
```
Get wallet address

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
currency | string | YES |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH or USDT)
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**Response:**
```javascript
{
  "result":"ok",
  "payload":[
    {
      "name":"Tether OMNI",   // wallet name
      "code":"USDT",          // currency symbol
      "address":"1PkYiGCF3zVif5vm1ogXYuvtGaK3p7qLgK", // wallet address
      "confirm":1             // number of confirmations required
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


**Response:**
```javascript
{
  "result":"ok",
  "payload":[         // array of currencies
    {
      "code":"USDZ",    // currency code
      "decimal":"2",    // number of decimal digits
      "rate":"1.00"     // exchange rate
    },
    {
      "code":"EURZ",
      "decimal":"2",
      "rate":"1.09"
    },
    {
      "code":"LTC",
      "decimal":"8",
      "rate":"43.71"
    },
    {
      "code":"BTC",
      "decimal":"8",
      "rate":"7239.00"
    },
    {
      "code":"ETH",
      "decimal":"8",
      "rate":"183.91"
    },
    {
      "code":"USDT",
      "decimal":"2",
      "rate":"1.00"
    }
  ]
}
```
#### statement
```
POST /apiv2/statement
```

Get transaction(s) history with optional filters. Maximum number of transactions returned is 100.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
txid | string | NO |ZixiPay transaction ID
timefrom | string | NO |from date in YYYY-MM-DD format
timeto | string | NO |to date in YYYY-MM-DD format
currency | string | NO |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH or USDT)
status | string | NO |Transaction status (pending, processed, cancelled or blocked)
type | string | NO |Transaction type (deposit, withdrawal, transfer, exchange or payment)
sender | string | NO |sender, could be user id or email address
recipient | string | NO |recipient, could be user id, email address or crypto wallet address
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**Response:**
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
        "details":""                    // transaction details
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
Withdraw any of the supported cryptocurrencies

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
amount | number | YES | amount to be withdrawn
currency | string | YES |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH or USDT)
recipient | string | YES | recipient's wallet address
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature


**Response:**
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
Internal transfer any of the supported cryptocurrencies

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
amount | number | YES | amount to be transferred
currency | string | YES |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH or USDT)
recipient | string | YES | recipient's email address or user id
note | string | NO | internal comment for the transfer
passcode | number | NO | 4 digit pin if the transfer needs to be passcode protected
uid | string | YES |User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature


**Response:**
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
