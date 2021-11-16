# Activation of ZixiPay Wallet API

In order to activate ZixiPay API, three things needs to be done. Finding the ```User ID```, setting the ```API Access Authorized IP``` and generating the ```API Key```.
Here is how it is done:

* Login to your ZixiPay Wallet at [https://zixipay.com/login](https://zixipay.com/login)
* Go to the ```Settings``` menu.
* Find your ```User ID``` right below the balances, User ID is mandatory in all endpoints calls as ```uid``` parameter.
* Set the ```API Access Authorized IP``` and save the Settings. This is the IP address your API calls are going to be made from and is mandatory. Up to 3 different IPs can be set. Endpoint calls will be rejected if no Authorized IP is set or the IP address doesn't match. **Only public IP addresses are accepted.**
* Enable your wallet's 2FA (Two Factor Authentication) if it has not been enabled before. API Access will not be activated unless your wallets's 2FA is enabled.
* Click on ```Generate New API Access Key``` to generate a new API Key.
* Click on ```Show API Access Key``` to get your API Key.
