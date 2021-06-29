from flask import Flask, request
import urllib.parse
import hmac
import hashlib

api = Flask(__name__)

callback_key = ''       # Your IPN callback key 

@api.route('/zcallback', methods=['POST'])
def post_callback():
    if(callback_key != ''):   # signature is required
        signature = request.form.get('sig')   # get the signature
        form = urllib.parse.urlencode(request.form).split('&sig')   # remove the signature part
        payload = form[0]
        
        if(signature != hmac.new(bytes(callback_key, 'latin-1'), bytes(payload, 'latin-1'), hashlib.sha256).hexdigest().lower()):
            # do nothing and return false or it could be anything that suites your platform
            return 'Signature does not match'
        else:
            # FIRST check if this IS NOT a duplicate callback by making sure no previous transaction has been processed with the same zxid (ZixiPay transaction id)
            # here will be the code to process the incoming transaction on your platfrom with the POST variables
            return 'Signature matches'
            
    else:
        # FIRST check if this IS NOT a duplicate callback by making sure no previous transaction has been processed with the same zxid (ZixiPay transaction id)
        # here will be the code to process the incoming transaction on your platfrom with the POST variables
        return 'No Signature is required'


if __name__ == '__main__':
    api.run()
