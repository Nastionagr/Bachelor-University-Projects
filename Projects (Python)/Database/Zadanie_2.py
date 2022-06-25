from marshmallow import Schema, fields, validate, ValidationError
from marshmallow.utils import EXCLUDE
from flask import request, jsonify, make_response
from flask.json import JSONEncoder
from flask_restful import Resource
import datetime
import math

from DataBase import query

def validate_year(date):
    current_year = datetime.datetime.today().year
    if date.year == current_year:
        return True
    else:
        raise ValidationError("invalid_range")

class DateEncoder(JSONEncoder): # make an ISO8601 date format
    def default(self, o):
        if isinstance(o, datetime.date) or isinstance(o, datetime.datetime): # date OR date+time
            return o.isoformat()
        return super().default(o)

class GetSchemas(Schema): # checking the input
    # page_number has to be a positive int number (1+ page(s))
    page = fields.Int(validate=validate.Range(min=1))
    # number of records on page has to be >=0
    per_page = fields.Int(validate=validate.Range(min=0))
    # all possible database rows
    order_by = fields.Str(validate=validate.OneOf(['id', 'br_court_name', 'kind_name', 'cin', 'registration_date',
                                                   'corporate_body_name', 'br_section', 'br_insertion', 'text', 'street', 'postal_code', 'city']))
    order_type = fields.Str(validate=lambda x: x.lower() in ['asc', 'desc'])
    # ISO8601 date format
    registration_date_lte = fields.DateTime()
    registration_date_gte = fields.DateTime()
    # full text search
    query = fields.Raw()

class DateSchema(Schema):
    registration_date_lte = fields.Date()
    registration_date_gte = fields.Date()

class PostSchemaDate(Schema):
    registration_date = fields.Date()

class PostSchemas(Schema):
    br_court_name = fields.Str(required=True, error_messages={"required": "required"})
    kind_name = fields.Str(required=True, error_messages={"required": "required"})
    cin = fields.Int(required=True, error_messages={"required": "required", "invalid": "not_number"})
    registration_date = fields.DateTime(required=True, validate=validate_year, error_messages={"required": "required", "invalid": "invalid_range"}) # current year
    corporate_body_name = fields.Str(required=True, error_messages={"required": "required"})
    br_section = fields.Str(required=True, error_messages={"required": "required"})
    br_insertion = fields.Str(required=True, error_messages={"required": "required"})
    text = fields.Str(required=True, error_messages={"required": "required"})
    street = fields.Str(required=True, error_messages={"required": "required"})
    postal_code = fields.Str(required=True, error_messages={"required": "required"})
    city = fields.Str(required=True, error_messages={"required": "required"})

