﻿-- 运营类型
INSERT INTO d_business_type VALUES
(null,'出租车'),
(null,'快车');

-- 规则初始化
INSERT INTO d_rule VALUES
(null,1,'low','最低消费',7.2),
(null,1,'d05_23','05:00-23:00',1.6),
(null,1,'d23_00','23:00-00:00',2.16),
(null,1,'d00_05','00:00-05:00',2.16),

(null,1,'t05_23','普通时段',0.38),
(null,1,'t23_00','23:00-00:00',0.32),
(null,1,'t00_05','00:00-05:00',0.32),
(null,1,'t0730_0930','07:30-09:30',0.44),
(null,1,'t17_19','17:00-19:00',0.44),

(null,2,'low','最低消费',12),
(null,2,'d05_23','普通时段',1.3),
(null,2,'d23_00','23:00-00:00',1.86),
(null,2,'d00_05','00:00-05:00',1.86),

(null,2,'t05_23','05:00-23:00',0.33),
(null,2,'t23_00','23:00-00:00',0.27),
(null,2,'t00_05','00:00-05:00',0.27),
(null,2,'t0730_0930','07:30-09:30',0.39),
(null,2,'t17_19','17:00-19:00',0.39);


-- 订单状态
INSERT INTO d_order_list_status VALUES
(null,'未接客'),
(null,'未过期'),
(null,'已过期'),
(null,'待支付'),
(null,'已支付'),
(null,'用户已评价'), -- 用户已评价
(null,'司机已评价'), -- 司机已评价
(null,'均已评价'); -- 司机用户均已评价

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
(8,'查询报表','&#xe61a;'), -- chart
(9,'客服聊天','&#xe6d0;'), -- chat
(10,'规则管理','&#xe6f5;'); -- rule
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
(null,'新闻发布','admin/News/publish',      7),
(null,'新闻编辑','admin/News/edit',         7),
(null,'订单统计','admin/Chart/orderList',  8),
(null,'用户统计','admin/Chart/user',       8),
(null,'营销统计','admin/Chart/market',     8),
(null,'客服聊天','admin/Chats/chats',9),
(null,'快车规则','admin/Rule/car',10),
(null,'出租车规则','admin/Rule/taxi',10);

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
(null,1,19),
(null,1,20),
(null,1,21);

-- 员工表
INSERT INTO d_employee VALUES
(null,NOW(),'e10adc3949ba59abbe56e057f20f883e','duduPrince','嘟嘟王子',1,110000,110100,110101,'使用','defaultHead.jpg'),
(null,NOW(),'e10adc3949ba59abbe56e057f20f883e','duduWillow','嘟嘟小魔仙',1,110000,110100,110101,'使用','defaultHead.jpg'),
(null,NOW(),'e10adc3949ba59abbe56e057f20f883e','duduMan','嘟嘟侠',1,110000,110100,110101,'使用','defaultHead.jpg'),
(null,NOW(),'e10adc3949ba59abbe56e057f20f883e','duduboy','嘟嘟男孩',1,110000,110100,110101,'使用','defaultHead.jpg'),
(null,NOW(),'e10adc3949ba59abbe56e057f20f883e','duduDaddy','嘟嘟奶爸',1,110000,110100,110101,'使用','defaultHead.jpg'),
(null,NOW(),'e10adc3949ba59abbe56e057f20f883e','duduMagic','嘟嘟魔人',1,110000,110100,110101,'使用','defaultHead.jpg');

-- 用户表
INSERT INTO d_user VALUES
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '何贻杰', '512501197203035172', '15959027027', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101,'o7r8T0YzM9NJInXv7Ek1iR-yYOOU'),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '潘晓文', '51052119850508797x', '15606923823', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101,'o7r8T0WuXQtbps8ZqhepfKhzLNn8'),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '刘家福', '512501197506045175', '15605907283', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101,'o7r8T0XJAc4FSyQjVK_VJ-FhGppY'),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '叶诚炜', '512501196512305186', '13559182419', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101,'o7r8T0VVqwIJDWtuj8pIE8eaKQQQ'),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '何岳', '512501197203035172', '18750998053', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101,'o7r8T0ZhAGxFdtVZAjqF10M2CmeY'),
(null, now(), 'e10adc3949ba59abbe56e057f20f883e', '黄建武', '51052119850508797x', '13015716570', 10, 10000, '使用', 'user-head.jpg', '仓山区金山大道', 110000, 110100, 110101,'o7r8T0WsFIMN-1JCSUeoDS70alQ0');

