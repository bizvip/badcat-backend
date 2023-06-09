<?php

namespace app\admin\model;

use think\Model;


class Other extends Model
{


    // 表名
    protected $name = 'other';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'image_text'
    ];
    
    //图片
    public function getImageTextAttr($value,$data)
    {
        if ( ! preg_match('/^http/', $data['image'])) {
            return config('host') .$data['image'];
        } else {
            return $data['image'];
        }
        //return config('host') . $data['image'];
    }


}
