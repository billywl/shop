-- 进入数据库
mysql -h127.0.0.1 -uroot -proot

-- 创建数据库
create database shop charset utf8;
use shop;

-- 创建表
-- 标前缀sh_
create table sh_admin(
a_id int not null primary key auto_increment,
a_username varchar(10) not null comment '用户名',
a_password char(50) not null comment '密码,md5加密',
a_last_log_ip char(15) comment '用户上次登录的ip',
a_last_time int unsigned not null comment '用户上次登录时间'
)charset utf8 engine=innodb;

-- 插入一个用户
insert into sh_admin values(null,'admin',md5('admin'),'',1);

-- 创建商品分类表
create table sh_category(
c_id int not null primary key auto_increment,
c_name varchar(20) not null comment '商品分类名称',
c_inv int unsigned not null default 0 comment '商品分类对应的商品数量',
c_sort int default 50 comment '商品分类排序',
c_parent_id int not null default 0 comment '商品分类的父类id,0表示顶级'
)charset utf8 engine=innodb;

-- 插入数据
insert into sh_category values
(null,'手机',default,default,default),
(null,'双模手机',default,default,1),
(null,'3G手机',default,default,1),
(null,'cdma手机',default,default,1),
(null,'手机配件',default,default,default),
(null,'电池',default,default,5),
(null,'充电器',default,default,5),
(null,'耳机',default,default,5),
(null,'beats耳机',default,default,8),
(null,'电视',default,default,default),
(null,'液晶电视',default,default,10),
(null,'等离子电视',default,default,10),
(null,'平板电视',default,default,10),
(null,'手机外壳',default,default,5);

-- 随机修改商品数量
update sh_category set c_inv = floor(rand()*3);

-- 创建商品表
create table sh_goods(
g_id int not null primary key auto_increment,
g_name varchar(20) not null comment '商品名称',
g_desc text comment '商品描述',
g_sn char(10) not null  comment '商品货号',
g_price decimal(10,2) default 1.0 comment '商品价格',
g_inv int unsigned not null default 0 comment '商品库存',
g_sort int default 50 comment '商品排序',
c_id int not null comment '商品分类',
g_is_sale tinyint default 0 comment '商品是否上架,1表示上架,0表示下架',
g_img varchar(254) comment '商品图片路径',
g_thumb_img varchar(255) comment '商品缩略图路径',
g_water_img varchar(255) comment '商品水印图',
g_is_hot tinyint default 0 comment '商品是否热销,默认0 不热销',
g_is_new tinyint default 1 comment '商品是否是新品,1默认是新品',
g_is_pro tinyint default 0 comment '商品是否促销,0默认不促销'
)charset utf8 engine = innodb;

-- 插入数据
insert into sh_goods values
(null,'IPHONE6','史上最好手机','GOODS00001',5288,0,default,4,default,'','','',default,default,default),
(null,'IPHONE5','史上次好手机','GOODS00002',5288,0,default,4,default,'','','',default,default,default),
(null,'IPHONE4S','最好手机','GOODS00003',5288,0,default,4,default,'','','',default,default,default),
(null,'IPHONE4','不错手机','GOODS00004',4288,0,default,4,default,'','','',default,default,default),
(null,'Galaxy S5','Samsung手机','GOODS00005',5488,0,default,3,default,'','','',default,default,default),
(null,'飞毛腿','移动电源','GOODS00006',5288,0,default,6,default,'','','',default,default,default),
(null,'长虹','电视机','GOODS00007',5288,0,default,12,default,'','','',default,default,default),
(null,'索尼','手机','GOODS00008',5288,0,default,4,default,'','','',default,default,default),
(null,'LG液晶电视','液晶电视','GOODS00009',5288,0,default,11,default,'','','',default,default,default),
(null,'三星电视','三星','GOODS00010',5288,0,default,11,default,'','','',default,default,default),
(null,'苹果电视','苹果','GOODS00011',5288,0,default,11,default,'','','',default,default,default),
(null,'创维','口碑不错的电视','GOODS00012',5288,0,default,13,default,'','','',default,default,default),
(null,'康佳','比较悠久的电视','GOODS00013',5288,0,default,13,default,'','','',default,default,default),
(null,'诺基亚','已经过气的手机','GOODS00014',5288,0,default,2,default,'','','',default,default,default),
(null,'夏普','女士手机','GOODS00015',5288,0,default,2,default,'','','',default,default,default),
(null,'海尔','可外接的电视','GOODS00016',5288,0,default,12,default,'','','',default,default,default)
;

-- 将部分属性变为随机值
update sh_goods set g_is_sale=round(rand()),g_is_pro=round(rand()),g_is_new=round(rand()),g_is_hot=round(rand());

-- 增加字段
alter table sh_goods add column g_is_delete tinyint default 0 comment '商品是否被删除,默认为0表示正常';

-- 增加session表
create table sh_session(
s_id char(32) not null,
s_info text,
s_expire int,
unique key(s_id)
)charset utf8;