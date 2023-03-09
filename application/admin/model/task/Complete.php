<?php

namespace app\admin\model\task;

use think\Model;


class Complete extends Model
{

    // 表名
    protected $name = 'complete';
    
    // 自动写入时间戳字段
    // protected $autoWriteTimestamp = 'datetime';
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    
    public function user()
    {
        return $this->belongsTo('User', 'userid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function task()
    {
        return $this->belongsTo('Task', 'tid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    

}
