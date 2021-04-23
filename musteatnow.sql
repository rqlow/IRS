SET time_zone='+08:00';

-- USER SET UP
drop table if exists musteatnow.user_master;
create table musteatnow.user_master (
userid INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY
,firstname varchar(255)
,lastname varchar(255)
,displayname varchar(255) not null
,household_name varchar(255) not null
,email varchar(255) not null
,reg_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

insert into musteatnow.user_master(firstname, lastname, displayname, household_name, email)
values ('konchok', 'lim', 'kc_lim','asahi_village', 'konchok.lim@gmail.com')
;

create index idx_user_master
on musteatnow.user_master (userid asc)
;

select * from musteatnow.user_master;




-- STOCK IN LOG TABLE SETUP
drop table if exists musteatnow.stock_in;
create table musteatnow.stock_in (
stockin_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
,stock_id bigint(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY
,userid INT(8) not null
,food_id varchar(255) not null
,prod_name varchar(255)
,serving float
);

truncate table musteatnow.stock_in;
insert into musteatnow.stock_in(userid, food_id, prod_name, serving)
values 
(1, 4, 'seng choon', 10),
(1, 239, 'Chicken breast', 6),
(1, 205, 'cold storage', 1),
(1, 194, 'green fields', 1),
(1, 194, 'farmers union', 1)
;

update musteatnow.stock_in
set stockin_dt= date_add(CURRENT_TIMESTAMP, interval -11 day)
where stock_id=1
;


create index idx_stock_in
on musteatnow.stock_in (stock_id asc, stockin_dt desc)
;

select * from musteatnow.stock_in;




-- STOCK OUT TABLE SETUP
drop table if exists musteatnow.stock_out;
create table musteatnow.stock_out (
row_id int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY
,userid INT(8) not null
,stock_id bigint(10) not null
,stockout_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
,serving_out float
,consumed_disposed int(1)
);

truncate table musteatnow.stock_out;
insert into musteatnow.stock_out(userid, stock_id, serving_out, consumed_disposed)
values 
(1, 2, -2, 1),
(1, 2, -2, 1),
(1, 2, -1, 1),
(1, 2, -1, 0)
;


create index idx_stock_out
on musteatnow.stock_out (stock_id asc, stockout_dt desc)
;


select * from musteatnow.stock_out
;



-- STOCK MASTER TABLE
drop table if exists musteatnow.stock_master;
create table musteatnow.stock_master (
food_id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY
,food_cat varchar(255)
,food_id_name varchar(255) not null unique
,default_expiry_days float
,default_serving float
,insert_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

insert into musteatnow.stock_master (food_cat, food_id_name, default_expiry_days, default_serving)
values
('Dairy','Milk', 14, 1),
('Dairy','Ice-Cream', 60, 1),
('Dairy', 'Yogurt', 7, 1),
('Dairy', 'Eggs', 14, 10),
('Dairy', 'Cheese',60 , 1),
('Dairy', 'cream',14, 1),
('Dairy', 'sour cream',14, 1),
('Dairy', 'butter',14, 1),
('Seafood', 'Fresh Fish', 8, 1),
('Seafood', 'Crab', 8, 1),
('Seafood', 'Processed Seafood', 30, 1),
('Vegetable', 'Fresh Vegetable', 8, 1),
('Vegetable', 'garlic', 10, 1),
('Vegetable', 'onion', 10, 1),
('Vegetable', 'parsley', 10, 1),
('Vegetable', 'cilantro', 10, 1),
('Vegetable', 'potato', 10, 1),
('Vegetable', 'spinach', 10, 1),
('Vegetable', 'chive', 10, 1),
('Vegetable', 'ginger', 10, 1),
('Vegetable', 'chilli', 10, 1),
('Fruit', 'Apples',10, 1),
('Fruit', 'Bananas', 10, 10),
('Fruit', 'pineapple', 10, 1),
('Fruit', 'Pear', 10, 1),
('Fruit', 'mango', 10, 1),
('Fruit', 'strawberries', 10, 1),
('Fruit', 'berries', 10, 1),
('Fruit', 'grapes', 10, 1),
('Fruit', 'blueberries', 10, 1),
('Fruit', 'avocado', 10, 1),
('Fruit', 'carrot', 10, 1),
('Fruit', 'peppers', 10, 1),
('Fruit', 'tomato', 10, 1),
('Beverage','Fruit Juice', 30, 1),
('Beverage','yakult', 14, 1),
('Beverage','Vitagen', 14, 1),
('Grain', 'Bread', 8, 1),
('Grain', 'Bun', 3, 1),
('Grain', 'rice', 365, 1),
('Grain', 'quinoe', 90, 1),
('Grain', 'tortilla', 90, 1),
('Grain', 'pasta', 90, 1),
('Grain', 'sugar', 90, 1),
('Grain', 'biscuits', 90, 1),
('Meat', 'Chicken',9, 1),
('Meat', 'Pork',9, 1),
('Meat', 'Beef',9, 1),
('Meat', 'sausage',9, 1),
('Meat', 'ham',9, 1)
;

-- drop index idx_stock_master on musteatnow.stock_master;
create index idx_stock_master
on musteatnow.stock_master (food_id asc, food_id_name asc)
;

-- FOOD TAG TABLE
drop table if exists musteatnow.stock_tag;
create table musteatnow.stock_tag (
rowid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
,food_id INT not null
,food_tag_raw varchar(255) not null
,food_tag varchar(255) as (regexp_replace(food_tag_raw, '[^\\x20-\\x7E]', ' '))
,food_id_tag varchar(280) as (concat(food_id, food_tag))
,insert_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
); 


insert into musteatnow.stock_tag (food_id, food_tag_raw)
values
(194,'milk'),
(195,'ice cream'),
(195,'ice-cream'),
(196,'yogurt'),
(197,'egg'),
(198,'cheese'),
(199,'cream'),
(200,'sour cream'),
(201,'butter'),
(202,'fish'),
(203,'crab'),
(204,'seafood'),
(205,'vegetables'),
(205,'vegetable'),
(205,'vege'),
(205,'veggie'),
(205,'veggies'),
(206,'garlic'),
(206,'garlics'),
(207,'onion'),
(207,'onions'),
(208,'parsley'),
(209,'cilantro'),
(210,'potato'),
(210,'potatoes'),
(211,'spinach'),
(212,'chive'),
(212,'chives'),
(213,'ginger'),
(213,'gingers'),
(214,'chili'),
(215,'apple'),
(215,'apples'),
(215,'appl'),
(216,'banana'),
(216,'bananas'),
(217,'pineapple'),
(217,'pineapples'),
(218,'pear'),
(218,'pears'),
(219,'mango'),
(219,'mangos'),
(220,'strawberries'),
(220,'strawberry'),
(221,'berry'),
(221,'berries'),
(222,'grape'),
(223,'blueberry'),
(223,'blueberries'),
(224,'avocado'),
(224,'avocados'),
(225,'carrot'),
(225,'carrots'),
(226,'pepper'),
(226,'peppers'),
(227,'tomato'),
(227,'tomatoes'),
(228,'fruit juice'),
(229,'yakult'),
(230,'vitagen'),
(231,'bread'),
(232,'bun'),
(232,'buns'),
(233,'rice'),
(234,'quinoe'),
(235,'tortilla'),
(236,'pasta'),
(236,'spaghetti'),
(236,'macaroni'),
(236,'lagsagne'),
(236,'linguine'),
(236,'orzo'),
(236,'rigatoni'),
(236,'penne'),
(236,'fusilli'),
(237,'sugar'),
(238,'biscuits'),
(238,'biscuit'),
(239,'chicken'),
(239,'chick'),
(240,'pork'),
(241,'beef'),
(242,'sausage'),
(242,'sausages'),
(243,'ham'),
(243,'bacon'),
(243,'culatello'),
(243,'iberico'),
(243,'prosciutto'),
(243,'serrano')
;

create index idx_stock_tag
on musteatnow.stock_tag (food_id asc, food_tag asc)
;


-- create view musteatnow.vw_current_stock as
alter view musteatnow.vw_current_stock as
with stock_out as
(select
	userid
	,stock_id 
	,max(stockout_dt) as last_stockout_dt
	,sum(case 
			when consumed_disposed=1 then serving_out 
            else 0 end
		) as consumed
        
	,sum(case 
			when consumed_disposed=0 then serving_out 
            else 0 end
		) as disposed
from musteatnow.stock_out
group by
	userid
	,stock_id 
),

stock_tag as
(select
	food_id
    ,group_concat(distinct food_tag order by food_tag asc separator ', ') as food_tags
    from musteatnow.stock_tag
    group by food_id
),

base as
(select 
	i.userid
	,u.displayname
	,i.stockin_dt
	,o.last_stockout_dt
    ,date_add(i.stockin_dt, INTERVAL m.default_expiry_days DAY) as expiry_dt
    ,datediff(
				date_add(i.stockin_dt, INTERVAL m.default_expiry_days DAY)
				,CURRENT_TIMESTAMP
            ) as days_left
	,i.stock_id
	,m.food_cat
	,lower(m.food_id_name) as food_id_name
	,lower(i.prod_name) as prod_name
	,o.consumed
    ,o.disposed
    ,(i.serving + o.consumed + o.disposed) as serving_left
    ,t.food_tags

from musteatnow.stock_in 			as i
inner join musteatnow.user_master 	as u on i.userid=u.userid
inner join musteatnow.stock_master 	as m on i.food_id=m.food_id	
left join stock_out 				as o on i.stock_id=o.stock_id
left join stock_tag					as t on i.food_id=t.food_id
)
 select * from base
 ;