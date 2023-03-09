<?php

namespace app\admin\model;

use think\Model;


class Mom extends Model
{


    // 表名
    protected $name = 'monitor';

    // 自动写入时间戳字段
    // protected $autoWriteTimestamp = 'datetime';
    // protected $dateFormat = 'Y-m-d H:i:s';
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];


    public function user()
    {
        return $this->belongsTo('User', 'userid', 'id', [], 'LEFT');
    }
}
