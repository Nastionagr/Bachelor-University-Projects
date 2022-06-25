from flask import Flask
from flask_restful import Api

from Zadanie_1 import WriteTime
from Zadanie_2 import Submissions_GET_POST, Submissions_DELETE, DateEncoder

# creating API
app = Flask(__name__)
app.json_encoder = DateEncoder # controlling an ISO8601 date format
app.config['JSON_SORT_KEYS'] = False # stop sorting the output by ABC order
api = Api(app)

###########################################  IMPLEMENTING DIFFERENT METHODS  ###########################################

#_________________________________________________  FIRST ASSIGNMENT  _________________________________________________#

api.add_resource(WriteTime, '/v1/health/')

#_________________________________________________  SECOND ASSIGNMENT  ________________________________________________#

api.add_resource(Submissions_GET_POST, '/v1/ov/submissions/')
api.add_resource(Submissions_DELETE, '/v1/ov/submissions/<id>')

########################################################################################################################

if __name__ == '__main__':
    app.run()