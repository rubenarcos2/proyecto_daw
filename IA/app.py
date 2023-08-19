import flask
import pickle
import pandas as pd
from datetime import datetime
from flask import send_from_directory, Flask, render_template
from flask import json
import os

app = flask.Flask(__name__, template_folder='templates')

@app.route('/favicon.ico')
def favicon():
    return send_from_directory(os.path.join(app.root_path, 'static'),
                               'favicon.ico', mimetype='image/vnd.microsoft.icon')

                               
@app.route('/', methods=['GET', 'POST'])
def main():
    if flask.request.method == 'GET':
        return 'Hi world!'
    if flask.request.method == 'POST':
        # Use pickle to load in the pre-trained model.
        with open(f'model/gesmerca_price_estimated.pkl', 'rb') as f:
            model = pickle.load(f)
        
        content = flask.request.json
        
        idproduct = content['idproduct']
        quantity = content['quantity']
        created_at = content['created_at']

        created_at_unix_time = datetime.strptime(created_at, "%Y-%m-%dT%H:%M:%S.%fZ").timestamp()

        input_variables = pd.DataFrame([[idproduct, quantity, created_at_unix_time]],
                               columns=['idproduct', 'quantity', 'created_at'],
                                       dtype=float)        
        prediction = model.predict(input_variables)[0]

        data = {
            "price" : prediction
        }
        response = app.response_class(
                response=json.dumps(data),
                status=200,
                mimetype='application/json'
            )
        return data

if __name__ == "__main__":
#    app.run(host='0.0.0.0', port=8000, debug=True)
    app.run(host='0.0.0.0', port=5001, debug=False, ssl_context=('/usr/src/certs/fullchain.pem','/usr/src/certs/privkey.pem'))
