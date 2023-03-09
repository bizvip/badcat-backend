define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'adv/adv/index' + location.search,
                    add_url: 'adv/adv/add',
                    edit_url: 'adv/adv/edit',
                    del_url: 'adv/adv/del',
                    multi_url: 'adv/adv/multi',
                    table: 'adv',
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
                        // 标题
                        {field: 'title', title: __('Title')},
	                    // 广告图片
	                    {field: 'image', title: __('Image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
	                    // 广告链接
                        {field: 'url', title: __('Url'), formatter: Table.api.formatter.url},
                        // 分类
	                    {field: 'class', title: __('Class'), formatter: Table.api.formatter.status, searchList: {'0': __('社区主界面'), '1': __('影视主界面'), '2': __('社区详情')}},
	                    // 发布时间
	                    {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
	        //绑定TAB事件
	        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		        // var options = table.bootstrapTable(tableOptions);
		        var typeStr = $(this).attr("href").replace('#', '');
		        var options = table.bootstrapTable('getOptions');
		        options.pageNumber = 1;
		        options.queryParams = function (params) {
			        // params.filter = JSON.stringify({type: typeStr});
			        params.type = typeStr;
			
			        return params;
		        };
		        table.bootstrapTable('refresh', {});
		        return false;
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
