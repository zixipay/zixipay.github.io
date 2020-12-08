# ZixiPay API Version 2

- [Merchant integrationa and payment acceptance](#payment-acceptance)
  - [General API Information](./rest-api.md#general-api-information)
  - [Merchant Settings](#merchant-settings)  
  - [API Endpoints](#api-endpoints)
    - [Merchant API Endpoints](#merchant-api-endpoints)
      - [getpaymentwallet](#getpaymentwallet)
    - [IPN Callback Parameters](#ipn-callback-parameters)

## Merchant integrationa and payment acceptance
* All [General API Information](./rest-api.md#general-api-information) are valid and applies.
* All the merchant API endpoints are accessible only if the [ZixiPay](https://zixipay.com) wallet acount is verified and the Merchant setting are set and active.

## Merchant Settings

Here are the steps for set up and activation of the ZixiPay Merchant API: 

* Login to your ZixiPay Wallet at [https://zixipay.com/login](https://zixipay.com/login)
* Go to the ```Merchants``` menu. If your wallet account is not verified, you willl be asked to verify your account.
* ```Website URL``` is the payment acceptance is going to be done.
* ```Category``` is the most relevant business category the website fits into.
* ```IPN Callback URL``` need to be set only if an callback payment notification is required to be called for each payment. If left empty, no callback be done. 
* ```IPN callback hash key``` is the key to be used to sign the callback call with ```HMAC-SHA256``` signatures. If left empty, callback will not be signed.
* ```Automatic exchange to USDZ``` if enabled, all incoming payments in other currencies will be automatically exchanged to and deposited in USDZ wallet.


## API Endpoints
### Merchant API Endpoints
#### getpaymentwallet
```
POST /apiv2/getpaymentwallet
```
Get payment wallet address

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
currency | string | YES |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH or USDT)
ref | string | NO |Reference code to this payment wallet
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
### IPN Callback Parameters
