<?php

namespace app\admin\model\user;

use think\Model;


class Follow extends Model
{

    
    // 表名
    protected $name = 'relationship';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    
    // 关联用户
    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    // 关联点赞文章
    public function publisher()
    {
        return $this->belongsTo('app\admin\model\Publisher', 'userid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    

}
