define(['jquery', 'bootstrap', 'backend', 'table', 'form','selectpage'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'water/top/index' + location.search,
                    add_url: 'water/top/add',
                    // edit_url: 'water/top/edit',
                    del_url: 'water/top/del',
                    multi_url: 'water/top/multi',
                    table: 'watertop',
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
                        // 文章id
                        {field: 'waterid', title: __('Waterid')},
                        // 标题
	                    {field: 'water.title', title: __('Water.title')},
	                    // 文章类型
	                    {field: 'list', title: __('类型'), searchList: {"1":__('默认')}, formatter: Table.api.formatter.normal},
	                    // 添加时间
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
	        $(document).on("change", "#c-list", function () {
		        $.post("water/top/water",{
			        //搜索条件，上一个selectpage选择完后传过来的id作为此次搜索的条件
			        class:$('#c-list').val()
		        },function(result){
			        var str = '';
			        $.each(result, function (n, value) {
				        str += '<option value="' + n + '" {in name="key" value=""}selected{/in}>' + value + '</option>';
			        });
			        $('#c-list1').html(str);
		        });
	        });
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