class Submissions_GET_POST(Resource):
    def get(self): # response to the GET method
        valid_input = None

        # validate dates
        try:
            obj = DateSchema().load(request.args, unknown=EXCLUDE)
            valid_input = obj if valid_input is None else {**valid_input, **obj}
        except ValidationError as err:
            valid_input = err.valid_data if valid_input is None else {**valid_input, **err.valid_data}

        try:
            obj = GetSchemas().load(request.args, unknown=EXCLUDE)
            valid_input = obj if valid_input is None else {**valid_input, **obj}
        except ValidationError as err:
            valid_input = err.valid_data if valid_input is None else {**valid_input, **err.valid_data}

        # first part of the query
        command = "SELECT id, br_court_name, kind_name, cin, registration_date, corporate_body_name, br_section, br_insertion, text, street, postal_code, city FROM ov.or_podanie_issues"
        command_metadata = " SELECT COUNT(*) FROM ov.or_podanie_issues"

        is_where = False
        where_condition = ""
        if 'query' in valid_input.keys() and len(valid_input['query']) > 0: # fulltext search
            where_condition += " WHERE ("
            if valid_input['query'].isnumeric():
                where_condition += "cin = %(cin)s OR "
                valid_input['cin'] = int(valid_input['query'])
            valid_input['query'] = "%%" + valid_input['query'] + "%%"
            where_condition += "corporate_body_name ILIKE %(query)s OR city ILIKE %(query)s)"
            is_where = True
        if 'registration_date_lte' in valid_input.keys(): # less than condition
            if is_where == False:
                where_condition += " WHERE"
                is_where = True
            else:
                where_condition += " AND"
            where_condition += " registration_date <= %(registration_date_lte)s::date"
        if 'registration_date_gte' in valid_input.keys(): # greater than condition
            if is_where == False:
                where_condition += " WHERE"
            else:
                where_condition += " AND"
            where_condition += " registration_date >= %(registration_date_gte)s::date"

        # completing the command with 'where condition'
        command += where_condition
        command_metadata += where_condition

        # format the output in the right order
        if 'order_by' not in valid_input.keys():
            valid_input['order_by'] = "id"
        command += " ORDER BY " + valid_input['order_by']
        if 'order_type' not in valid_input.keys() or valid_input['order_type'].lower() == "desc":
            command += " DESC"
        else:
            command += " ASC"

        # setting default values if it is necessary
        if 'page' not in valid_input.keys():
            valid_input['page'] = 1
        if 'per_page' not in valid_input.keys():
            valid_input['per_page'] = 10
        command += " LIMIT %(per_page)s OFFSET (%(page)s-1)*%(per_page)s" # implement paging

        results = query(command, valid_input) # perform query

        metadata = {"page": valid_input['page'], "per_page": valid_input['per_page']} # add all necessary info
        metadata["total"] = query(command_metadata, valid_input)[0]['count'] # count all items
        metadata["pages"] = math.ceil(metadata["total"] / metadata["per_page"]) # count number of pages

        return jsonify({"items": results, "metadata": metadata})

    def post(self):  # response to the POST method
        body_data = request.get_json()
        if body_data is None:
            body_data = dict()

        valid_input = None
        try:
            obj = PostSchemaDate().load(body_data, unknown=EXCLUDE) # read and check the whole input
            body_data["registration_date"] = datetime.datetime.combine(obj["registration_date"], datetime.datetime.min.time()).isoformat()
        except ValidationError as err:
            pass

        try:
            valid_input = PostSchemas().load(body_data, unknown=EXCLUDE) # read and check the whole input
        except ValidationError as err:
            # print all errors if there is any
            errors = list(map(lambda x: {"field": x, "reasons": err.messages[x]}, err.messages))
            return make_response(jsonify({"errors": errors}), 422)

        # getting new item's id in bulletin_issues
        id_bulletin = query("INSERT INTO ov.bulletin_issues(year, number, published_at, created_at, updated_at) VALUES "
                            "(date_part('year', CURRENT_DATE), (SELECT MAX(number) FROM ov.bulletin_issues "
                            "WHERE year = date_part('year', CURRENT_DATE))+1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) RETURNING id")[0]["id"]

        # getting new item's id in raw_issues
        id_raw = query("INSERT INTO ov.raw_issues (bulletin_issue_id, file_name, content, created_at, updated_at) VALUES "
                       "(" + str(id_bulletin) + ", '-', '-', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) RETURNING id")[0]["id"]

        # insertion into the or_podanie_issues table
        valid_input['address'] = valid_input['street'] + ", " + valid_input['postal_code'] + " " + valid_input['city']
        command = "INSERT INTO ov.or_podanie_issues(bulletin_issue_id, raw_issue_id, br_mark, br_court_code, br_court_name," \
                  " kind_code, kind_name, cin, registration_date, corporate_body_name, br_section, br_insertion, text, " \
                  "created_at, updated_at, address_line, street, postal_code, city) VALUES (" + str(id_bulletin) + ", " + str(id_raw) \
                  + ", '-', '-', %(br_court_name)s, '-', %(kind_name)s, %(cin)s, %(registration_date)s::date, %(corporate_body_name)s, " \
                  "%(br_section)s, %(br_insertion)s, %(text)s, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, %(address)s, %(street)s, %(postal_code)s, %(city)s) " \
                  "RETURNING id, br_court_name, kind_name, cin, registration_date, corporate_body_name, br_section, br_insertion, text, street, postal_code, city"

        return make_response(jsonify(query(command, valid_input)), 201)

class Submissions_DELETE(Resource):
    def delete(self, id):  # response to the DELETE method
        if id.isnumeric() is not True: # check if id is a number, so we can perform a select
            return make_response(jsonify({"errors": {"message": "not_number"}}), 404)

        both_id = query("SELECT id FROM ov.or_podanie_issues WHERE id = %(id)s", {"id":id})
        if len(both_id) == 0: # if the row with such id doesn't exist
            return make_response(jsonify({"error": {"message": "Záznam neexistuje"}}), 404)
        else:
            # delete the item
            query("DELETE FROM ov.or_podanie_issues WHERE id = " + id)

            return make_response(jsonify(), 204)