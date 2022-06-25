import psycopg2

###############################################  CONNECTION PARAMETERS  ################################################

from parameters import DB_NAME, DB_USER, DB_PASS, DB_HOST, DB_PORT, DB_SSL

########################################################################################################################

def connection_to_db():
    try:
        # trying to connect to the database
        connection = psycopg2.connect(host=DB_HOST, port=DB_PORT, database=DB_NAME, user=DB_USER, password=DB_PASS, sslmode=DB_SSL)
        connection.autocommit = True # for saving changes into DB
        print("> Database is connected successfully.")
        return connection
    except:
        print("> ERROR during the connection to the database.")

def query(query, param=None):
    connection = None
    cur = None

    try:
        # establishing connection with the db
        connection = connection_to_db()
        cur = connection.cursor()

        # getting information from the db
        if param is not None:
            cur.execute(query, param)
        else:
            cur.execute(query)

        if cur.description is not None:
            # each item in the line combine with its name (first item in the row)
            # return array of dictionaries (key - name, item - data)
            return [dict(zip([s[0] for s in cur.description], line)) for line in cur]
        else:
            return None

    except psycopg2.InterfaceError as exc:
        print(exc.message)

    finally:
        # ending connection to the db
        if cur is not None:
            cur.close()
        if connection is not None:
            connection.close()