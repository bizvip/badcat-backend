define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'direct/vip2/index' + location.search,
                    add_url: 'direct/vip2/add',
                    edit_url: 'direct/vip2/edit',
                    del_url: 'direct/vip2/del',
                    multi_url: 'direct/vip2/multi',
                    import_url: 'direct/vip2/import',
                    table: 'vip',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // 房间id
                        {field: 'direct_id', title: __('Direct_id')},
                        // 名称
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        // 头像
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // 等级
                        {field: 'level', title: __('Level'), operate: 'LIKE'},
                        // 性别
                        {field: 'sex', title: __('Sex'), operate: 'LIKE'},
                        // 贡献值
                        {field: 'contribution', title: __('Contribution'), operate: 'LIKE'},
                        // 创建时间
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        // 修改时间
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        // 分类
                        // {field: 'class', title: __('Class'), operate: 'LIKE'},
                        {field: 'class', title: __('Class'), formatter: Table.api.formatter.status, searchList: {'0': '贵宾', '1': '守护'}},
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
