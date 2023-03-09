define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
	$.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "订单管理";};

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shopdingdan/shopdingdan/index' + location.search,
                    add_url: 'shopdingdan/shopdingdan/add',
                    edit_url: 'shopdingdan/shopdingdan/edit',
                    del_url: 'shopdingdan/shopdingdan/del',
                    multi_url: 'shopdingdan/shopdingdan/multi',
                    table: 'shopdingdan',
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
                        {field: 'username', title: '购买用户'},
                        {field: 'oderid', title: '订单号'},
                        {field: 'title', title:'商品名称'},
                        {field: 'type', title:'类型', formatter: Table.api.formatter.status, searchList: {'0': '商品', '1': '会员卡'}},
                        {field: 'paytype', title: '支付方式'},
                        {field: 'pay_jiage', title: '金额'},
                        {field: 'dizhi', title: '收货地址', formatter: function(value){return value.toString().substr(0, 20)}},
                        {field: 'time', title:'支付日期', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'fahuoxinxi', title:'发货信息', formatter: Table.api.formatter.status, searchList: {'0': '目前暂未发货', '1': '已发货'}},
                        // 是否发货
                        {field: 'fahuoxinxi', title: '是否发货', table: table, formatter: Table.api.formatter.toggle}, 
                    ]
                ]
            });
            //libra20201213
           $('.change').click(function(e){
               var typeStr = $(this).attr("href").replace('#', '');
		       var options = table.bootstrapTable('getOptions');
		      
		       var ids = "";
		       var rows = options.data;
		       console.log(4,rows);
                for (var i = 0; i < rows.length; i++) {
                    if(rows[i][0]==true){
                        ids += rows[i].id + ',';
                    }
                    
                }
                ids = ids.substring(0, ids.length - 1);
                $.post("shopdingdan/shopdingdan/change",{
			        ids:ids
		        },function(result){
			      window
		        });
               
           });

            // 为表格绑定事件
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
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        addcard: function () {
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
