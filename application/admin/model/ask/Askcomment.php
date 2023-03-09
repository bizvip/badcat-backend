<?php

namespace app\admin\model\ask;

use think\Model;


class Askcomment extends Model
{


    // 表名
    protected $name = 'ask_comment';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

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


}