-- 司机表
INSERT INTO d_driver VALUES
(null,'58cf703f664397ec4f0ac359b84b565c',now(),110000,110100,110101,'就不告诉你','叶诚炜',350111199504221911,now(),'闽AD86','AD86知道不','叶诚炜',now(),99999,13559182419,'使用',10,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTIIiaaUdsrCxZRbn7jh9xVsE9GwonvWlPGYQlVfk7SATc2NVkjGia178RD3WfGw6ibCAJUCvSWzQ7RvA/0',1,123456,'oMv4i0ewxqDKYYBmkckaQYXYF4Mo'),
(null,'58cf703f664397ec4f0ac359b84b565c',now(),350000,350100,350104,'哈哈哈','嘟嘟嘟','512501197506045175',now(),'闽A66666','奇瑞QQ','嘟嘟嘟','2012-06-24',0,'15605605656','使用',10,'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83epQCxC0KlJZdSzIQlwDqf1JpJXgCYxiblPXIe0NnIZiaKhLvVXiciawKvo5hKYO5VB8fBCnJxNQ7f1htQ/0',2,'','oMv4i0VQv1OV5CyD19OpfvwuKCw4'),
(null,'58cf703f664397ec4f0ac359b84b565c',now(),350000,350100,350104,'动次打次','嘟嘟打的','512501197506045175',now(),'闽A88888','玛莎拉蒂','小魔仙','2012-06-24',0,'15606923823','使用',10,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJLSd1FhgLoLrs9hxYcxsQDsZhOVibiaCaP8QRTUdI2fqgvO66rRY6hyCiaJX5TYP54jD0ojedUxXYrg/0',2,'','oMv4i0T_uFEgFWTkvnGbOSjmYOrs'),
(null,'9bf16b7c535adabb293245f39c574ecd',now(),350000,350100,350104,'桥洞','何贻杰','35018119930615215X',now(),'闽A88888','宝马','何贻杰','2015-11-28 00:00:00',0,'15959027027','使用',10,'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83erp8XI8ESuGGddsOiaRM397nurPicoA4poMwbpdApggDebU7T78Uerj6uRMh8l588V8V2a2xjPFkNRg/0',2,'','oMv4i0YtfcMTm2UFsXfEug8Ndh_U'),
(null,'9bf16b7c535adabb293245f39c574ecd',now(),350000,350100,350104,'桥洞','何岳','35018765930615215X',now(),'闽A88998','宝马','何岳','2015-11-28 00:00:00',0,'15959027027','使用',10,'https://wx.qlogo.cn/mmopen/vi_32/icHicAP9M4M4AIBzrA5Qf2BDYP4lRJ21zHM6GzfoW8vI9PQvggKPllHl3kY7DwXVagd2pXa46yYKyf2ZYdE1uplQ/0',2,'','oMv4i0V0AJjNUjlmwC17b3tfmB94'),
(null,'9bf16b7c535adabb293245f39c574ecd',now(),350000,350100,350104,'桥洞','何贻辉','350181199611061836',now(),'闽B88888','宝马','何贻辉','2015-11-28 00:00:00',0,'15959028028','使用',10,'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTIIiaaUdsrCxZRbn7jh9xVsE9GwonvWlPGYQlVfk7SATc2NVkjGia178RD3WfGw6ibCAJUCvSWzQ7RvA/0',2,'','oMv4i0WyQpcxfHoqMYlu7hi65nz8');

-- 初始化订单数据
-- INSERT INTO d_order_list VALUES
-- (null,4,22,now(),now(),1,1,1,10,5,5,10,10,10,10,'这是个起始点1','这是个终点1',1),
-- (null,4,22,now(),now(),1,2,1,10,5,5,10,10,10,10,'这是个起始点2','这是个终点2',1),
-- (null,4,22,now(),now(),1,3,1,10,5,5,10,10,10,10,'这是个起始点3','这是个终点3',1),
-- (null,4,22,now(),now(),1,4,2,10,5,5,10,10,10,10,'这是个起始点4','这是个终点4',2),
-- (null,4,22,now(),now(),1,1,2,10,5,5,10,10,10,10,'这是个起始点5','这是个终点5',1),
-- (null,4,22,now(),now(),1,2,2,10,5,5,10,10,10,10,'这是个起始点6','这是个终点6',1),
-- (null,4,22,now(),now(),1,3,3,10,5,5,10,10,10,10,'这是个起始点7','这是个终点7',1),
-- (null,4,22,now(),now(),1,4,3,10,5,5,10,10,10,10,'这是个起始点8','这是个终点8',2),
-- (null,4,22,now(),now(),1,1,3,10,5,5,10,10,10,10,'这是个起始点9','这是个终点9',1),
-- (null,4,22,now(),now(),1,2,3,10,5,5,10,10,10,10,'这是个起始点10','这是个终点10',1),
-- (null,4,22,now(),now(),1,3,3,10,5,5,10,10,10,10,'这是个起始点11','这是个终点11',1),
-- (null,4,22,now(),now(),1,4,3,10,5,5,10,10,10,10,'这是个起始点12','这是个终点12',2),
-- (null,4,22,now(),now(),1,1,4,10,5,5,10,10,10,10,'这是个起始点13','这是个终点13',1),
-- (null,4,22,now(),now(),1,2,4,10,5,5,10,10,10,10,'这是个起始点14','这是个终点14',1),
-- (null,4,22,now(),now(),1,3,4,10,5,5,10,10,10,10,'这是个起始点15','这是个终点15',1),
-- (null,4,22,now(),now(),1,4,4,10,5,5,10,10,10,10,'这是个起始点16','这是个终点16',2);
-- 资讯
INSERT INTO d_news VALUES
(null,'滴滴平台正式获得网约车牌照 将更好服务亿',now(),'发布','../public/static/img/update/news_1512360982.jpg',
'4月28日，全球领先的出行平台滴滴出行今日宣布完成新一轮超过55亿美元融资，以支持其全球化战略的推进和前沿技术领域的投资。'),
(null,'滴滴完成超55亿美元新一轮融资 布局国际',NOW(),'发布','../public/static/img/update/news_1512447789.jpg',
'3月2日消息，滴滴出行今天宣布，经过天津市交通运输委员会等多部门审查，滴滴平台已获得天津市颁发的《网络预约出租车汽车经营许可证》，这一获批意味着滴滴网约车平台符合国家政策，得到政府认可。');