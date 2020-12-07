# ZixiPay API Version 2

- [Merchant integrationa and payment acceptance](#payment-acceptance)
  - [General API Information](./rest-api.md#general-api-information)
  - [Merchant Set Up](#merchant-set-up)  
  - [API Endpoints](#api-endpoints)
    - [Merchant API Endpoints](#merchant-api-endpoints)
      - [getpaymentwallet](#getpaymentwallet)

## Merchant integrationa and payment acceptance
* All [General API Information](./rest-api.md#general-api-information) are valid and applies.
* All the merchant API endpoints are accessible only if the [ZixiPay](https://zixipay.com) wallet acount is verified and the Merchant setting are set and active.

## Merchant Set Up


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
