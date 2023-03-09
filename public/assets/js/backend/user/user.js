define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: 'user/user/add',
                    edit_url: 'user/user/edit',
                    del_url: 'user/user/del',
                    multi_url: 'user/user/multi',
                    table: 'user',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true},
                        // 用户名
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        // 头像
	                    {field: 'avatar', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
	                    // 手机号
	                    {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
	                    // 背景图
	                    {field: 'background', title: __('Background'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
	                    // 个性签名
	                    {field: 'bio', title: __('Bio'), formatter: function(value){return value.toString().substr(0, 20)}},
	                    // 性别
	                    {field: 'gender', title: __('Gender'), formatter: Table.api.formatter.status, searchList: {'0': __('男'), '1':'女'}},
	                    // 年龄
	                    {field: 'age', title: __('Age'), operate: 'LIKE'},
	                    // vip等级
	                    {field: 'level', title: __('Level'), formatter: Table.api.formatter.status, searchList: {'0': __('LV0'), '1': __('LV1'), '2':'LV2'}},
	                    // 积分
	                    {field: 'integral', title: __('Integral'), operate: 'LIKE'},
	                    // 待审核积分
	                    {field: 'integral_examine', title: __('Integral_examine'), operate: 'LIKE'},
	                    // 余额
	                    {field: 'money', title: __('Money'), operate: 'LIKE'},
	                    // 绑定的邮箱
	                    {field: 'email', title: __('Email'), operate: 'LIKE'},
	                    // 关注数
	                    {field: 'guanzhu', title: __('Guanzhu'), operate: 'false'},
	                    // 粉丝数
	                    {field: 'fensi', title: __('Fensi'), operate: 'false'},
	                    // vip到期时间
	                    {field: 'vip_time', title: __('VIP到期时间'), operate: 'RANGE', addclass: 'datetimerange', sortable: true},
	                    // {field: 'agent', title: __('是否为代理'), formatter: Table.api.formatter.status, searchList: {'0': __('否'), '1': __('是')}},
	                    // 邀请码
	                    {field: 'number', title: __('Number'), operate: 'LIKE'},
	                    // 二维码
	                    // {field: 'photo', title: __('Photo'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
	                    // 上级
	                    {field: 't_number', title: __('T_number'), formatter: Table.api.formatter.status},
	                    // {field: 'user.username', title: __('T_number'), formatter: Table.api.formatter.status},
	                    // 注册IP地址
	                    {field: 'joinip', title: __('Joinip'), operate: 'LIKE'},
	                    // 登录IP地址
	                    {field: 'loginip', title: __('Loginip'), operate: 'LIKE'},
	                    // 设备IMEI码
	                    {field: 'imei', title: __('Imei'), operate: 'LIKE'},
	                    // 手机品牌
	                    {field: 'brand', title: __('Brand'), operate: 'LIKE'},
	                    // 手机型号
	                    {field: 'model', title: __('Model'), operate: 'LIKE'},
	                    // 系统名称
	                    {field: 'osName', title: __('OsName'), operate: 'LIKE'},
	                    // 上次登录时间
	                    {field: 'prevtime', title: __('Prevtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
	                    // 登录失败次数
	                   // {field: 'loginfailure', title: __('Loginfailure'), operate: 'LIKE'},
	                    // 连续登录天数
	                    {field: 'successions', title: __('Successions'), operate: 'LIKE'},
	                    // 最大登录天数
	                    {field: 'maxsuccessions', title: __('Maxsuccessions'), operate: 'LIKE'},
	                    // 注册时间
	                    {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
	                    // {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
	                    {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('正常'), hidden: __('禁封')}},
	                    // {
		                  //  field: 'buttons',
		                  //  width: "120px",
		                  //  title: '操作',
		                  //  table: table,
		                  //  events: Table.api.events.operate,
		                  //  buttons: [
			                 //   {
			                 //       name: 'daili', 
			                 //       text: '成为代理', 
			                 //       title: '成为代理', 
			                 //       icon: '', 
                    //     	        classname: 'btn btn-primary btn-dialog', 
                    //     	        url: 'user/user/daili?username={username}&number={number}'
                    //             },
			                 //   {
				                //     name: 'addtabs', 
				                //     text: '取消代理',
				                //     title: '取消代理',
				                //     classname: 'btn btn-success btn-edit btn-ajax', 
				                //     // confirm:'您确定要取消吗',
				                //     icon: '',
				                //     url: 'user/user/canceldaili?title={title}&housename={housename}&img={img}&address={address}&houseimg={houseimg}&roomurl={roomurl}&xuhao={xuhao}',
				                //     success: function (data) {
					               //     //Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
				                //     },
				                //     visible: function (row) {
					               //     //返回true时按钮显示,返回false隐藏
					               //     return true;
				                //     }
			                 //   }
			                    
		                  //  ],
		                  //  formatter: Table.api.formatter.buttons
	                   // },
	                    {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        daili: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
