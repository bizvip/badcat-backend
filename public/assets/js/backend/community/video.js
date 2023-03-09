define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
	$.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "视频标题";};

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'community/video/index' + location.search,
                    add_url: 'community/video/add',
                    edit_url: 'community/video/edit',
                    del_url: 'community/video/del',
                    multi_url: 'community/video/multi',
                    table: 'community',
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
                        // // 发布者名称
	                    // {field: 'name', title: __('Name')},
	                    // // 发布者头像
	                    // {field: 'avator_image', title: __('Avator_image'), events: Table.api.events.image, formatter: Table.api.formatter.image,operate:false},
	                    // 标题
                        {field: 'title', title: __('Title'), formatter: function(value){return value.toString().substr(0, 20)}},
                        // 标签
	                    // {field: 'labels.name', title: __('标签')},
	                    // 视频封面
                        {field: 'video_image', title: __('Video_image'), events: Table.api.events.image, formatter: Table.api.formatter.image,operate:false},
	                    // 视频流连接
	                    {field: 'video', title: __('Video'), formatter: Table.api.formatter.url},
                        // // 点赞量
                        // {field: 'fabulous', title: __('Fabulous')},
                        // // 踩数
                        // {field: 'stamp', title: __('Stamp')},
                        // 浏览量
                        {field: 'browse', title: __('Browse')},
                        // // 评论数
	                    // {field: 'comment', title: __('评论数')},
	                    // 发布人
	                   // {field: 'status', title: __('Status'),formatter: Table.api.formatter.status, searchList: {'0': __('用户'), '1': __('后台')}},
	                    // 发布时间
                        {field: 'create_time', title: __('Create_time')},
                        // 审核状态
	                    {field: 'tong', title: __('审核状态'), formatter: Table.api.formatter.status, searchList: {'0': __('待审核'), '1': __('已通过'),'2':'已拒绝'}},
	                    {
	                        field: 'buttons',
	                        operate:false,
	                        width: "120px",
	                        title: __('审核操作'),
	                        table: table,
	                        events: Table.api.events.operate,
	                        buttons: [
	                            {
	                                name: 'ajax',
	                                text: __('通过'),
	                                title: __('通过'),
	                                classname: 'btn btn-success btn-edit btn-ajax',
	                                icon: '',
	                                url: 'community/video/tong?tong=1',
	                                success: function (data) {
	                                	console.log(data);
	                                },
	                                visible: function (row) {
	                                    //返回true时按钮显示,返回false隐藏
	                                    return true;
	                                }
	                            },
		                        {
			                        name: 'addtabs',
			                        text: __('拒绝'),
			                        title: __('拒绝'),
			                        classname: 'btn btn-danger btn-del btn-ajax',
			                        confirm:'您确定要拒绝吗',
			                        icon: '',
			                        url: 'community/video/tong?tong=2',
			                        callback: function (data) {
				                        Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
			                        },
			                        visible: function (row) {
				                        //返回true时按钮显示,返回false隐藏
				                        return true;
			                        }
		                        },
	                        ],
	                        formatter: Table.api.formatter.buttons
	                    },
	                    {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
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
