<?php

namespace app\admin\model;

use think\Model;


class Adv extends Model
{


    // 表名
    protected $name = 'adv';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;
    // protected $autoWriteTimestamp = 'datetime';
    // protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        'image_ad'
    ];

    //图片
    public function getImageAdAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['image'])) {
            return config('host') . $data['image'];
        } else {
            return $data['image'];
        }
     
    }

}
