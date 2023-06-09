define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
	
	var Controller = {
		index: function () {
			// 初始化表格参数配置
			Table.api.init({
				extend: {
					index_url: 'tvideo/commenta/index' + location.search,
				    add_url: 'tvideo/commenta/add',
					edit_url: 'tvideo/commenta/edit',
					del_url: 'tvideo/commenta/del',
					multi_url: 'tvideo/commenta/multi',
					table: 'movie_commenta',
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
						{field: 'name', title: __('Name')},
						{
							field: 'avator_image',
							title: __('Avator_image'),
							events: Table.api.events.image,
							formatter: Table.api.formatter.image
						},
						// 分类		
						// {field: 'class', title: __('类型'), searchList: {"0":__('社区短文'),"1":__('社区图片'),"2":__('社区ASMR'),"3":__('影视'),"4":__('社区回答'),"5":__('短视频'),"6":__('吃瓜专区')}, formatter: Table.api.formatter.normal},
						// 被评论的视频ID	
						{field: 'tvideo.id', title: __('Video_id')},
						// 被评论的视频
						{field: 'tvideo.title', title: __('Video_title')},
				        // 评论内容		
						{field: 'content', title: __('Content')},
						// 评论时间
						{field: 'creat_time', title: __('Creat_time'), operate: 'RANGE', addclass: 'datetimerange'},
				        // 用户等级
						{field: 'level', title: __('level')},
						{
							field: 'tong',
							title: __('审核状态'),
							formatter: Table.api.formatter.status,
							searchList: {'0': __('待审核'), '1': __('已通过'), '2': '已拒绝'}
						},
						{
							field: 'buttons',
							operate: false,
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
									url: 'tvideo/commenta/tong?tong=1',
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
									confirm: '您确定要拒绝吗',
									icon: '',
									url: 'tvideo/commenta/tong?tong=2',
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
						}
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
