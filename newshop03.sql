
#创建商品品牌表
DROP TABLE IF EXISTS newshop03_brand;

CREATE TABLE newshop03_brand(
  `brand_id` smallint not null AUTO_INCREMENT COMMENT '品牌id',
  `brand_name` varchar(60) not null COMMENT '品牌名称',
  `url` varchar(150) not null DEFAULT '' COMMENT '品牌网址',
  `logo` varchar(150) not null default '' COMMENT '品牌logo',
  `sort_order` tinyint unsigned not null default 50 comment '排序',
  `brand_desc` varchar(512) not null default '' comment '品牌描述',
  `cat_name` varchar(128) DEFAULT '' COMMENT '品牌分类',
  `parent_cat_id` smallint default 0 comment '分类id',
  `cat_id` smallint default 0 comment '分类id',
  `is_hot` tinyint  not null default 0 comment '是否推荐',
  `create_time` int not null default 0 comment '添加时间',
  `update_time` int not null default 0 comment '更新时间',
  PRIMARY KEY (`brand_id`)
)engine=MyISAM default charset=utf8;

# 创建商品分类表
drop table if exists newshop03_category;

CREATE TABLE newshop03_category(
  `cat_id` smallint not null AUTO_INCREMENT COMMENT '分类id',
  `cat_name` varchar(60) not null comment '分类名称',
  `mobile_name` varchar(50) not null comment '手机端分类名称',
  `parent_id` smallint unsigned not null default 0 comment '父id',
  `parent_id_path` varchar(128) not null default '' comment '家庭图谱',
  `level` tinyint not null default 0 comment '等级',
  `is_show` tinyint not null default 1 comment '是否显示',
  `cat_group` tinyint unsigned not null default 0 comment '分组',
  `image` varchar(150) not null default '' comment '分类图片',
  `is_hot` tinyint not null default 0 comment '是否推荐',
  `commission_rate` tinyint not null default 0 comment '分佣比例',
  `sort_order` tinyint unsigned not null default 50 comment '排序',
  `create_time` int not null default 0 comment '添加时间',
  `update_time` int not null default 0 comment '更新时间',
  PRIMARY KEY (`cat_id`),
  KEY `parent_id` (`parent_id`)
)engine=MyISAM default charset = utf8;

====================  RBAC ===============================
# 创建权限表
drop table if exists newshop03_system_menu;

CREATE TABLE newshop03_system_menu(
  `id` smallint not null AUTO_INCREMENT COMMENT '权限资源id',
  `name` varchar(60) not null default '' comment '权限资源名称',
  `group` varchar(20) not null default '' comment '所属分组',
  `right` text comment '权限码（控制器@动作）',
  `is_del` tinyint not null default 0 comment '删除状态 1删除 0正常',
  `type` tinyint not null default 0 comment '所属模块类型 0admin 1home 2mobile 3api',
  `create_time` int not null default 0 comment '添加时间',
  `update_time` int not null default 0 comment '更新时间',
  PRIMARY KEY (`id`)
) engine = MyISAM charset = utf8;


# 创建角色表
drop table if exists newshop03_role;

CREATE TABLE newshop03_role(
  `role_id` smallint not null AUTO_INCREMENT COMMENT '角色id',
  `role_name` varchar(60) not null default '' comment '角色名称',
  `act_list` text comment '功能列表',
  `role_desc` varchar(255) not null default '' comment '角色描述',
  `create_time` int not null default 0 comment '添加时间',
  `update_time` int not null default 0 comment '更新时间',
  PRIMARY KEY (`role_id`)
) engine = MyISAM default charset = utf8;

# 创建管理员表
drop table if exists newshop03_admin;

CREATE TABLE newshop03_admin(
  `admin_id` smallint not null AUTO_INCREMENT COMMENT '管理员id',
  `admin_name` varchar(60) not null default '' comment '管理员名称',
  `email` varchar(60) not null default '' comment 'email',
  `password` char(32) not null comment '密码',
  `ec_salt` char(10) not null default '' comment '密钥',
  `last_ip` varchar(20) not null default '' comment '最后一次登陆ip',
  `last_login` int not null default 0 comment '最后一次登陆时间',
  `nav_ist` text comment '权限',
  `lang_type` varchar(50) not null default '' comment 'lang_type',
  `agency_id` smallint unsigned not null default 0 comment '代理agency_id',
  `suppliers_id`  smallint unsigned not null default 0 comment '供应商suppliers_id',
  `todolist` text comment 'todolist',
  `role_id` smallint unsigned not null default 0 comment '角色id',
  `province_id` int unsigned not null default 0 comment '加盟商省级id',
  `city_id` int unsigned not null default 0 comment '加盟商市级id',
  `district_id` int unsigned not null default 0 comment '加盟商区级id',
  `create_time` int not null default 0 comment '添加时间',
  `update_time` int not null default 0 comment '更新时间',
  PRIMARY KEY (`admin_id`)
) engine = MyISAM default charset = utf8;