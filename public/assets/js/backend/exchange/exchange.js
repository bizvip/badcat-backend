define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exchange/exchange/index' + location.search,
                    add_url: 'exchange/exchange/add',
                    piadd_url: 'exchange/exchange/piadd',
                    edit_url: 'exchange/exchange/edit',
                    del_url: 'exchange/exchange/del',
                    multi_url: 'exchange/exchange/multi',
                    table: 'exchange',
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
                        // 激活码
                        {field: 'code', title: __('Code')},
                        // 种类
                        {field: 'class', title: __('Class'), searchList: {"1":__('Class1'),"2":__('Class2'),"3":__('Class3'),"4":__('Class4'),"5":__('Class5'),"6":__('Class6')}, formatter: Table.api.formatter.normal},
                        // 状态
                        {field: 'list', title: __('List'), searchList: {"0":__('List0'),"1":__('List1')}, formatter: Table.api.formatter.normal},
                        // 添加时间
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'userid', title: __('Userid')},
                        {field: 'user.username', title: __('兑换者')},
                        {field: 'user.mobile', title: __('手机号')},
                        {field: 'note', title: __('备注')},
                        {field: 'use_time', title: __('兑换时间')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                // 追加表格参数（页数）
                pageList:[10,25,50,200,500,1000]
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
function piliang(){
    $('.no-padding').append(`<div class="layui-layer layui-layer-iframe layui-layer-border layui-layer-fast" id="layui-layer2" type="iframe" times="2" showtime="0" contype="string" style="z-index: 19891018; width: 800px; height: 600px; top: 143.5px; left: -10px;"><div class="layui-layer-title" style="cursor: move;">添加</div><div id="" class="layui-layer-content"><iframe scrolling="auto" allowtransparency="true" id="layui-layer-iframe2" name="layui-layer-iframe2" onload="this.className='';" class="" frameborder="0" src="/rdyamTtYRB.php/exchange/exchange/add?dialog=1" style="height: 507px;"></iframe></div><div class="layui-layer-btn layui-layer-footer">
        <div class="row"><label class="control-label col-xs-12 col-sm-2"></label><div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed">确定</button>
            <button type="reset" class="btn btn-default btn-embossed">重置</button>
        </div></div>
        
    </div><span class="layui-layer-setwin"><a class="layui-layer-min" href="javascript:;"><cite></cite></a><a class="layui-layer-ico layui-layer-max" href="javascript:;"></a><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a></span><span class="layui-layer-resize"></span></div>`);
    console.log($('#layui-layer1'))
}
