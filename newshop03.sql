#创建用户表
DROP TABLE IF EXISTS newshop03_users;

CREATE TABLE newshop03_users (
  `user_id` mediumint unsigned not null auto_increment comment '用户表id',
  `email` varchar(64) not null default '' comment 'email',
  `password` char(32) not null comment '密码',
  `paypwd` char(32) not null comment '支付密码',
  `sex` tinyint unsigned not null default 0 comment '性别 0保密 1男 2女',
  `birthday` int unsigned not null default 0 comment '生日',
  `user_money` decimal(10,2) not null default '0.00' comment '用户金额',
  `frozen_money` decimal(10,2) not null default '0.00' comment '冻结金额',
  `distribute_money` decimal(10,2) not null default '0.00' comment '累积分佣金额',
  `underline_number` mediumint not null default 0 comment '用户下线总数',
  `pay_points` int unsigned not null default 0 comment '消费积分',
  `address_id` mediumint unsigned not null default 0 comment '默认收货地址',
  `reg_time` int unsigned not null default 0 comment '注册时间',
  `last_login` int unsigned not null default 0 comment '最后登陆时间',
  `last_ip` varchar(15) not null default '' comment '最后登陆ip',
  `qq` varchar(20) not null default '' comment 'QQ',
  `mobile` varchar(15) not null default '' comment '手机号码',
  `mobile_validated` tinyint unsigned not null default 0 comment '是否验证手机',
  `oauth` varchar(10) not null default '' comment '第三方来源 wx weibo alipay',
  `openid` varchar(100) not null default '' comment '第三方唯一标示',
  `unionid` varchar(100) not null default '' comment '联合登陆id',
  `head_pic` varchar(150) not null default '' comment '头像',
  `province` int unsigned not null default 0 comment '省份',
  `city` int unsigned not null default 0 comment '市区',
  `district` int unsigned not null default 0 comment '县',
  `email_validated` tinyint unsigned not null default 0 comment '是否验证email',
  `nickname` varchar(50) not null default '' comment '第三方返回昵称',
  `level` tinyint unsigned not null default 1 comment '会员等级',
  `discount` decimal(10,2) not null default '1.00' comment '会员折扣, 默认1不享受',
  `total_amount` decimal(10,2) not null default '0.00' comment '消费累计额度',
  `is_lock` tinyint unsigned not null default 0 comment '是否被锁定冻结',
  `is_distribute` tinyint unsigned not null default 0 comment '是否为分销商 0否 1是',
  `first_leader` int unsigned not null default 0 comment '第一个上级',
  `second_leader` int unsigned not null default 0 comment '第二个上级',
  `third_leader` int unsigned not null default 0 comment '第三个上级',
  `token` varchar(64) not null default '' comment '用于app 授权类似于session_id',
  `message_mask` tinyint unsigned not null default '63' comment '消息掩码',
  `push_id` varchar(32) not null default '' comment '推送id',
  `distribute_level` tinyint unsigned not null default '0' comment '分销商等级',
  `is_vip` tinyint unsigned not null default '0' comment '是否为vip 0不是 1是',
  `xcx_qrcode` varchar(150) not null default '' comment '小程序专属二维码',
  `poster` varchar(150) not null default '' comment '专属推广海报',
  PRIMARY KEY (`user_id`),
  KEY `email`(`email`),
  KEY `underline_number`(`underline_number`),
  KEY `mobile`(`mobile`),
  KEY `openid`(`openid`),
  KEY `unionid`(`unionid`)
) charset=utf8;

insert  into `newshop03_users`(`user_id`,`email`,`password`,`paypwd`,`sex`,`birthday`,`user_money`,`frozen_money`,`distribute_money`,`underline_number`,`pay_points`,`address_id`,`reg_time`,`last_login`,`last_ip`,`qq`,`mobile`,`mobile_validated`,`oauth`,`openid`,`unionid`,`head_pic`,`province`,`city`,`district`,`email_validated`,`nickname`,`level`,`discount`,`total_amount`,`is_lock`,`is_distribute`,`first_leader`,`second_leader`,`third_leader`,`token`,`message_mask`,`push_id`,`distribut_level`,`is_vip`,`xcx_qrcode`,`poster`) values (1,'13800138000@163.com',md5('123456'),md5('123456'),0,-28800,100000.00,0.00,0.00,0,100000,0,1523235674,1523235674,'','','',0,'','','','/public/upload/user/1/head_pic/1673d08c39ff9d1103611a7585a8ae0f.jpg',0,0,0,1,'13800138000@163.com',4,0.94,4939.90,0,0,0,0,0,'81953a80817fdf7c25e682ca3311abc9',63,'0',0,0,'','');

