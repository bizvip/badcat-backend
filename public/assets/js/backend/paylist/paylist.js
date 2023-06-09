define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'paylist/paylist/index' + location.search,
                    add_url: 'paylist/paylist/add',
                    edit_url: 'paylist/paylist/edit',
                    del_url: 'paylist/paylist/del',
                    multi_url: 'paylist/paylist/multi',
                    table: 'pay_list',
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
                        // 充值金额
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        // 到账金额
                        {field: 'c_price', title: __('C_price'), operate:'BETWEEN'},
                        // 充值地址
                        {field: 'url', title: __('Url'), formatter: Table.api.formatter.url},
                        // 创建时间
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
                        // 修改时间
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange'},
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