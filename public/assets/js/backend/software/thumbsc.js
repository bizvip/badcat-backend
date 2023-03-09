define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'software/thumbsc/index' + location.search,
                    add_url: 'software/thumbsc/add',
                    edit_url: 'software/thumbsc/edit',
                    del_url: 'software/thumbsc/del',
                    multi_url: 'software/thumbsc/multi',
                    table: 'app_thumbsc',
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
                        // 用户ID
                        {field: 'userid', title: __('Userid')},
                        // 用户名
	                    {field: 'user.username', title: __('User.name')},
                        // 踩软件ID
                        {field: 'thumbscid', title: __('Thumbscid')},
                        // 踩软件名
                        {field: 'software.title', title: __('Thumbsc')},
                        // 分类
                        // {field: 'class', title: __('Class')},
                        // {field: 'class', title: __('Class'), formatter: Table.api.formatter.status, searchList: {'0': '小视频', '1': '影视', '2': '社区视频', '3': '软件', '4': '吃瓜'}},
                        // 踩时间
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});