
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