define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'card/card/index' + location.search,
                    add_url: 'card/card/add',
                    edit_url: 'card/card/edit',
                    del_url: 'card/card/del',
                    multi_url: 'card/card/multi',
                    table: 'vipcard',
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
                        {field: 'name', title: __('Name')},
                        {field: 'time', title: __('Time')},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'y_money', title: __('Y_money'), operate:'BETWEEN'},
                        // {field: 'agent_money', title:'代理提卡价格', operate:'BETWEEN'},
                        {field: 'url', title:'跳转链接', operate:'BETWEEN'},
                        // {field: 'url', title: __('跳转链接'), formatter: function(value){return value.toString().substr(0, 20)}},
                        // 发布时间
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
                        // 是否开启
                        {field: 'switch', title: __('Switch'), table: table, formatter: Table.api.formatter.toggle},
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