# 创建购物车表
DROP TABLE IF EXISTS `newshow03_cart`;

CREATE TABLE `newshop03_cart` (
  `cart_id` int unsigned NOT NULL auto_increment comment '购物车id',
  `user_id` mediumint unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `session_id` char(128) NOT NULL DEFAULT '' COMMENT 'SESSION',
  `goods_id` mediumint unsigned NOT NULL DEFAULT '0' comment '商品id',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品货号',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `market_parice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `goods_parice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '本店价',
  `member_goods_parice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员折扣价',
  `goods_num` smallint unsigned NOT NULL DEFAULT 0 COMMENT '购买数量',
  `item_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格id',
  `spec_key` varchar(64) NOT NULL DEFAULT '' COMMENT '商品规格key, 对应spec_goods_price表的key',
  `spec_key_name` varchar(64) NOT NULL DEFAULT '' COMMENT '商品规格组合名称',
  `bar_code` varchar(64) NOT NULL DEFAULT '' COMMENT '商品条码',
  `selected` tinyint unsigned not null default 1 comment '购物车选中状态',
  `add_time` int unsigned not null default 0 comment '加入购物车的时间',
  `prom_type` tinyint unsigned not null default 0 comment '0普通订单, 1限时抢购, 2团购, 3促销优惠, 7搭配购',
  `prom_id` int unsigned not null DEFAULT '0' comment '活动id',
  `sku` varchar(128) not null default '' comment 'sku',
  `combination_group_id` int unsigned NOT NULL DEFAULT 0 COMMENT '搭配购的组id/cart_id',
  PRIMARY KEY (`cart_id`),
  KEY `session_id`(`session_id`),
  KEY `user_id`(`user_id`),
  KEY `goods_id`(`goods_id`),
  KEY `spec_key`(`spec_key`)
) charset=utf8;


# 创建用户等级表
DROP TABLE IF EXISTS `newshop03_user_level`;
CREATE TABLE `newshop03_user_level` (
    `level_id` smallint unsigned NOT NULL AUTO_INCREMENT COMMENT '用户等级表id',
    `level_name` varchar(32) default '' COMMENT '等级名称',
    `amount` decimal(10,2) DEFAULT '0.00' COMMENT '等级必要的金额',
    `discount` smallint unsigned not null default 0 comment '折扣',
    `describe` varchar(200) not null default '' comment '描述',
    PRIMARY KEY(`level_id`)
) engine=MyISAM charset=utf8;

insert  into `newshop3_user_level`(`level_id`,`level_name`,`amount`,`discount`,`describe`) values (1,'倔强青铜',0.00,100,'若如初相见，若如初相恋'),(2,'秩序白银',1000.00,99,''),(3,'荣耀黄金',3000.00,94,''),(4,'尊贵铂金',10000.00,95,''),(5,'永恒钻石',50000.00,93,''),(6,'至尊星耀',100000.00,91,''),(7,'最强王者',3000000.00,90,'');

