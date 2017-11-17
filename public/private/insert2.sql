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
(null,'超级管理员','拥有至高无上的权利'),
(null,'客服','用户管理、订单管理、司机管理'),
(null,'业务员','资讯管理'),
(null,'经理','地区监控、员工管理');

-- 父菜单
INSERT INTO d_fmenu VALUES
(1,'实时监控','&#xe725;'), -- monitor
(2,'员工管理','&#xe62d;'), -- employee
(3,'司机管理','&#xe70d;'), -- driver
(4,'乘客管理','&#xe62c;'), -- user
(5,'订单管理','&#xe687;'), -- order_list
(6,'角色管理','&#xe611;'), -- role
(7,'资讯管理','&#xe616;'), -- new
(8,'查询报表','&#xe61a;'); -- chart

-- 子菜单
INSERT INTO d_smenu VALUES
(null,'司机监控','admin/Monitor/driver',   1),
(null,'乘客监控','admin/Monitor/user',     1),
(null,'历史路径','admin/Monitor/history',  1),
(null,'员工列表','admin/Employee/lists',    2),
(null,'添加员工','admin/Employee/add',     2),
(null,'司机列表','admin/Driver/lists',      3),
(null,'司机审核','admin/Driver/verify',    3),
(null,'乘客列表','admin/User/lists',        4),
(null,'未成交订单', 'admin/OrderList/undeal' ,5),
(null,'成交订单','admin/OrderList/deal',   5),
(null,'过期订单','admin/OrderList/overdue',5),
(null,'角色列表','admin/Role/lists',        6),
(null,'添加角色','admin/Role/add',         6),
(null,'推广信息','admin/News/spread',       7),
(null,'新闻发布','admin/News/publish',      7),
(null,'新闻编辑','admin/News/edit',         7),
(null,'订单统计','admin/Chart/orderList',  8),
(null,'用户统计','admin/Chart/user',       8),
(null,'营销统计','admin/Chart/market',     8);

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

-- 用户表
INSERT INTO d_user VALUES
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '何贻杰', '512501197203035172', '15959027027', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '潘晓文', '51052119850508797x', '15606923823', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '刘家福', '512501197506045175', '15605907283', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '叶诚炜', '512501196512305186', '13559182419', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '何岳', '512501197203035172', '18750998053', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '黄建武', '51052119850508797x', '13015716570', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101);
