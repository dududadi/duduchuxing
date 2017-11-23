DROP DATABASE IF EXISTS dudu;
CREATE DATABASE dudu DEFAULT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';
USE dudu;

SET FOREIGN_KEY_CHECKS=0;


-- 创建省份表
DROP TABLE IF EXISTS d_province;
CREATE TABLE d_province (
  prov_num INT(6) NOT NULL PRIMARY KEY, -- 省级代码
  prov_name VARCHAR(10) NOT NULL -- 省份名称
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建市级表
DROP TABLE IF EXISTS d_city;
CREATE TABLE d_city (
  city_num INT(6) NOT NULL  PRIMARY KEY, -- 市级代码
  city_name VARCHAR(15) NOT NULL, -- 市级名称
  prov_num INT(6) NOT NULL, -- 省级代码
  FOREIGN KEY(prov_num) REFERENCES d_province(prov_num)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建地区表
DROP TABLE IF EXISTS d_area;
CREATE TABLE d_area (
  area_num INT(6) NOT NULL  PRIMARY KEY, -- 地区代码
  area_name VARCHAR(20) NOT NULL, -- 地区名称
  city_num INT(6) NOT NULL, -- 市级代码
  FOREIGN KEY(city_num) REFERENCES d_city(city_num)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建运营类型表
DROP TABLE IF EXISTS d_business_type;
CREATE TABLE d_business_type(
bt_id INT NOT NULL auto_increment PRIMARY KEY, -- 运营类型id
bt_name VARCHAR(10) NOT NULL -- 运营类型名称
)ENGINE=INNODB DEFAULT charset=utf8;


-- 创建用户(乘客)表
DROP TABLE IF EXISTS d_user;
CREATE TABLE d_user(
user_id  INT NOT NULL auto_increment PRIMARY KEY, -- 用户id
user_reg_time datetime NOT NULL, -- 注册时间
user_psw CHAR(32) NOT NULL, -- 密码
user_name VARCHAR(32) NOT NULL, -- 真实姓名
user_id_num VARCHAR(18),  -- 身份证号
user_tel CHAR(11) NOT NULL, -- 手机号
user_score FLOAT NOT NULL, -- 评分
user_money DECIMAL(7,2) NOT NULL, -- 余额
user_status enum('锁定','使用') NOT NULL, -- 状态
user_head_img VARCHAR(100) NOT NULL, -- 头像
user_address text, -- 详细地址
prov_num INT(6) NOT NULL, -- 省份
city_num INT(6) NOT NULL, -- 地级市
area_num INT(6) NOT NULL, -- 区/县/县级市
open_id varchar(28),-- 微信注册open_id
FOREIGN KEY(prov_num) REFERENCES d_province(prov_num),
FOREIGN KEY(city_num) REFERENCES d_city(city_num),
FOREIGN KEY(area_num) REFERENCES d_area(area_num),
key(user_tel)
)ENGINE=INNODB DEFAULT charset=utf8;


-- 创建司机表
DROP TABLE IF EXISTS d_driver;
CREATE TABLE d_driver(
driv_id  INT NOT NULL auto_increment PRIMARY KEY, -- 司机id
driv_psw CHAR(32) NOT NULL, -- 密码
driv_reg_time datetime NOT NULL, -- 注册时间
prov_num INT(6) NOT NULL , -- 省份
city_num INT(6) NOT NULL , -- 地级市
area_num INT(6) NOT NULL , -- 区/县/县级市
FOREIGN KEY(prov_num) REFERENCES d_province(prov_num),
FOREIGN KEY(city_num) REFERENCES d_city(city_num),
FOREIGN KEY(area_num) REFERENCES d_area(area_num),
driv_address text, -- 详细地址
driv_name VARCHAR(32) NOT NULL, -- 真实姓名
driv_id_num VARCHAR(18) NOT NULL, -- 身份证号
driv_license_time datetime NOT NULL, -- 领证日期
driv_car_num VARCHAR(7), -- 车牌号
driv_car_type VARCHAR(20), -- 车型（品牌@颜色）
driv_owner VARCHAR(32) NOT NULL, -- 车辆所有人
driv_car_reg_time datetime NOT NULL, -- 车辆注册日期
driv_money DECIMAL(7,2)  NOT NULL, -- 余额
driv_tel CHAR(11) NOT NULL, -- 手机号
driv_status enum('锁定','使用','未审核') NOT NULL, -- 状态
driv_score FLOAT NOT NULL, -- 评分
driv_head_img VARCHAR(100) NOT NULL, -- 头像
bt_id INT NOT NULL, -- 运营类型id
FOREIGN KEY(bt_id) REFERENCES d_business_type(bt_id),
driv_bank_num VARCHAR(19), -- 银行卡号
open_id varchar(28),-- 微信注册open_id
key(driv_tel)
)ENGINE=INNODB DEFAULT charset=utf8;

-- 创建订单状态表
DROP TABLE IF EXISTS d_order_list_status;
CREATE TABLE d_order_list_status(
ols_id INT(2) NOT NULL PRIMARY KEY auto_increment, -- 订单状态id
ols_name VARCHAR(20) NOT NULL -- 状态名称
)ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 创建订单表
DROP TABLE IF EXISTS d_order_list;
CREATE TABLE d_order_list(
ol_id INT NOT NULL PRIMARY KEY auto_increment, -- 订单id
user_id INT NOT NULL, -- 用户id
driv_id INT NOT NULL, -- 司机id
ol_start_time datetime NOT NULL, -- 下单时间
ol_end_time datetime NOT NULL, -- 结束时间
rpt_id INT(2) NOT NULL, -- 付款方式id
ols_id INT(2) NOT NULL, -- 订单状态id
ol_km_num FLOAT(5,2), -- 里程数
ol_km_price DECIMAL(5,2), -- 里程价
ol_overtime_price DECIMAL(5,2), -- 超时价
ol_tip DECIMAL(5,2), -- 小费

oh_start_latitude FLOAT,  -- 起始经度
oh_start_longitude FLOAT,-- 起始纬度
oh_end_latitude FLOAT,-- 终点经度
oh_end_longitude FLOAT,-- 终点纬度
oh_start_name VARCHAR(100), -- 起始点名称
oh_end_name VARCHAR(100),-- 终点名称

FOREIGN KEY(user_id) REFERENCES d_user(user_id),
FOREIGN KEY(driv_id) REFERENCES d_driver(driv_id),
FOREIGN KEY(rpt_id) REFERENCES d_recharge_pay_type(rpt_id),
FOREIGN KEY(ols_id) REFERENCES d_order_list_status(ols_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 创建挂起订单表
DROP TABLE IF EXISTS d_order_handup;
CREATE TABLE d_order_handup(
open_id VARCHAR(24) PRIMARY KEY,-- 用户openid
driv_id INT,-- 司机id
oh_start_latitude FLOAT,  -- 起始经度
oh_start_longitude FLOAT,-- 起始纬度
oh_end_latitude FLOAT,-- 终点经度
oh_end_longitude FLOAT,-- 终点纬度
oh_start_name VARCHAR(100), -- 起始点名称
oh_end_name VARCHAR(100),-- 终点名称
oh_status enum('已接单','未接单'),-- 接单状态
oh_create_time -- 挂起时间
)ENGINE=INNODB DEFAULT CHARSET=utf8;




-- 创建路程表
DROP TABLE IF EXISTS d_distance;
CREATE TABLE d_distance(
dis_id INT NOT NULL PRIMARY KEY auto_increment, -- 订单id
dis_latitude float(6,3), -- 纬度
dis_longitude float(6,3) -- 经度
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建充值付款类型表
DROP TABLE IF EXISTS d_recharge_pay_type;
CREATE TABLE d_recharge_pay_type(
rpt_id INT(2) NOT NULL PRIMARY KEY auto_increment, -- 充值付款类型id
rpt_name VARCHAR(20) NOT NULL, -- 名称
rpt_recharge enum('0','1'), -- 是否支持充值
rpt_pay enum('0','1') -- 是否支持支付
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建用户钱包记录表
DROP TABLE IF EXISTS d_user_money_record;
CREATE TABLE d_user_money_record(
umr_id INT NOT NULL PRIMARY KEY auto_increment, -- 用户钱包记录id
rpt_id INT(2) NOT NULL, -- 付款类型id
user_id INT NOT NULL , -- 用户id
umr_time datetime NOT NULL, -- 时间
umr_result enum('成功','失败') NOT NULL, -- 结果
umr_money DECIMAL(7,2), -- 金额
behavior enum('充值','支付'), -- 行为
FOREIGN KEY(rpt_id) REFERENCES d_recharge_pay_type(rpt_id),
FOREIGN KEY(user_id) REFERENCES d_user(user_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建司机钱包记录表
DROP TABLE IF EXISTS d_driv_money_record;
CREATE TABLE d_driv_money_record(
dmr_id INT NOT NULL PRIMARY KEY auto_increment, -- 司机钱包记录id
rpt_id INT(2) NOT NULL, -- 付款类型id
driv_id INT NOT NULL , -- 司机id
dmr_time datetime NOT NULL, -- 时间
dmr_result enum('成功','失败') NOT NULL, -- 结果
dmr_money DECIMAL(7,2), -- 金额
behavior enum('收入','支付'), -- 行为
FOREIGN KEY(rpt_id) REFERENCES d_recharge_pay_type(rpt_id),
FOREIGN KEY(driv_id) REFERENCES d_driver(driv_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建用户对司机评价表
DROP TABLE IF EXISTS d_comment_utd;
CREATE TABLE d_comment_utd(
cutd_id INT NOT NULL PRIMARY KEY auto_increment, -- 用户对司机评价id
cutd_content varchar(50), -- 内容
cutd_score float, -- 评分
user_id INT NOT NULL, -- 用户id
driv_id INT NOT NULL, -- 司机id
ol_id INT NOT NULL, -- 订单id
cutd_time datetime, -- 评价时间
FOREIGN KEY(user_id) REFERENCES d_user(user_id),
FOREIGN KEY(driv_id) REFERENCES d_driver(driv_id),
FOREIGN KEY(ol_id) REFERENCES d_order_list(ol_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建司机对用户评价表
DROP TABLE IF EXISTS d_comment_dtu;
CREATE TABLE d_comment_dtu(
cdtu_id INT NOT NULL PRIMARY KEY auto_increment, -- 司机对用户评价id
cdtu_score float, -- 评分
user_id INT NOT NULL, -- 用户id
driv_id INT NOT NULL, -- 司机id
ol_id INT NOT NULL, -- 订单id
cdtu_time datetime, -- 评价时间
FOREIGN KEY(user_id) REFERENCES d_user(user_id),
FOREIGN KEY(driv_id) REFERENCES d_driver(driv_id),
FOREIGN KEY(ol_id) REFERENCES d_order_list(ol_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建用户对客服评价表
DROP TABLE IF EXISTS d_comment_uts;
CREATE TABLE d_comment_uts(
cuts_id INT NOT NULL PRIMARY KEY auto_increment, -- 用户对客服评价id
cuts_content varchar(50), -- 内容
cuts_score float, -- 评分
user_id INT NOT NULL, -- 用户id
emp_id INT NOT NULL, -- 客服id
cuts_time datetime, -- 评价时间
FOREIGN KEY(user_id) REFERENCES d_user(user_id),
FOREIGN KEY(emp_id) REFERENCES d_employee(emp_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建司机对客服评价表
DROP TABLE IF EXISTS d_comment_dts;
CREATE TABLE d_comment_dts(
cdts_id INT NOT NULL PRIMARY KEY auto_increment, -- 司机对客服评价id
cdts_content varchar(50), -- 内容
cdts_score float, -- 评分
driv_id INT NOT NULL, -- 司机id
emp_id INT NOT NULL, -- 客服id
cdts_time datetime, -- 评价时间
FOREIGN KEY(driv_id) REFERENCES d_driver(driv_id),
FOREIGN KEY(emp_id) REFERENCES d_employee(emp_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建用户司机聊天记录表
DROP TABLE IF EXISTS d_chat_record_ud;
CREATE TABLE d_chat_record_ud(
crud_id INT NOT NULL PRIMARY KEY auto_increment, -- 用户司机聊天记录id
crud_content varchar(50), -- 内容
driv_id INT NOT NULL, -- 司机id
user_id INT NOT NULL, -- 用户id
crud_time datetime, -- 时间
crud_direction enum('utd','dtu'), -- 方向
FOREIGN KEY(driv_id) REFERENCES d_driver(driv_id),
FOREIGN KEY(user_id) REFERENCES d_user(user_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建用户客服聊天记录表
DROP TABLE IF EXISTS d_chat_record_us;
CREATE TABLE d_chat_record_us(
crus_id INT NOT NULL PRIMARY KEY auto_increment, -- 用户客服聊天记录id
crus_content varchar(50), -- 内容
emp_id INT NOT NULL, -- 客服id
user_id INT NOT NULL, -- 用户id
crus_time datetime, -- 时间
crus_direction enum('etu','ute'), -- 方向
FOREIGN KEY(emp_id) REFERENCES d_employee(emp_id),
FOREIGN KEY(user_id) REFERENCES d_user(user_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建客服司机聊天记录表
DROP TABLE IF EXISTS d_chat_record_ds;
CREATE TABLE d_chat_record_ds(
crds_id INT NOT NULL PRIMARY KEY auto_increment, -- 客服司机聊天记录id
crds_content varchar(50), -- 内容
emp_id INT NOT NULL, -- 司机id
driv_id INT NOT NULL, -- 客服id
crds_time datetime, -- 时间
crds_direction enum('dts','std'), -- 方向
FOREIGN KEY(emp_id) REFERENCES d_employee(emp_id),
FOREIGN KEY(driv_id) REFERENCES d_driver(driv_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建父菜单表
DROP TABLE IF EXISTS d_fmenu;
CREATE TABLE d_fmenu(
fm_id INT NOT NULL PRIMARY KEY auto_increment, -- 父菜单id
fm_name VARCHAR(10), -- 名称
fm_ico CHAR(8) -- 图标
)ENGINE=INNODB DEFAULT CHARSET=utf8;


-- 创建后台员工表
DROP TABLE IF EXISTS d_employee;
CREATE TABLE d_employee(
emp_id  INT NOT NULL auto_increment PRIMARY KEY, -- 后台员工id
emp_reg_time datetime NOT NULL, -- 注册时间
emp_psw CHAR(32) NOT NULL, -- 密码
emp_name VARCHAR(32) NOT NULL, -- 真实姓名
emp_nickname VARCHAR(32) NOT NULL, -- 昵称
role_id INT NOT NULL, -- 角色id
prov_num INT(6) NOT NULL, -- 省级代码
city_num INT(6) NOT NULL, -- 市级代码
area_num INT(6) NOT NULL, -- 地区代码
emp_status enum('锁定','使用') NOT NULL, -- 状态
emp_head_img varchar(255) not null, -- 头像
FOREIGN KEY(prov_num) REFERENCES d_province(prov_num),
FOREIGN KEY(city_num) REFERENCES d_city(city_num),
FOREIGN KEY(area_num) REFERENCES d_area(area_num),
FOREIGN KEY(role_id) REFERENCES d_role(role_id)
)ENGINE=INNODB DEFAULT charset=utf8;


-- 创建子菜单表
DROP TABLE IF EXISTS d_smenu;
CREATE TABLE d_smenu(
sm_id INT NOT NULL PRIMARY KEY auto_increment, -- 子菜单id
sm_name VARCHAR(10), -- 名称
sm_url VARCHAR(255), -- url
fm_id INT NOT NULL, -- 父菜单id
FOREIGN KEY(fm_id) REFERENCES d_fmenu(fm_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 创建角色表
DROP TABLE IF EXISTS d_role;
CREATE TABLE d_role(
role_id INT NOT NULL PRIMARY KEY auto_increment, -- 角色id
role_name VARCHAR(10) NOT NULL, -- 名称
role_description varchar(20) NOT NULL -- 描述
)ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 创建角色子菜单表
DROP TABLE IF EXISTS d_role_menu;
CREATE TABLE d_role_menu(
rm_id INT NOT NULL PRIMARY KEY auto_increment, -- 角色子菜单id
role_id INT NOT NULL, -- 角色id
sm_id INT NOT NULL, -- 子菜单id
FOREIGN KEY(role_id) REFERENCES d_role(role_id),
FOREIGN KEY(sm_id) REFERENCES d_smenu(sm_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 创建新闻表
CREATE TABLE d_news(
news_id INT NOT NULL auto_increment PRIMARY KEY, -- 新闻id
news_title VARCHAR(20) NOT NULL,
news_release_time datetime NOT NULL,     -- 创建时间
news_status enum('发布','未发布') NOT NULL,
news_img VARCHAR(100) NOT NULL,
news_content VARCHAR(999) NOT NULL
)ENGINE=INNODB DEFAULT charset=utf8;