#创建商品表
DROP TABLE IF EXISTS newshop03_goods;
CREATE TABLE newshop03_goods(
       `goods_id` mediumint unsigned not null auto_increment comment '商品id',
       `gods_name` varchar(120) not null comment '商品名称',
       `goods_sn` varchar(60) not null default '' comment '商品编号',
       `cat_id` smallint unsigned default 0 comment '分类id',
       `extend_cat_id` smallint default 0 comment '扩展分类id',
       `click_count` mediumint unsigned not null default 0 comment '点击数',
       `brand_id` smallint unsigned default 0 comment '品牌id',
       `store_count` smallint unsigned not null default 0 comment '库存数量',
       `comment_count` smallint unsigned not null default 0 comment '商品评论数',
       `weight` mediumint unsigned not null default 0 comment '商品重量, 单位为克',
       `volume` float(10,4) unsigned not null default '0.0000' comment '商品体积, 单位为立方米',
       `market_price` decimal(10,2) unsigned not null default '0.00' comment '市场价',
       `shop_price` decimal(10,2) unsigned not null default '0.00' comment '本店价',
       `cost_price` decimal(10,2) unsigned not null default '0.00' comment '成本价',
       `price_ladder` text comment '价格阶梯',
       `keywords` varchar(255) not null default '' COMMENT '商品关键词',
       `goods_brief`  varchar(255) not null default '' COMMENT '商品简单描述',
       `goods_desc` text COMMENT '商品详细描述',
       `mobile_desc` text COMMENT '手机端商品详细描述',
       `original_img` varchar(150) not null default '' comment '商品上传原始图',
       `is_virtual` tinyint unsigned not null default 0 COMMENT '是否是虚拟商品 1是, 0否',
       `virtual_indate` int default 0 COMMENT '虚拟商品有效期',
       `virtual_limit` smallint unsigned not null default 0 comment '虚拟商品购买上限',
       `virtual_refund` tinyint unsigned not null default 1 COMMENT '是否允许过期退款 1是, 0否',
       `virtual_sales_sum` mediumint unsigned not null default 0 comment '虚拟销售量',
       `virtual_collect_sum` mediumint unsigned not null default 0 comment '虚拟收藏量',
       `collect_sum` mediumint unsigned not null default 0 comment '收藏量',
       `is_on_sale` tinyint unsigned not null default 1 COMMENT '是否上架',
       `is_shipping` tinyint unsigned not null default 0 COMMENT '是否包邮 1是, 0否',
       `sort_order` smallint unsigned not null default 50 comment '商品排序',
       `is_recommend` tinyint unsigned not null default 0 COMMENT '是否推荐',
       `is_new` tinyint unsigned not null default 1 COMMENT '是否新品',
       `is_hot` tinyint unsigned not null default 0 COMMENT '是否热卖',
       `goods_type` smallint unsigned not null default 0 comment '商品所属类型id',
       `give_integral` mediumint unsigned not null default 0 comment '购买商品赠送积分',
       `exchange_integral` int not null default 0 comment '积分兑换, 0不参与积分兑换',
       `suppliers_id` smallint unsigned not null default 0 comment '供货商id',
       `sales_sum` int unsigned not null default 0 comment '商品销量',
       `prom_type` tinyint unsigned not null default 0 comment '活动类型, 0默认, 1抢购, 2团购, 3优惠促销, 4预售, 5虚拟, 6拼团, 7搭配购',
       `prom_id` mediumint unsigned not null default 0 comment '优惠活动id',
       `commission` decimal(5,2) default 0.00 comment '佣金用于分销分成',
       `spu` varchar(255) not null default '' comment 'spu标准化单元',
       `sku` varchar(255) not null default '' comment 'spu库存量单位',
       `template_id` smallint unsigned not null default 0 comment '运费模板id',
       `create_time` int unsigned not null default 0 COMMENT '更新时间',
       `update_time` int unsigned not null default 0 COMMENT '更新时间',
       `video` varchar(150) not null default '' comment '视频',
       PRIMARY KEY(`goods_id`),
       KEY `goods_sn`(`goods_sn`),
       KEY `cat_id`(`cat_id`),
       KEY `last_update`(`update_time`),
       KEY `brand_id`(`brand_id`),
       KEY `goods_number`(`store_count`),
       KEY `goods_weight`(`weight`),
       KEY `sort_order`(`sort_order`)
)engine=MyISAM default charset=utf8;


#创建商品与属性的关联表
DROP TABLE IF EXISTS newshop03_goods_attr;
CREATE TABLE newshop03_goods_attr(
    `goods_attr_id` mediumint unsigned not null auto_increment comment '商品属性关联表id',
    `goods_id` mediumint unsigned not null default 0 comment '商品id',
    `attr_id` mediumint unsigned not null default 0 comment '属性id',
    `attr_value` varchar(150) not null default '' comment '属性值',
    `attr_price` varchar(50) not null default '' comment '属性价格',
    PRIMARY KEY(`goods_attr_id`),
    KEY `goods_id`(`goods_id`),
    KEY `attr_id`(`attr_id`)
)engine=MyISAM default charset=utf8;


