<?php

namespace app\admin\model\ask;

use think\Model;


class Ask extends Model
{


    // 表名
    protected $name = 'ask';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    //label
    public function labels(){
        return $this->belongsTo('label','label','id','','left')->setEagerlyType(0);
    }
    

}
