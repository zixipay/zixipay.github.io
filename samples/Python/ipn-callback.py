from flask import Flask, request
import urllib.parse
import hmac
import hashlib

api = Flask(__name__)

callback_key = ''       # Your IPN callback key 

@api.route('/zcallback', methods=['POST'])
def post_callback():
    if(callback_key is not ''):   # signature is required
        signature = request.form.get('sig')
        form = urllib.parse.urlencode(request.form).split('&sig')   # remove the signature part
        payload = (form[0])
        
        if(signature != hmac.new(bytes(callback_key, 'latin-1'), bytes(payload, 'latin-1'), hashlib.sha256).hexdigest().lower()):
            return 'Signature does not match'
        else:
            return 'Signature matches'
            
    else:
        return 'No Signature is required'

if __name__ == '__main__':
    api.run()