#创建商品相册表
DROP TABLE IF EXISTS newshop03_goods_image;
CREATE TABLE newshop03_goods_image(
     `img_id` mediumint unsigned not null auto_increment comment '商品相册id',
     `goods_id` mediumint unsigned not null default 0 comment '商品id',
     `image_url` varchar(150) not null default '' comment '图片地址',
     PRIMARY KEY(`img_id`)
)engine=MyISAM default charset=utf8;


#创建商品规格图片表
DROP TABLE IF EXISTS newshop03_spec_image;
CREATE TABLE newshop03_spec_image(
      `spec_image_id` mediumint unsigned not null default 0 comment '对应的规格项id',
      `goods_id` mediumint unsigned not null default 0 comment '商品id',
      `src` varchar(150) not null default '' comment '图片地址'
)engine=MyISAM default charset=utf8;


#创建商品规格关联表
DROP TABLE IF EXISTS newshop03_spec_goods_price;
CREATE TABLE newshop03_spec_goods_price(
    `item_id` mediumint unsigned not null auto_increment comment '商品规格关联id',
    `goods_id` mediumint unsigned not null default 0 comment '商品id',
    `key` varchar(32) not null default '' comment '规格键名',
    `key_name` varchar(64) not null default '' comment '规格键名中文',
    `price` decimal(10,2) unsigned not null default 0.00 comment '价格',
    `cost_price` decimal(10,2) unsigned not null default 0.00 comment '成本价',
    `commission` decimal(10,2) unsigned not null default 0.00 comment '佣金',
    `store_count` mediumint unsigned not null default 0 comment '库存数量',
    `sku` varchar(150) not null default '' comment 'sku',
    `spec_img` varchar(150) comment '规格图片',
    `prom_id` smallint default 0 comment '活动id',
    `prom_type` tinyint default 0 comment '活动类型',
    PRIMARY KEY(`item_id`),
    KEY `key`(`key`)
)engine=MyISAM default charset=utf8;


#创建商模型表
DROP TABLE IF EXISTS newshop03_goods_type;
CREATE TABLE newshop03_goods_type(
  `type_id` smallint not null auto_increment comment '商品模型id',
  `type_name` varchar(60) not null comment '商品模型名称',
  PRIMARY KEY(`type_id`)
)engine=MyISAM default charset=utf8;

#创建商品规格表
DROP TABLE IF EXISTS newshop03_spec;

CREATE TABLE newshop03_spec (
   `spec_id` smallint unsigned not null auto_increment comment '规格id',
   `spec_name` varchar(60) not null comment '商品规格名称',
   `type_id` smallint default 0 comment '规格所属类型',
   `sort_order` tinyint unsigned not null DEFAULT 50 comment '排序',
   `is_upload_image` tinyint unsigned not null DEFAULT 0 comment '是否可上传规格图 0不可以 1可以',
   `search_index` tinyint unsigned not null DEFAULT 1 comment '是否需要检索: 1是 0否',
   PRIMARY KEY(`spec_id`)
)engine=MyISAM default charset=utf8;


#创建商品规格项表
DROP TABLE IF EXISTS newshop03_spec_item;

CREATE TABLE newshop03_spec_item (
    `id` int not null auto_increment comment '规格项id',
    `item` varchar(50) not null comment '商品规格项',
    `spec_id` smallint not null comment '规格项所属规格id',
    `sort_order` tinyint unsigned not null DEFAULT 0 comment '排序',
    PRIMARY KEY(`id`)
)engine=MyISAM default charset=utf8;



#创建商品规格表
DROP TABLE IF EXISTS newshop03_attribute;

CREATE TABLE newshop03_attribute (
  `attr_id` smallint unsigned not null auto_increment comment '属性id',
  `attr_name` varchar(60) not null comment '属性名称',
  `type_id` smallint unsigned default 0 comment '规格所属类型',
  `attr_index` tinyint unsigned not null DEFAULT 0 comment '是否显示 0不显示 1显示',
  `attr_values` text comment '可选值列表',
  `sort_order` tinyint unsigned not null DEFAULT 50 comment '排序',
  PRIMARY KEY(`attr_id`)
)engine=MyISAM default charset=utf8;

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

######################## RBAC ###########################
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

