<?php

namespace app\admin\model;

use think\Model;


class Banner extends Model
{


    // 表名
    protected $name = 'direct_banner';

    // 自动写入时间戳字段
    // protected $autoWriteTimestamp = 'datetime';
    protected $autoWriteTimestamp = 'datetime';
    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'image_text'
    ];

    public function getImageTextAttr($name,$data)
    {
        if ( ! preg_match('/^http/', $data['image'])) {
            return config('host') . $data['image'];
        } else {
            return $data['image'];
        }
       
       // return config('host').$data['image'];
    }


}
