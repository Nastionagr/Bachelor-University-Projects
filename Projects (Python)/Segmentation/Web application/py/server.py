from flask import Flask, request
from flask_cors import CORS
import json
import analyse_photo

app = Flask(__name__)
cors = CORS(app)

@app.route('/analyze', methods = ['POST'])
def analyze():
    data = request.get_json()

    baseimg = data['url']
    result = runNeiro(baseimg)
    
    print(result)

    return json.dumps({"mask": str(result[0]), "red": result[1],"green": result[2], "blue": result[3] })



@app.route('/test', methods = ['GET'])
def test():
    return json.dumps({"result":"success"})

def runNeiro(baseimg):
    return analyse_photo.run(baseimg)

if __name__== "__main__":
    app.run(port=2200, debug=True)
