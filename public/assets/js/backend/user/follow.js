define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/follow/index' + location.search,
                    add_url: 'user/follow/add',
                    edit_url: 'user/follow/edit',
                    del_url: 'user/follow/del',
                    multi_url: 'user/follow/multi',
                    import_url: 'user/follow/import',
                    table: 'relationship',
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
                        // 关注者id
                        {field: 'user_id', title: __('User_id')},
                        // 关注者
                        {field: 'user.username', title: __('User.name')},
                        // 被关注者id
                        {field: 'userid', title: __('Userid')},
                        // 被关注者
                        {field: 'publisher.name', title: __('Publisher.name')},
                        // 关注者为
                        {field: 'class', title: __('Class'), formatter: Table.api.formatter.status, searchList: {'0': '用户', '1': '后台'}},
                        // 关注时间
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
