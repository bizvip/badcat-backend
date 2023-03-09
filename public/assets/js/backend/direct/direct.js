define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'direct/direct/index' + location.search,
                    add_url: 'direct/direct/add',
                    edit_url: 'direct/direct/edit',
                    del_url: 'direct/direct/del',
                    multi_url: 'direct/direct/multi',
                    table: 'direct',
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
                        {field: 'id', title: __('Id')},
                        // 主播id
                        {field: 'anchor_id', title: __('Anchor_id')},
                        // 主播名称
	                    {field: 'anchor.name', title: __('Anchor.name')},
	                    // 头像
	                    {field: 'anchor.image', title: __('Anchor.image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
	                    // 房间号
                        {field: 'room_number', title: __('Room_number')},
                        // 分类名称
	                    {field: 'directclass.title', title: __('Directclass.title')},
	                    // 直播间标题
                        {field: 'direct_name', title: __('Direct_name')},
                        // 直播间封面
                        {field: 'direct_image', title: __('Direct_image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // 在线人数
                        {field: 'online', title: __('Online')},
                        // 是否开播
                        {field: 'switch', title: __('Switch'), table: table, formatter: Table.api.formatter.toggle},
                        // 是否置顶
                        {field: 'is_top', title:'是否置顶', formatter: Table.api.formatter.status, searchList: {'0': '未置顶', '1': '已置顶'}},
                        // 直播间样式
                        {field: 'istoll', title: __('Istoll'), formatter: Table.api.formatter.status, searchList: {'1': '免费', '2': '会员'}},
                        // 收费方式
                        {field: 'live_room_style', title: __('Room_style'), formatter: Table.api.formatter.status, searchList: {'one': '样式一', 'two': '样式二'}},
                        // 添加时间
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
                        // 修改时间
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, 
                        buttons: [
                                {
				                    name: 'top',
				                    text: '置顶',
				                    title: '置顶',
				                    classname: 'btn btn-success btn-edit btn-ajax',
				                    icon: '',
				                    url: 'direct/direct/top',
				                    success: function (data) {
				                        alert('133');
					                    console.log(data);
				                    },
				                    visible: function (row) {
					                    //返回true时按钮显示,返回false隐藏
					                    return true;
				                    }
			                    },
			                    {
				                    name: 'canceltop',
				                    text: '取消置顶',
				                    title: '取消置顶',
				                    classname: 'btn btn-success btn-edit btn-ajax',
				                    icon: '',
				                    url: 'direct/direct/canceltop',
				                    success: function (data) {
				                        alert('133');
					                    console.log(data);
				                    },
				                    visible: function (row) {
					                    //返回true时按钮显示,返回false隐藏
					                    return true;
				                    }
			                    }
                         ],
                        formatter: Table.api.formatter.operate} 
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
