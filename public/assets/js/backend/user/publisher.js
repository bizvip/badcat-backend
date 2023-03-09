define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/publisher/index' + location.search,
                    add_url: 'user/publisher/add',
                    edit_url: 'user/publisher/edit',
                    del_url: 'user/publisher/del',
                    multi_url: 'user/publisher/multi',
                    table: 'publisher',
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
                        {field: 'id', title: __('ID')},
                        // 发布者名称
                        {field: 'name', title: __('Name')},
                        // 发布者头像
                        {field: 'image', title: __('Image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // 背景图
	                    {field: 'background', title: __('Background'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        // 个性签名
	                    {field: 'bio', title: __('Bio'), formatter: function(value){return value.toString().substr(0, 20)}},
	                    // 性别
	                    {field: 'gender', title: __('Gender'), formatter: Table.api.formatter.status, searchList: {'0': __('男'), '1':'女'}},
                        // 关注数
                        {field: 'guanzhu', title: __('Guanzhu')},
                        // 粉丝数
                        {field: 'fensi', title: __('Fensi')},
                        // 等级
                        {field: 'level', title: __('Level')},
                        // 操作
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