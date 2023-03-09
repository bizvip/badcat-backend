define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'text/index' + location.search,
                    add_url: 'text/add',
                    edit_url: 'text/edit',
                    del_url: 'text/del',
                    multi_url: 'text/multi',
                    import_url: 'text/import',
                    table: 'text',
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
                        {field: 'text', title: __('Text'), formatter: function(value){return value.toString().substr(0, 20)}},
                        // 	评论分类 0=社区短文 1=社区图片- 2=社区ASMR 3=影视 4=社区回答
                        // {field: 'class', title: __('Class'), operate: 'LIKE'},
                        {field: 'class', title: __('Class'), searchList: {"0":__('社区短文'),"1":__('社区图片'),"2":__('社区ASMR'),"3":__('影视'),"4":__('社区问答'),"5":__('短视频'),"6":__('吃瓜专区')}, formatter: Table.api.formatter.normal},
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
