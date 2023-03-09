define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
	$.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "影片列表";};

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'video/dvideo/index' + location.search,
                    add_url: 'video/dvideo/add',
                    edit_url: 'video/dvideo/edit',
                    del_url: 'video/dvideo/del',
                    multi_url: 'video/dvideo/multi',
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
                        {field: 'id', title: __('Vod_id')},
                        // 片名
                        {field: 'vod_name', title: __('Vod_name')},
                        // 封面图
                        {field: 'vod_pic', title: __('Vod_pic'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // 导演
                        {field: 'vod_director', title: __('Vod_director')},
                        // // 演员
                        // {field: 'actor', title: __('Actor')},
                        // 上映时间
                        {field: 'vod_year', title: __('Vod_year')},
                        // 地区
                        {field: 'vod_area', title: __('Vod_area')},
                        // 语言
                        {field: 'vod_lang', title: __('Vod_lang')},
                        // 所属专题
                        // {field: 'belong.title', title: __('Vod_belong'), formatter: Table.api.formatter.status, searchList: {'1': '免费', '2': '付费'}},
                        {field: 'belong.title', title: __('Vod_belong')},
                        // 分类
                        {field: 'subordinate.name', title: __('Vod_class'),formatter: Table.api.formatter.status},
                        // 视频流链接
                        {field: 'vod_play_url', title: __('Vod_play_url'), formatter: Table.api.formatter.url},
	                    // {field: 'actress.name', title: __('演员')},
                        // 单独购买价格
                        {field: 'watch_price', title: __('单独购买价格')},
                        // 单独购买有效期
                        {field: 'watch_day', title: __('单独购买有效期')},
                        // 点赞量
                        {field: 'vod_hits', title: __('Vod_hits')},
                        // 评论量
                        {field: 'vod_comments', title: __('Vod_comments')},
                        // 浏览量
                        {field: 'vod_browse', title: __('Vod_browse')},
                        // 收费方式
                        {field: 'vod_istoll', title: __('Vod_istoll'), formatter: Table.api.formatter.status, searchList: {'1': '免费', '2': '付费'}},
                        // 发布时间
                        {field: 'vod_create_time', title: __('Vod_create_time'), operate:'RANGE', addclass:'datetimerange'},
                        
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
