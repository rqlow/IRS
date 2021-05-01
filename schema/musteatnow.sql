drop schema if exists musteatnow;
create schema musteatnow;
set SQL_SAFE_UPDATES = 0;
set time_zone='+08:00';

-- USER login setup
drop table if exists musteatnow.user_master;
create table musteatnow.user_master (
userid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
,firstname varchar(255)
,lastname varchar(255)
,displayname varchar(255) not null
,household_name varchar(255) not null unique
,email varchar(255) not null
,password varchar (255)
,reg_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
row_format=compressed
key_block_size=8
;

insert into musteatnow.user_master(firstname, lastname, displayname, household_name, email, password)
values
('my', 'family', 'my_family','asahi_village', 'my_family@example.com', 'irs2021')
,('luca', 'teh', 'luca_teh','honey_wood', 'lucateh@example.com', 'goodpw12')
;

create index idx_user_master
on musteatnow.user_master (userid asc, household_name asc)
;


-- STOCK IN LOG TABLE SETUP
drop table if exists musteatnow.stock_in;
create table musteatnow.stock_in (
stockin_dt TIMESTAMP as (ifnull(user_dt, db_dt))
,stock_id bigint UNSIGNED AUTO_INCREMENT PRIMARY KEY
,userid INT not null
,food_id varchar(255) not null
,prod_name varchar(255)
,serving float
,price decimal(10,2)
,user_dt TIMESTAMP
,db_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
row_format=compressed
key_block_size=8
;


insert into musteatnow.stock_in(userid, food_id, prod_name, serving)
values 
-- household 1 groceries
(1, 4, 'seng choon', 10)
,(1, 46, 'Chicken breast', 6)
,(1, 12, 'cold storage', 1)
,(1, 1, 'green fields', 1)
,(1, 1, 'farmers union', 1)
,(1, 3, 'greek yogurt', 2)
,(1, 2, 'Häagen-Dazs', 2)
,(1, 8, 'scs salted butter', 1)
,(1, 36, 'yakult', 5)
,(1, 22, 'fuji apple', 1)
,(1, 40, 'golden phoenix thai mix grain', 10)
,(1, 3, 'meiji yogurt', 2)
,(1, 2, 'Ben & Jerrys', 2)
,(1, 8, 'scs salted butter', 1)
,(1, 36, 'yakult', 5)
,(1, 22, 'fuji apple', 1)
,(1, 40, 'golden phoenix thai mix grain', 1)
,(1, 47, 'pork belly', 1)
,(1, 3, 'meiji yogurt', 1)
,(1, 2, 'Häagen-Dazs', 6)
,(1, 8, 'Farmers pride', 1)
,(1, 36, 'yakult', 5)
,(1, 22, 'gala apples', 5)
,(1, 40, 'golden phoenix thai mix grain', 1)
,(1, 3, 'greek yogurt', 2)
,(1, 2, 'Gelato', 6)
,(1, 8, 'bordier butter', 1)
,(1, 36, 'yakult', 5)
,(1, 22, 'busan apple', 1)
,(1, 40, 'golden phoenix thai mix grain', 1)
,(1, 47, 'pork loin', 1)
,(1, 3, 'plain yogurt', 2)
,(1, 2, 'Walls ice cream', 2)
,(1, 8, 'scs butter', 1)
,(1, 36, 'yakult', 5)
,(1, 22, 'apples', 1)
,(1, 40, 'organicbrown rice', 1)
,(1, 3, 'yogurt', 2)
,(1, 2, 'Ben & Jerrys', 2)
,(1, 8, 'scs unsalted butter', 1)
,(1, 36, 'yakult', 5)
,(1, 22, 'Busan apple', 1)
,(1, 40, 'golden phoenix thai mix grain', 1)
,(1, 47, 'pork belly', 1)
,(1, 3, 'meiji yogurt', 1)
,(1, 2, 'Häagen-Dazs', 6)
,(1, 8, 'Farmers pride', 1)
,(1, 36, 'yakult', 5)
,(1, 22, 'gala apples', 5)
,(1, 40, 'AAA Halong mix grain', 1)
,(1, 3, 'greek yogurt', 2)
,(1, 2, 'Gelato', 6)
,(1, 8, 'bordier butter', 1)
,(1, 36, 'yakult', 5)
,(1, 22, 'busan apple', 1)
,(1, 40, 'golden phoenix thai mix grain', 1)
,(1, 47, 'pork loin', 1)

-- Next household
,(2, 3, 'greek yogurt', 2)
,(2, 2, 'Häagen-Dazs', 2)
,(2, 8, 'scs salted butter', 1)
,(2, 36, 'yakult', 5)
,(2, 22, 'fuji apple', 1)
,(2, 234, 'green leaf organic quinoa', 1)
,(2, 3, 'meiji yogurt', 2)
,(2, 2, 'Ben & Jerrys', 2)
,(2, 8, 'scs salted butter', 1)
,(2, 36, 'yakult', 5)
,(2, 22, 'fuji apple', 1)
,(2, 40, 'royal basmati mix grain', 1)
,(2, 47, 'pork shoulder', 1)
,(2, 3, 'green fields natural yogurt', 1)
,(2, 2, 'bloom gelato', 6)
,(2, 8, 'Farmers pride', 1)
,(2, 36, 'yakult', 5)
,(2, 22, 'gala apples', 5)
,(2, 40, 'golden phoenix thai mix grain', 1)
,(2, 3, 'greek yogurt', 2)
,(2, 2, 'Gelato', 6)
,(2, 8, 'bordier butter', 1)
,(2, 36, 'yakult', 5)
,(2, 22, 'busan apple', 1)
,(2, 40, 'golden  thai mix grain', 1)
,(2, 47, 'pork loin', 1)
;

-- Modifying datetime stamps to simulate different purchase dates
update musteatnow.stock_in
set db_dt= date_add(CURRENT_TIMESTAMP, interval -49 day)
where stock_id>=8 and stock_id<14
;

update musteatnow.stock_in
set db_dt= date_add(CURRENT_TIMESTAMP, interval -40 day)
where stock_id>=15 and stock_id<21
;

update musteatnow.stock_in
set db_dt= date_add(CURRENT_TIMESTAMP, interval -32 day)
where stock_id>=22 and stock_id<28
;

update musteatnow.stock_in
set db_dt= date_add(CURRENT_TIMESTAMP, interval -26 day)
where stock_id>=29 and stock_id<35
;

update musteatnow.stock_in
set db_dt= date_add(CURRENT_TIMESTAMP, interval -18 day)
where stock_id>=36 and stock_id<42
;

update musteatnow.stock_in
set db_dt= date_add(CURRENT_TIMESTAMP, interval -12 day)
where stock_id>=43 and stock_id<49
;

update musteatnow.stock_in
set db_dt= date_add(CURRENT_TIMESTAMP, interval -4 day)
where stock_id>=50 and stock_id<58
;

create index idx_stock_in
on musteatnow.stock_in (stock_id asc, stockin_dt desc)
;


-- STOCK OUT TABLE SETUP
drop table if exists musteatnow.stock_out;
create table musteatnow.stock_out (
row_id int UNSIGNED AUTO_INCREMENT PRIMARY KEY
,userid INT not null
,stock_id bigint not null
,stockout_dt TIMESTAMP as (ifnull(user_dt, db_dt))
,serving_out float
,consumed_disposed int(1)
,user_dt TIMESTAMP
,db_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
row_format=compressed
key_block_size=8
;

insert into musteatnow.stock_out(userid, stock_id, user_dt, serving_out, consumed_disposed)
values
(1, 3, date_add(CURRENT_TIMESTAMP, interval -1 day), 1,1)
,(1, 9, date_add(CURRENT_TIMESTAMP, interval -49 day), 1,1)
,(1, 9, date_add(CURRENT_TIMESTAMP, interval -48 day), 1,1)
,(1, 9, date_add(CURRENT_TIMESTAMP, interval -47 day), 2,1)
,(1, 9, date_add(CURRENT_TIMESTAMP, interval -46 day), 1,1)
,(1, 10, date_add(CURRENT_TIMESTAMP, interval -46 day), 1,1)
,(1, 12, date_add(CURRENT_TIMESTAMP, interval -49 day), 1,1)
,(1, 12, date_add(CURRENT_TIMESTAMP, interval -48 day), 1,1)
,(1, 13, date_add(CURRENT_TIMESTAMP, interval -47 day), 1,1)
,(1, 13, date_add(CURRENT_TIMESTAMP, interval -46 day), 1,1)
,(1, 16, date_add(CURRENT_TIMESTAMP, interval -40 day), 1,1)
,(1, 17, date_add(CURRENT_TIMESTAMP, interval -40 day), 1,1)
,(1, 18, date_add(CURRENT_TIMESTAMP, interval -40 day), 1,1)
,(1, 19, date_add(CURRENT_TIMESTAMP, interval -40 day), 1,1)
,(1, 20, date_add(CURRENT_TIMESTAMP, interval -39 day), 1,1)
,(1, 20, date_add(CURRENT_TIMESTAMP, interval -38 day), 1,1)
,(1, 20, date_add(CURRENT_TIMESTAMP, interval -38 day), 1,1)
,(1, 20, date_add(CURRENT_TIMESTAMP, interval -38 day), 2,1)
,(1, 20, date_add(CURRENT_TIMESTAMP, interval -38 day), 1,0)
,(1, 22, date_add(CURRENT_TIMESTAMP, interval -33 day), 1,1)
,(1, 23, date_add(CURRENT_TIMESTAMP, interval -33 day), 1,1)
,(1, 23, date_add(CURRENT_TIMESTAMP, interval -32 day), 1,1)
,(1, 23, date_add(CURRENT_TIMESTAMP, interval -31 day), 1,1)
,(1, 23, date_add(CURRENT_TIMESTAMP, interval -30 day), 1,0)
,(1, 23, date_add(CURRENT_TIMESTAMP, interval -30 day), 1,0)
,(1, 33, date_add(CURRENT_TIMESTAMP, interval -27 day), 1,1)
,(1, 34, date_add(CURRENT_TIMESTAMP, interval -27 day), 0.5,1)
,(1, 38, date_add(CURRENT_TIMESTAMP, interval -18 day), 1,1)
,(1, 39, date_add(CURRENT_TIMESTAMP, interval -18 day), 1,1)
,(1, 41, date_add(CURRENT_TIMESTAMP, interval -19 day), 1,1)
,(1, 41, date_add(CURRENT_TIMESTAMP, interval -16 day), 1,1)
,(1, 41, date_add(CURRENT_TIMESTAMP, interval -17 day), 1,1)
,(1, 43, date_add(CURRENT_TIMESTAMP, interval -13 day), 1,1)
,(1, 44, date_add(CURRENT_TIMESTAMP, interval -13 day), 1,1)
,(1, 45, date_add(CURRENT_TIMESTAMP, interval -13 day), 1,1)
,(1, 46, date_add(CURRENT_TIMESTAMP, interval -13 day), 1,1)
,(1, 46, date_add(CURRENT_TIMESTAMP, interval -12 day), 1,1)
,(1, 46, date_add(CURRENT_TIMESTAMP, interval -11 day), 1,1)
,(1, 46, date_add(CURRENT_TIMESTAMP, interval -10 day), 1,0)
,(1, 47, date_add(CURRENT_TIMESTAMP, interval -10 day), 1,1)
,(1, 48, date_add(CURRENT_TIMESTAMP, interval -13 day), 1,1)
,(1, 48, date_add(CURRENT_TIMESTAMP, interval -12 day), 1,1)
,(1, 48, date_add(CURRENT_TIMESTAMP, interval -11 day), 1,0)
,(1, 48, date_add(CURRENT_TIMESTAMP, interval -10 day), 1,1)
,(1, 50, date_add(CURRENT_TIMESTAMP, interval -5 day), 0.6,1)
,(1, 51, date_add(CURRENT_TIMESTAMP, interval -4 day), 1,1)
,(1, 52, date_add(CURRENT_TIMESTAMP, interval -5 day), 1,1)
,(1, 52, date_add(CURRENT_TIMESTAMP, interval -4 day), 2,1)
,(1, 52, date_add(CURRENT_TIMESTAMP, interval -3 day), 1,1)
,(1, 52, date_add(CURRENT_TIMESTAMP, interval -2 day), 1,0)
,(1, 54, date_add(CURRENT_TIMESTAMP, interval -5 day), 1,1)
,(1, 54, date_add(CURRENT_TIMESTAMP, interval -4 day), 2,1)
,(1, 54, date_add(CURRENT_TIMESTAMP, interval -3 day), 1,1)
,(1, 54, date_add(CURRENT_TIMESTAMP, interval -2 day), 1,0)
,(1, 1, date_add(CURRENT_TIMESTAMP, interval -1 day), 2,1)
,(1, 1, date_add(CURRENT_TIMESTAMP, interval -1 day), 1,0)
;

create index idx_stock_out
on musteatnow.stock_out (stock_id asc, stockout_dt desc)
;

# once we are done with row level updates, we'll restore safe_updates to ON.
SET SQL_SAFE_UPDATES = 1;


drop table if exists musteatnow.food_cat_master;
create table musteatnow.food_cat_master (
food_cat_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
,food_cat_name varchar(255) not null unique
,insert_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
row_format=compressed
key_block_size=8
;

insert into musteatnow.food_cat_master (food_cat_name)
values
('Dairy')
,('Seafood')
,('Vegetable')
,('Fruit')
,('Beverage')
,('Grain')
,('Meat')
;

create index idx_food_cat_master
on musteatnow.food_cat_master (food_cat_id asc)
;


-- STOCK MASTER TABLE
drop table if exists musteatnow.stock_master;
create table musteatnow.stock_master (
food_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
,food_cat_id INT
,food_id_name varchar(255) not null unique
,default_expiry_days float
,default_serving float
,insert_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
row_format=compressed
key_block_size=8
;

insert into musteatnow.stock_master (food_cat_id, food_id_name, default_expiry_days, default_serving)
values
(1,'Milk', 14, 1),
(1,'Ice-Cream', 60, 1),
(1, 'Yogurt', 7, 1),
(1, 'Eggs', 14, 10),
(1, 'Cheese',60 , 1),
(1, 'cream',14, 1),
(1, 'sour cream',14, 1),
(1, 'butter',14, 1),
(2, 'Fresh Fish', 8, 1),
(2, 'Crab', 8, 1),
(2, 'Processed Seafood', 30, 1),
(3, 'Fresh Vegetable', 8, 1),
(3, 'garlic', 10, 1),
(3, 'onion', 10, 1),
(3, 'parsley', 10, 1),
(3, 'cilantro', 10, 1),
(3, 'potato', 10, 1),
(3, 'spinach', 10, 1),
(3, 'chive', 10, 1),
(3, 'ginger', 10, 1),
(3, 'chilli', 10, 1),
(4, 'Apples',10, 1),
(4, 'Bananas', 10, 10),
(4, 'pineapple', 10, 1),
(4, 'Pear', 10, 1),
(4, 'mango', 10, 1),
(4, 'strawberries', 10, 1),
(4, 'berries', 10, 1),
(4, 'grapes', 10, 1),
(4, 'blueberries', 10, 1),
(4, 'avocado', 10, 1),
(4, 'carrot', 10, 1),
(4, 'peppers', 10, 1),
(4, 'tomato', 10, 1),
(5,'Fruit Juice', 30, 1),
(5,'yakult', 14, 1),
(5,'Vitagen', 14, 1),
(6, 'Bread', 8, 1),
(6, 'Bun', 3, 1),
(6, 'rice', 365, 1),
(6, 'quinoe', 90, 1),
(6, 'tortilla', 90, 1),
(6, 'pasta', 90, 1),
(6, 'sugar', 90, 1),
(6, 'biscuits', 90, 1),
(7, 'Chicken',9, 1),
(7, 'Pork',9, 1),
(7, 'Beef',9, 1),
(7, 'sausage',9, 1),
(7, 'ham',9, 1)
;

create index idx_stock_master
on musteatnow.stock_master (food_id asc, food_id_name asc)
;


-- FOOD TAG TABLE
drop table if exists musteatnow.stock_tag;
create table musteatnow.stock_tag (
rowid INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
,food_id INT not null
,food_tag_raw varchar(255) not null
,food_tag varchar(255) as (trim(regexp_replace(food_tag_raw, '[^A-Za-z0-9 ]', ' ')))
,insert_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP )
row_format=compressed
key_block_size=8
; 

insert into musteatnow.stock_tag (food_id, food_tag_raw)
values
(1,'milk'),
(2,'ice cream'),
(2,'ice-cream'),
(3,'yogurt'),
(4,'egg'),
(5,'cheese'),
(6,'cream'),
(7,'sour cream'),
(8,'butter'),
(9,'fish'),
(10,'crab'),
(11,'seafood'),
(12,'vegetables'),
(12,'vegetable'),
(12,'vege'),
(12,'veggie'),
(12,'veggies'),
(13,'garlic'),
(13,'garlics'),
(14,'onion'),
(14,'onions'),
(15,'parsley'),
(16,'cilantro'),
(17,'potato'),
(17,'potatoes'),
(18,'spinach'),
(19,'chive'),
(19,'chives'),
(20,'ginger'),
(20,'gingers'),
(21,'chili'),
(22,'apple'),
(22,'apples'),
(22,'appl'),
(23,'banana'),
(23,'bananas'),
(24,'pineapple'),
(24,'pineapples'),
(25,'pear'),
(25,'pears'),
(26,'mango'),
(26,'mangos'),
(27,'strawberries'),
(27,'strawberry'),
(28,'berry'),
(28,'berries'),
(29,'grape'),
(30,'blueberry'),
(30,'blueberries'),
(31,'avocado'),
(31,'avocados'),
(32,'carrot'),
(32,'carrots'),
(33,'pepper'),
(33,'peppers'),
(34,'tomato'),
(34,'tomatoes'),
(35,'fruit juice'),
(36,'yakult'),
(37,'vitagen'),
(38,'bread'),
(38,'bun'),
(38,'buns'),
(40,'rice'),
(41,'quinoe'),
(42,'tortilla'),
(43,'pasta'),
(43,'spaghetti'),
(43,'macaroni'),
(43,'lagsagne'),
(43,'linguine'),
(43,'orzo'),
(43,'rigatoni'),
(43,'penne'),
(43,'fusilli'),
(44,'sugar'),
(45,'biscuits'),
(45,'biscuit'),
(46,'chicken'),
(46,'chick'),
(47,'pork'),
(47,'bacon'),
(48,'beef'),
(49,'sausage'),
(49,'sausages'),
(50,'ham'),
(50,'culatello'),
(50,'iberico'),
(50,'prosciutto'),
(50,'serrano')
;

create index idx_stock_tag
on musteatnow.stock_tag (food_id asc, food_tag asc)
;

# Generate logical views
drop view if exists musteatnow.vw_current_stock;
create view musteatnow.vw_current_stock as
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
	,c.food_cat_name as food_cat
	,lower(m.food_id_name) as food_id_name
	,lower(i.prod_name) as prod_name
    ,i.serving
	,ifnull(o.consumed, 0) as consumed
    ,ifnull(o.disposed, 0) as disposed
    ,round(ifnull((i.serving - (o.consumed + o.disposed)),serving),2) as serving_left
    ,t.food_tags

from musteatnow.stock_in 				as i
inner join musteatnow.user_master 		as u on i.userid=u.userid
inner join musteatnow.stock_master 		as m on i.food_id=m.food_id
inner join musteatnow.food_cat_master 	as c on m.food_cat_id=c.food_cat_id
left join stock_out 					as o on i.stock_id=o.stock_id
left join stock_tag						as t on i.food_id=t.food_id
)
select * from base
;

drop view if exists musteatnow.vw_consumption_rpt;
create view musteatnow.vw_consumption_rpt as
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
	,c.food_cat_name as food_cat
	,lower(m.food_id_name) as food_id_name
	,lower(i.prod_name) as prod_name
    ,i.serving
	,ifnull(o.consumed, 0) as consumed
    ,ifnull(o.disposed, 0) as disposed

from musteatnow.stock_in 				as i
inner join musteatnow.user_master 		as u on i.userid=u.userid
inner join musteatnow.stock_master 		as m on i.food_id=m.food_id
inner join musteatnow.food_cat_master 	as c on m.food_cat_id=c.food_cat_id
left join stock_out 					as o on i.stock_id=o.stock_id
),

food_grp as
(select 
	userid
	,displayname
	,food_cat
    ,food_id_name
	,prod_name
    ,count(stock_id) as purchased
	,sum(consumed) as consumed
    ,sum(disposed) as disposed
from base
group by
	userid
	,displayname
	,food_cat
    ,food_id_name
    ,prod_name
),

food_concat as
(select
	userid
	,displayname
	,food_cat 
    ,food_id_name
	,group_concat(distinct prod_name order by purchased desc separator ', ') as fave_foods
    ,sum(purchased) as purchased
	,sum(consumed) as consumed
    ,sum(disposed) as disposed
from food_grp
group by
	userid
	,displayname
	,food_cat
    ,food_id_name
)
select * from food_concat
;

# stock expiry forecast table
drop view if exists musteatnow.vw_what_will_runout;
create view musteatnow.vw_what_will_runout as
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

base as
(select 
	i.userid
	,i.stockin_dt
	,o.last_stockout_dt
    ,date_add(i.stockin_dt, INTERVAL m.default_expiry_days DAY) as expiry_dt
    ,datediff(
			date_add(i.stockin_dt, INTERVAL m.default_expiry_days DAY)
			,CURRENT_TIMESTAMP
            ) as days_left
	,i.stock_id
    ,m.food_cat_id
	,c.food_cat_name as food_cat
    ,m.food_id
	,lower(m.food_id_name) as food_id_name
	,lower(i.prod_name) as prod_name
    ,i.serving
	,ifnull(o.consumed, 0) as consumed
    ,ifnull(o.disposed, 0) as disposed
    ,ifnull((i.serving - (o.consumed + o.disposed)),serving) as serving_left
    
    ,case
		when ifnull((i.serving - (o.consumed + o.disposed)),serving) <=0 then 1
        when datediff(-- calculation for days_left
					date_add(i.stockin_dt, INTERVAL m.default_expiry_days DAY)
					,CURRENT_TIMESTAMP
					) <=0 then 1
		else 0
		end as stock_finished

from musteatnow.stock_in 				as i
inner join musteatnow.user_master 		as u on i.userid=u.userid
inner join musteatnow.stock_master 		as m on i.food_id=m.food_id
inner join musteatnow.food_cat_master 	as c on m.food_cat_id=c.food_cat_id
left join stock_out 					as o on i.stock_id=o.stock_id
),

t1 as
(select
	userid
	,stock_id
    ,food_cat_id
	,food_cat
    ,food_id
	,food_id_name
	,prod_name
	,stockin_dt
    ,ifnull(last_stockout_dt, stockin_dt) as last_stockout_dt
	,serving
	,serving_left
	,stock_finished
    ,case when stock_finished=1 then datediff(last_stockout_dt, stockin_dt) else null end as stock_consumption_days
from base
),

t3 as
(select
	t.userid
	,t.stock_id
	,t.food_cat
    ,t.food_id
	,t.food_id_name
	,t.prod_name
    ,t.stockin_dt
    ,t.last_stockout_dt
    ,t.serving
    ,t.serving_left
    ,t.stock_finished
    ,t.stock_consumption_days
    
	,max(stock_consumption_days) over(partition by userid, food_cat_id) as cat_id_consumption_days
    ,max(stock_consumption_days) over(partition by userid, food_id) as food_id_consumption_days
    
from t1 as t

),

t4 as
(select
	userid
	,food_cat
    ,stock_id
    ,food_id
	,food_id_name
	,prod_name
    ,stockin_dt
    ,last_stockout_dt
    ,serving
    ,serving_left
	,serving_left* ifnull(food_id_consumption_days,cat_id_consumption_days) as days_to_consumed
from t3
),


t5 as
(select
	userid
    ,food_id
	,food_cat
	,food_id_name
    ,group_concat(distinct prod_name order by serving_left asc separator ', ') as products
    ,sum(serving) as servings_purchased
    ,sum(serving_left) as serving_left
    ,max(days_to_consumed) as days_to_consumed
from t4
group by
	userid
    ,food_id
	,food_cat
	,food_id_name
),

t6 as
(select
	userid
    ,food_id
	,food_cat
	,food_id_name
    ,products
    ,servings_purchased
    ,serving_left
    ,days_to_consumed
	,date_add(CURRENT_TIMESTAMP, interval days_to_consumed day) as forecasted_consumption_date
from t5
)
select * from t6
;

# End of MySQL db setup.
