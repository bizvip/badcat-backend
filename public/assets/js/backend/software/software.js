define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'software/software/index' + location.search,
                    add_url: 'software/software/add',
                    edit_url: 'software/software/edit',
                    del_url: 'software/software/del',
                    multi_url: 'software/software/multi',
                    table: 'software',
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
                        // ID
                        {field: 'id', title: __('Id')},
                        // 应用名
                        {field: 'title', title: __('Title')},
                        // 图标
                        {field: 'app_image', title: __('APP_image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // APK体积
                        {field: 'apksize', title: __('APKsize')},
                        // 下载链接
                        {field: 'downurl', title: __('DownUrl'), formatter: Table.api.formatter.url},
                        // // 应用截图
                        // {field: 'screenshots', title: __('Screenshots')},
                        // 短简介
                        {field: 'brief', title: __('Brief')},
                        // 应用截图(竖)
                        {field: 'screenshots_just', title: __('Screenshots_just'), events: Table.api.events.image, formatter: Table.api.formatter.images,operate:false},
                        // 应用截图(横)
                        {field: 'screenshots_long', title: __('Screenshots_long'), events: Table.api.events.image, formatter: Table.api.formatter.images,operate:false},
                        // 预览视频
                        {field: 'videourl', title: __('Videourl'), formatter: Table.api.formatter.url},
                        // 赞数
                        {field: 'hits', title: __('Hits')},
                        // 踩数
                        {field: 'cai', title: __('Cai')},
                        // 评论量
                        // {field: 'comments', title: __('Comments')},
                        // 浏览量
                        {field: 'browse', title: __('Browse')},
                        // 分类
                        {field: 'subordinate.name', title: __('Class')},
                        // 所属
                        {field: 'belong.name', title: __('Belong')},
                        // 收费方式
                        {field: 'istoll', title: __('Istoll'), formatter: Table.api.formatter.status, searchList: {'1': '免费', '2': '付费'}},
                        // 状态
                        {field: 'status', title:__('Status'), formatter: Table.api.formatter.status, searchList: {'n': '已下架', 'y': '上架中'}},
                        // 发布时间
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
                        // 更新时间
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange'},
                        
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