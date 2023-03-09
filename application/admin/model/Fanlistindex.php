<?php

namespace app\admin\model;

use think\Model;


class Fanlistindex extends Model
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
        return $this->belongsTo('Publisher', 'user_id', 'id', [], 'LEFT');
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
