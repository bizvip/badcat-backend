define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
	$.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "IP地址";};

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mom/mom/index' + location.search,
                    add_url: 'mom/mom/add',
                    edit_url: 'mom/mom/edit',
                    del_url: 'mom/mom/del',
                    multi_url: 'mom/mom/multi',
                    table: 'monitor',
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
	                    // 用户名
	                    {field: 'user.username', title: __('User.username')},
	                    // 手机号
	                    {field: 'user.mobile', title: __('Mobile')},
	                    // 登录次数
                        {field: 'num', title: __('Num')},
                        // 会员状态
	                    {field: 'class', title: __('Status'),formatter: Table.api.formatter.status, searchList: {'0': __('普通用户'), '1': __('会员用户')}},
	                    // IP地址
                        {field: 'ip', title: __('Ip')},
                        // 登录地址
	                    {field: 'address', title: __('Address')},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
