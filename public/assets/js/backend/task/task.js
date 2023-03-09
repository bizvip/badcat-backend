define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'task/task/index' + location.search,
                    add_url: 'task/task/add',
                    edit_url: 'task/task/edit',
                    del_url: 'task/task/del',
                    multi_url: 'task/task/multi',
                    table: 'task',
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
                        // 任务名称
                        {field: 'name', title: __('Name')},
                        // 任务图标
                        {field: 'image', title: __('Image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // 获得积分
                        {field: 'agent', title: __('Agent')},
                        // 获得VIP天数
                        {field: 'vip', title: __('Vip')},
                        // 任务可重复
                        {field: 'repeatable', title: __('Repeatable'), formatter: Table.api.formatter.status, searchList: {'0': '不可重复', '1': '可重复'}},
                        // 是否显示
                        {field: 'switch', title: __('Switch'), table: table, formatter: Table.api.formatter.toggle},
                        // 发布时间
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
