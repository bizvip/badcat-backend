define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'water/thumbsc/index' + location.search,
                    add_url: 'water/thumbsc/add',
                    edit_url: 'water/thumbsc/edit',
                    del_url: 'water/thumbsc/del',
                    multi_url: 'water/thumbsc/multi',
                    table: 'water_thumbs',
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
                        // 踩文章ID
                        {field: 'thumbscid', title: __('Thumbscid')},
                        // 踩文章
	                    {field: 'qia.title', title: __('Qia.title')},
                        // 分类
                        // {field: 'class', title: __('Class'), formatter: Table.api.formatter.status, searchList: {'1': '图片'}},
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