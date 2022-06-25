const assert = require("assert");
const axios = require("axios");

describe("Order end to end test", function () {
   it("Get all products", function (done) {
      axios
         .get("http://localhost:8080/products")
         .then(function (res) {
            axios.defaults.headers.common.Cookie = res.headers["set-cookie"][0].split(";")[0];

            assert(res.data.length == 5);

            const info = [
               {
                  id: 1,
                  name: "Computer",
                  picture: "https://cdn.pixabay.com/photo/2014/09/24/14/29/macbook-459196_960_720.jpg",
                  price: "500.50",
               },
               {
                  id: 2,
                  name: "Phone",
                  picture: "https://cdn.pixabay.com/photo/2014/10/23/16/58/phone-499991_960_720.jpg",
                  price: "120",
               },
               {
                  id: 3,
                  name: "Mouse",
                  picture: "https://cdn.pixabay.com/photo/2017/05/24/21/33/workplace-2341642_960_720.jpg",
                  price: "100",
               },
               {
                  id: 4,
                  name: "GoPro",
                  picture: "https://cdn.pixabay.com/photo/2017/08/06/10/42/camera-2591164_960_720.jpg",
                  price: "700.01",
               },
               {
                  id: 5,
                  name: "Earphones",
                  picture: "https://cdn.pixabay.com/photo/2020/04/19/16/33/headphones-5064411_960_720.jpg",
                  price: "99.99",
               },
            ];
            assert.deepEqual(res.data, info);
            done();
         })
         .catch(done);
   });

   it("Add product to cart", function (done) {
      axios
         .get("http://localhost:8080/addToCart?id=1")
         .then(function (res) {
            assert(res.status == "200");
            assert(res.statusText == "OK");
            done();
         })
         .catch(done);
   });

   it("Get cart", function (done) {
      axios
         .get("http://localhost:8080/cart")
         .then(function (res) {
            assert(res.data[0].id == "1");

            const first_product = {
               id: 1,
               name: "Computer",
               picture: "https://cdn.pixabay.com/photo/2014/09/24/14/29/macbook-459196_960_720.jpg",
               price: "500.50",
               quantity: 1,
            };

            assert.deepEqual(res.data[0], first_product);
            done();
         })
         .catch(done);
   });

   it("Change quantity of the products", function (done) {
      axios
         .get("http://localhost:8080/addToCart?id=1&quantity=3")
         .then(function (res) {
            assert(res.status == "200");
            assert(res.statusText == "OK");
            done();
         })
         .catch(done);
   });

   var order = null;
   it("Create order", function (done) {
      const person = {
         name: "John Smith",
         email: Math.random().toString(36).slice(-8) + "@gmail.com",
         street: "Fifth Avenue",
         number: "25",
         city: "London",
         zip: "65739",
      };

      axios
         .post("http://localhost:8080/order", person)
         .then(function (res) {
            assert(res.status == "200");
            assert(res.statusText == "OK");
            const user = Object.assign({}, res.data.user);
            delete user.id;
            assert.deepEqual(user, person);
            order = res.data;
            done();
         })
         .catch(done);
   });

   it("Check if order is in the orders list.", function (done) {
      axios
         .get("http://localhost:8080/orders")
         .then(function (res) {
            const filterOrders = res.data.filter((item) => item.order_id == order.order.id);
            assert(filterOrders.length == 1);
            done();
         })
         .catch(done);
   });
});
