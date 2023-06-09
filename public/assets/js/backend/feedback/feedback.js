define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
	$.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "关键字";};

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'feedback/feedback/index' + location.search,
                    add_url: 'feedback/feedback/add',
                    edit_url: 'feedback/feedback/edit',
                    del_url: 'feedback/feedback/del',
                    multi_url: 'feedback/feedback/multi',
                    table: 'feedback',
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
                        // 反馈人
	                    {field: 'user.username', title: __('User.username')},
	                    // 问题截图
	                    {field: 'images', title: __('Images'), events: Table.api.events.image, formatter: Table.api.formatter.images},
	                    // 遇到的问题
                        {field: 'encounter', title: __('Encounter')},
	                    // 反馈内容
                        {field: 'content', title: __('Content'), formatter: function(value){return value.toString().substr(0, 20)}},
                        // 提交时间
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
