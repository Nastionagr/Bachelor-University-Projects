from flask import jsonify
from flask_restful import Resource
import datetime

from DataBase import query

def lovely_text(time_from_db): # rewriting the results out of database to the right data format
    hours_min_sec = datetime.timedelta(seconds = time_from_db.seconds) # converting sec into hours:minutes:seconds
    return {"uptime": str(time_from_db.days) + ' days ' + str(hours_min_sec)}

class WriteTime(Resource):
    def get(self): # response to the GET method

        time_from_db = query("SELECT date_trunc('second', current_timestamp -pg_postmaster_start_time()) as uptime;")
        return jsonify({"pgsql": lovely_text(time_from_db[0]['uptime'])})