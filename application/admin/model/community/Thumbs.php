<?php

namespace app\admin\model\community;

use think\Model;


class Thumbs extends Model
{


    // 表名
    protected $name = 'community_thumbs';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'datetime';
    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    
    // 关联用户
    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'userid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    // 关联点赞文章
    public function community()
    {
        return $this->belongsTo('app\admin\model\Community', 'thumbsid', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
