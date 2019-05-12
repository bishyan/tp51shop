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