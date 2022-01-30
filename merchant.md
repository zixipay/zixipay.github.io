# ZixiPay Merchant API Version 2

- [Merchant Integration and Payment Acceptance](#payment-acceptance)
  - [General API Information](./rest-api.md#general-api-information)
  - [Merchant Settings](#merchant-settings)  
  - [API Endpoints](#api-endpoints)
    - [Merchant API Endpoints](#merchant-api-endpoints)
      - [getpaymentwallet](#getpaymentwallet)
      - [createinvoice](#createinvoice)
      - [cancelinvoice](#cancelinvoice)
      - [getinvoice](#getinvoice)
      - [listinvoices](#listinvoices)
    - [IPN Callback Parameters](#ipn-callback-parameters)

## Merchant integration and payment acceptance
ZixiPay Merchant API alllows accepting crypto payments with an automated payment notification callback function. Merchant API could be integrated and utilized in all kind of use cases such as invoice payments, wallet services, e-commerce, exchange services, etc.

* All [General API Information](./rest-api.md#general-api-information), [Endpoints Limit](./rest-api.md#endpoints-limit) and [Endpoints security](./rest-api.md#endpoints-security) are valid and applies.
* All the merchant API endpoints are accessible only if your [ZixiPay](https://zixipay.com/) wallet is verified and the [Merchant Setting](#merchant-settings) are set and active.
* ZixiPay Merchant API provides two methods of integration with the client's platfrom:
  * Total integration: using ZixiPay as **Wallet as a Service** to fully integrate with your own platfrom. The best use case for this option is giving your users their own dedicated wallet addresses on your platfrom ([getpaymentwallet](#getpaymentwallet) endpoint)
  * Invoicing: issuing invoices and letting ZixiPay handle the UI and the payment ([createinvoice](#createinvoice) endpoint) ([INVOICE SAMPLE](https://zixipay.com/invoice?id=51NLE7RW780YR55YU3RULNZYMSPUE8))

## Merchant Settings

How to set up and activate the ZixiPay Merchant API: 

* Login to your ZixiPay Wallet at [https://zixipay.com/login](https://zixipay.com/login)
* Go to the ```Merchants``` menu. If your wallet account is not verified, you willl be asked to verify your account.
* ```Website URL``` is the website the payment acceptance service is going to be used for.
* ```Logo URL``` is the URL to the merchant's logo file to be used in the invoices.
* ```Category``` is the most relevant business category the website fits in.
* ```IPN Callback URL``` needs to be set only if a callback payment notification is required for each payment. If left empty, no callback will be done. 
* ```IPN callback hash key``` is the key to be used to sign the payment callback with ```HMAC-SHA256```. If left empty, callback will not be signed.
* ```Automatic exchange to USDZ``` if enabled, all incoming payments in other currencies will be automatically exchanged to and deposited in USDZ. **This option completely protects the merchant from crypto price fluctuation.**


## API Endpoints
### Merchant API Endpoints
#### getpaymentwallet
```
POST /apiv2/getpaymentwallet
```
Get payment wallet address.
This endpoint returns a new wallet address everytime it is called.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
currency | string | YES |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH or USDT)
ref | string | YES |A reference tag to this payment wallet (depeneding on the usage this could be an invoice number, account number, userid, username, email address or any other kind of unique reference in your platform)
uid | string | YES |ZixiPay User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature


**IMPORTANT:** Calls to this endpoint with the same ```ref``` tag would return the same wallet address. This is useful when you need to have persistant wallet address for each client/account in your platform.


**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":[
    {
      "name":"Tether TRC20",   // currency name
      "code":"USDT",          // currency symbol
      "address":"TH53ejapLDKDFxxqP2RREfxCNtW26gFKeb", // wallet address
      "qr-code":"https://qrg.zixipay.com/api/qr.php?data=TH53ejapLDKDFxxqP2RREfxCNtW26gFKeb", // QR-Code of the address
      "confirm":20             // number of confirmations required
    },
    {
      "name":"Tether ERC20",
      "code":"USDT",
      "address":"0x0ed8991afc868c45ffbcd4afdf7ebc273cf38ed2",
      "qr-code":"https://qrg.zixipay.com/api/qr.php?data=0x0ed8991afc868c45ffbcd4afdf7ebc273cf38ed2",
      "confirm":3
    },
    {
      "name":"Tether OMNI",
      "code":"USDT",
      "address":"1PkYiGCF3zVif5vm1ogXYuvtGaK3p7qLgK",
      "qr-code":"https://qrg.zixipay.com/api/qr.php?data=1PkYiGCF3zVif5vm1ogXYuvtGaK3p7qLgK",
      "confirm":1
    }
  ]
}
```

**QR-Code:** Calls to this endpoint returns a URL for the ```qr-code``` of the wallet address which could be easily used in the ```<img>``` HTML tag as its ```src```.

#### createinvoice
```
POST /apiv2/createinvoice
```
Create a payment invoice.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
amount | number | YES |Invoice amount
currency | string | YES |Any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH or USDT)
multicurrency | binary | NO |Can the invoice be paid by any of the supported cryptocurrencies?<br />0: Disabled (default). Invoice can only be paid by the invoice currency<br />1: Enabled. Available when the invoice currency is USDZ
validity | number | NO |The invoice validity in minutes. 0 (default) means the invoice will be valid for 10 days
ref | string | YES |A reference tag to this invoice (depeneding on the usage this could be an invoice number, account number, userid, username, email address or any other kind of unique reference in your platform)
uid | string | YES |ZixiPay User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

Calls to this endpoint returns a unique ```invoice_id``` plus the invoice URL and URL of the QR-Code of the invoice URL will returned as well.

**IMPORTANT:** Invoices will be cancelled automatically when the validity period is expired. Cancelled invoices will be deleted after one week.


**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":{
    "invoice_id":"RC38OQ2NXJQ77GSSU7R728BUHSOLNK",
    "invoice_url":"https://zixipay.com/invoice?id=RC38OQ2NXJQ77GSSU7R728BUHSOLNK",
    "invoice_url_qrcode":"https://qrg.zixipay.com/api/qr.php?data=https://zixipay.com/invoice?id=RC38OQ2NXJQ77GSSU7R728BUHSOLNK"
  }
}
```

#### cancelinvoice
```
POST /apiv2/cancelinvoice
```
Cancel a payment invoice.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
invoice_id | string | YES |The ```invoice_id``` of the invoice to be cancelled
uid | string | YES |ZixiPay User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature

**IMPORTANT:** Cancelled invoices will be deleted after one week.

**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":"invoice cancelled successfully"
}
```

#### getinvoice
```
POST /apiv2/getinvoice
```
Get an invoice details.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
invoice_id | string | YES |The ```invoice_id``` of the invoice
uid | string | YES |ZixiPay User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature


**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":{
    "invoice_id":"RC38OQ2NXJQ77GSSU7R728BUHSOLNK",
    "amount":"0.40000000",
    "currency":"BTC",
    "date":"2021-11-20 10:14:26",
    "status":"paid",
    "ref":"some1random2ref",
    "accept_multi":"0",
    "validity":"1000"
  }
}
```

#### listinvoices
```
POST /apiv2/listinvoices
```
Get all the invoices. Optional filters could be used to narrow down the search.

**Parameters:**


Name | Type | Mandatory | Description
------------ | ------------ | ------------ | ------------
invoice_id | string | NO |The ```invoice_id``` of the invoice
ref | string | NO |Reference tag to the invoice
datefrom | string | NO |From this issue date (YYYY-MM-DD)
dateto | string | NO |To this issue date (YYYY-MM-DD)
currency | string | NO |Invoice currency (USDZ, EURZ, LTC, BTC, ETH or USDT)
status | string | NO |Invoice status (paid, pending or cancelled)
uid | string | YES |ZixiPay User ID
ts | number | YES |Unix time
sig | string | YES |HMAC-SHA256 signature


**Response:** (Example)
```javascript
{
  "result":"ok",
  "payload":{
    "records":1,
    "invoices":[
      {
        "invoice_id":"RC38OQ2NXJQ77GSSU7R728BUHSOLNK",
        "amount":"0.40000000",
        "currency":"BTC",
        "date":"2021-11-20 10:14:26",
        "status":"paid",
        "ref":"some1random2ref",
        "accept_multi":"0",
        "validity":"1000"
      }
    ]
  }
}
```

---

### IPN Callback Parameters
If the IPN Callback URL is set in the Merchant settings, upon receiving funds in any of the wallet addresses generated by the ```getpaymentwallet``` endpoint or invoices created by the ```createinvoice``` endpoint, an HTTPS POST will be made to the Callback URL with the following parameters in urlencoded format.

**Parameters:**


Name | Type | Description
------------ | ------------ | ------------
ref | string |reference tag to this payment wallet
invoice | string |receiving ```invoice_id``` if this an invoive payment, null if it is not an invoice payment.
wallet | string |receiving wallet address.
amount|number| amount of the incoming payment
fee|number|merchant API processing fee + the exhange fee if the auto-exchange to USDZ has been enabled.
currency | string | any of the supported currencies (USDZ, EURZ, LTC, BTC, ETH or USDT)
exchange<sup>*</sup> | binary | 0: if auto-echange to USDZ has NOT been done.<br />1: if auto-exchange to USDZ has been done.
xamount<sup>*</sup> | number | actual incoming payment amount if auto-exchange to USDZ has been done, null otherwise.
xcurrency<sup>*</sup> | string | actual incoming currency if auto-exchange to USDZ has been done, null otherwise.
xrate<sup>*</sup> | number | applied exchange rate if auto-exchange to USDZ has been done, null otherwise.
txid | string | blockchain txid/hash
zxid | string | ZixiPay transaction id
time | number | Transaction time (Unix time)
sig | string | HMAC-SHA256 signature (will be null if ```IPN callback hash key``` has not been set in the [Merchant Settings](#merchant-settings))

**\* exchange, xamount, xcurrency and xrate are used when ```Automatic exchange to USDZ``` is activated in the [Merchant Settings](#merchant-settings) or this was an invoice payment with enabled ```multicurrency```.**

**VERY IMPORTANT:** If there was an error/technical problem during IPN callback, our system would try up to 5 times until it is done successfully and nevertheless there is a tiny chance your system receives more than one IPN callback for the same transaction. Your IPN callback handler must always watch for duplicate callbacks by checking ```zxid``` (ZixiPay transaction id) or a method of your choice to avoid double deposit/credit on your side.

**IMPORTANT 1:** If the receiving end is behind a firewall, ZixiPay's IP addresses and TCP port 443 needs to be permitted to pass through.

**IMPORTANT 2:** Callback is HTTPS only, so SSL needs to be enabled and valid on the receiving end.
