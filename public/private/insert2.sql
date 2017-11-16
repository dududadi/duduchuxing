-- 运营类型
INSERT INTO d_business_type VALUES
(null,'出租车'),
(null,'快车');

-- 订单状态
INSERT INTO d_order_list_status VALUES
(null,'未过期'),
(null,'已过期'),
(null,'已支付');

-- 充值付款类型
INSERT INTO d_recharge_pay_type VALUES
(null,'微信','1','1'),
(null,'支付宝','1','1'),
(null,'余额','0','1'),
(null,'现金','0','1');

-- 角色表 超级管理员初始化
INSERT INTO d_role VALUES
(null,'超级管理员'),
(null,'客服'),
(null,'业务员'),
(null,'经理');

-- 父菜单
INSERT INTO d_fmenu VALUES
(1,'实时监控'), -- monitor
(2,'员工管理'), -- employee
(3,'司机管理'), -- driver
(4,'乘客管理'), -- user
(5,'订单管理'), -- order_list
(6,'角色管理'), -- role
(7,'资讯管理'), -- new
(8,'查询报表'); -- chart

-- 子菜单
INSERT INTO d_smenu VALUES
(null,'admin/Monitor/driver','司机监控',1),
(null,'admin/Monitor/user','乘客监控',1),
(null,'admin/Monitor/history','历史路径',1),
(null,'admin/Employee/list','员工列表',2),
(null,'admin/Employee/add','添加员工',2),
(null,'admin/Driver/list','司机列表',3),
(null,'admin/Driver/verify','司机审核',3),
(null,'admin/User/list','乘客列表',4),
(null,'admin/OrderList/undeal','未成交订单',5),
(null,'admin/OrderList/deal','成交订单',5),
(null,'admin/OrderList/overdue','过期订单',5),
(null,'admin/Role/list','角色列表',6),
(null,'admin/Role/add','添加角色',6),
(null,'admin/New/spread','推广信息',7),
(null,'admin/New/publish','新闻发布',7),
(null,'admin/New/edit','新闻编辑',7),
(null,'admin/Chart/orderList','订单统计',8),
(null,'admin/Chart/user','用户统计',8),
(null,'admin/Chart/market','营销统计',8);

-- 角色子菜单表 超管初始化
INSERT INTO d_role_menu VALUES
(null,1,1),
(null,1,2),
(null,1,3),
(null,1,4),
(null,1,5),
(null,1,6),
(null,1,7),
(null,1,8),
(null,1,9),
(null,1,10),
(null,1,11),
(null,1,12),
(null,1,13),
(null,1,14),
(null,1,15),
(null,1,16),
(null,1,17),
(null,1,18),
(null,1,19);

-- 员工表
INSERT INTO d_employee VALUES
(null,NOW(),'e10adc3949ba59abbe56e057f20f883e','duduboy','嘟嘟王子',1,110000,110100,110101,'使用','defaultHead.jpg');

