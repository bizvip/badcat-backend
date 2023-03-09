<?php

namespace app\admin\model\water;

use think\Model;


class Comment extends Model
{


    // 表名
    protected $name = 'water_comment';

    // 自动写入时间戳字段
    // protected $autoWriteTimestamp = false;
    protected $autoWriteTimestamp = 'datetime';
    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [

    ];

    public function getAvatorImageAttr($value)
    {
        if ( ! preg_match('/^http/', $value)) {
            return config('host') .$value;
        } else {
            return $value;
        }
        
        //return config('host') . $value;
    }
    
    // 关联被评论的文章
    public function qia()
    {
        return $this->belongsTo('Qia', 'water_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
