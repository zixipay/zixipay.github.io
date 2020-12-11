# ZixiPay API Version 2

- [Merchant Integration and Payment Acceptance](#payment-acceptance)
  - [General API Information](./rest-api.md#general-api-information)
  - [Merchant Settings](#merchant-settings)  
  - [API Endpoints](#api-endpoints)
    - [Merchant API Endpoints](#merchant-api-endpoints)
      - [getpaymentwallet](#getpaymentwallet)
    - [IPN Callback Parameters](#ipn-callback-parameters)

## Merchant integration and payment acceptance
* All [General API Information](./rest-api.md#general-api-information), [Endpoints Limit](./rest-api.md#endpoints-limit) and [Endpoints security](./rest-api.md#endpoints-security) are valid and applies.
* All the merchant API endpoints are accessible only if the [ZixiPay](https://zixipay.com) wallet acount is verified and the [Merchant Setting](#merchant-settings) are set and active.

## Merchant Settings

How to set up and activate the ZixiPay Merchant API: 

* Login to your ZixiPay Wallet at [https://zixipay.com/login](https://zixipay.com/login)
* Go to the ```Merchants``` menu. If your wallet account is not verified, you willl be asked to verify your account.
* ```Website URL``` is the website the payment acceptance service is required for.
* ```Category``` is the most relevant business category the website fits in.
* ```IPN Callback URL``` needs to be set only if a callback payment notification is required for each payment. If left empty, no callback will be done. 
* ```IPN callback hash key``` is the key to be used to sign the payment callback with ```HMAC-SHA256```. If left empty, callback will not be signed.
* ```Automatic exchange to USDZ``` if enabled, all incoming payments in other currencies will be automatically exchanged to and deposited in USDZ.


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
ref | string | YES |A reference tag to this payment wallet (depeneding on the usage this could be an invoice number, userid, username or any other references in the caller's platform)
uid | string | YES |ZixiPay User ID
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
      "qr-code":"https://qrg.zixipay.com/api/qr.php?data=1PkYiGCF3zVif5vm1ogXYuvtGaK3p7qLgK", // QR-Code of the address
      "confirm":1             // number of confirmations required
    },
    {
      "name":"Tether ERC20",
      "code":"USDT",
      "address":"0x0ed8991afc868c45ffbcd4afdf7ebc273cf38ed2",
      "qr-code":"https://qrg.zixipay.com/api/qr.php?data=0x0ed8991afc868c45ffbcd4afdf7ebc273cf38ed2", // QR-Code of the address
      "confirm":3
    }
  ]
}
```
**IMPORTANT:** Calls to this endpoint with the same ```ref``` tag would return the same payment wallet address.



### IPN Callback Parameters
If the IPN Callback URL is set in the Merchant settings, an HTTP POST request will be made to the defined URL with the following parameteres:

**Parameters:**


Name | Type | Description
------------ | ------------ | ------------
ref | string |Reference tag to this payment wallet
amount|number| amount of the incoming payment
fee|number|the fee that has been charged for auto-exhange, callback, both or will be zero if no fees has been charged
currency | string | Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH or USDT)
txid | string | ZixiPay transaction id
ts | number | Unix time
sig | string | HMAC-SHA256 signature (will be null if hash signature has not been set in the Merchant settings)
