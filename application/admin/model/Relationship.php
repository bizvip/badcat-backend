<?php

namespace app\admin\model;

use think\Model;


class Relationship extends Model
{


    // 表名
    protected $name = 'relationship';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'datetime';
    // 关注写入时间戳字段
    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';
    // 追加属性
    protected $append = [

    ];
    
    
    public function publisher()
    {
        return $this->belongsTo('Publisher', 'userid', 'id', [], 'LEFT');
    }

    public function user()
    {
        return $this->belongsTo('User', 'userid', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
