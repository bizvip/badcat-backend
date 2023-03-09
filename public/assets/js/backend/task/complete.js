define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'task/complete/index' + location.search,
                    add_url: 'task/complete/add',
                    edit_url: 'task/complete/edit',
                    del_url: 'task/complete/del',
                    multi_url: 'task/complete/multi',
                    import_url: 'task/complete/import',
                    table: 'complete',
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
	                    {field: 'user.username', title: __('User.username')},
                        // 任务ID
                        {field: 'tid', title: __('Tid')},
                        // 任务名称
	                    {field: 'task.name', title: __('Task.name')},
                        // 完成时间
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
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
