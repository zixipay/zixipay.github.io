import requests
import urllib.parse
import hmac
import hashlib
import time
import json

#
# Zixipay API endpoint call
#
# endpoint: string, endpoint you want to call
# params: array, enpoint parameters
# uid: string, User ID
# apikey: string, API Access Key
#

def ZixiPay_ApiCall(endpoint, params, apikey):
    apiurl = 'https://api.zixipay.com/apiv2/'    # API endpoint URL
    payload = urllib.parse.urlencode(params)    # create urlencoded payload
    signature = hmac.new(bytes(apikey, 'latin-1'), bytes(payload, 'latin-1'), hashlib.sha256).hexdigest().lower()    # generate signature
    params.append(('sig', signature))    # add signature to the end of api parameters array
    result = requests.post(apiurl+endpoint, data = params, headers = {"Content-Type": "application/x-www-form-urlencoded"})
    if result == None:
        return False
    else:
        return json.loads(result.text)


uid = ''          # ZixiPay User ID
apikey = ''       # ZixiPay API Access Key

# API call parameters 
prms = [
    ('currency',  'BTC'),
    ('ref',  'anyreference'),
    ('uid',  uid),
    ('ts',   int(time.time()))
]

# getpaymentwallet call
result = ZixiPay_ApiCall('getpaymentwallet', prms, apikey)
print(result)
