const database = require("pg");
const db = new database.Pool({
   user: "postgres",
   host: "postgres",
   database: "eshop",
   password: "postgres",
   port: 5432,
});

console.log("RUNNING MIGRATION!!!!!!!!!!");

const query = `
create table if not exists Products(
	id serial primary key,
	name varchar,
	picture varchar,
	price numeric
);

create table if not exists Clients(
	id serial primary key,
	email varchar unique,
	name varchar,
	street varchar,
	number varchar,
	city varchar,
	zip int
);

create table if not exists Orders(
	id serial primary key,
	clients_id int references Clients(id),
	is_paid boolean
);

create table if not exists Order_product(
	id serial primary key,
	order_id int references Orders(id),
	products_id int references Products(id),
	quantity int
);

create table if not exists Advertisements(
	id serial primary key,
	picture varchar,
	link varchar,
	counter int
);

insert into Products(id, name, picture, price) values
	(1, 'Computer', 'https://cdn.pixabay.com/photo/2014/09/24/14/29/macbook-459196_960_720.jpg', 500.50),
	(2, 'Phone', 'https://cdn.pixabay.com/photo/2014/10/23/16/58/phone-499991_960_720.jpg', 120),
	(3, 'Mouse', 'https://cdn.pixabay.com/photo/2017/05/24/21/33/workplace-2341642_960_720.jpg', 100),
	(4, 'GoPro', 'https://cdn.pixabay.com/photo/2017/08/06/10/42/camera-2591164_960_720.jpg', 700.01),
	(5, 'Earphones', 'https://cdn.pixabay.com/photo/2020/04/19/16/33/headphones-5064411_960_720.jpg', 99.99)
	ON CONFLICT DO NOTHING;
	
insert into Advertisements(id, picture, link, counter) values 
	(1, 'https://cdn.pixabay.com/photo/2019/11/06/14/26/black-friday-social-media-post-4606225_960_720.png',
	'https://www.youtube.com/watch?v=rjWv5EWvrYk', 0) 
	ON CONFLICT DO NOTHING;
`;

db.query(query);
