const cors = require("cors");
const express = require("express");
const express_session = require("express-session");

const database = require("pg");
const server = express();
const session = express_session({
   secret: "nejdem_sa_zabit",
   saveUninitialized: true,
   resave: false,
});
const carts = {};

server.use(session);
server.use(cors({ origin: "http://localhost:3000", credentials: true }));
server.use(express.urlencoded({ extended: true }));
server.use(express.json());

const db = new database.Pool({
   user: "postgres",
   host: "postgres",
   database: "eshop",
   password: "postgres",
   port: 5432,
});

server.get("/products", function (req, res) {
   db.query("select * from products").then(function (data) {
      res.json(data.rows);
   });
});

server.get("/addToCart", function (req, res) {
   const user_id = req.sessionID;
   const product_id = req.query.id;
   const product_quantity = parseInt(req.query.quantity) || 1;

   if (user_id in carts) {
      const prod = carts[user_id].filter(function (product) {
         return product.product_id == product_id;
      });
      if (prod.length == 1 && prod[0].quantity + product_quantity > 0) prod[0].quantity += product_quantity;
      else if (prod.length == 1 && prod[0].quantity + product_quantity == 0) {
         const index = carts[user_id].indexOf(prod[0]);
         if (index > -1) {
            carts[user_id].splice(index, 1);
         }
      } else carts[user_id].push({ product_id, quantity: product_quantity });
   } else {
      carts[user_id] = [{ product_id, quantity: product_quantity }];
   }
   res.sendStatus(200);
});

server.get("/cart", function (req, res) {
   const user_id = req.sessionID;
   const ids = {};
   (carts[user_id] || []).forEach(function (product) {
      ids[product.product_id] = product.quantity;
   });

   db.query("select * from products where id = ANY(ARRAY[" + Object.keys(ids).join(",") + "]::integer[])").then(function (data) {
      data.rows.forEach(function (row) {
         row.quantity = ids[row.id];
      });
      res.json(data.rows);
   });
});

server.post("/order", async function (req, res) {
   const session_id = req.sessionID;
   const name = req.body.name;
   const email = req.body.email;
   const street = req.body.street;
   const number = req.body.number;
   const city = req.body.city;
   const zip = req.body.zip;

   var user_data;
   try {
      user_data = await db.query("insert into clients (name, email, street, number, city, zip) values($1, $2, $3, $4, $5, $6) returning *;", [
         name,
         email,
         street,
         number,
         city,
         zip,
      ]);
   } catch (error) {
      return res.sendStatus(500);
   }
   const user_id = user_data.rows[0].id;

   const order_data = await db.query("insert into orders (clients_id, is_paid) values($1, $2) returning *;", [user_id, false]);
   const order_id = order_data.rows[0].id;

   const order_products = await db.query(
      "insert into order_product (order_id, products_id, quantity) values" +
         carts[session_id]
            .map(function (cart_item) {
               return "(" + order_id + "," + cart_item.product_id + "," + cart_item.quantity + ")";
            })
            .join(",")
   );

   delete carts[session_id];
   res.json({ user: user_data.rows[0], order: order_data.rows[0] });
});

server.get("/advertisement", function (req, res) {
   db.query("select * from advertisements").then(function (data) {
      res.json(data.rows[0]);
   });
});

server.post("/advertisement", function (req, res) {
   db.query("update advertisements set counter = counter + 1 returning *").then(function (data) {
      res.json(data.rows[0]);
   });
});

server.get("/orders", function (req, res) {
   db.query(
      `select *, 
      row_number() over(partition by order_id order by order_product_id asc) as rn,
      count(*) over(partition by order_id) as prod_count,
      sum(price * quantity) over(partition by order_id) as total 
	    from (select *, clients.name as clients_name, order_product.id as order_product_id, 
      products.name as products_name from order_product
      inner join orders on orders.id=order_product.order_id 
      inner join clients on clients.id=orders.clients_id 
      inner join products on products.id=order_product.products_id) T
	    order by order_id, order_product_id asc`
   ).then(function (data) {
      res.json(data.rows);
   });
});

server.post("/advertisement/update", function (req, res) {
   const counter_is_reset = req.body.counter;
   const picture = req.body["picture-link"];
   const link = req.body["advertisement-link"];

   if (counter_is_reset) {
      db.query("update advertisements set counter = 0 returning *").then(function (data) {
         res.json(data.rows[0]);
      });
   } else if (picture) {
      db.query("update advertisements set picture = $1 returning *", [picture]).then(function (data) {
         res.json(data.rows[0]);
      });
   } else if (link) {
      db.query("update advertisements set link = $1 returning *", [link]).then(function (data) {
         res.json(data.rows[0]);
      });
   }
});

server.post("/order/pay/:id", function (req, res) {
   db.query("update orders set is_paid = true where id = $1 returning *", [req.params.id]).then(function (data) {
      res.json(data.rows[0]);
   });
});

server.listen((port = 8080));
