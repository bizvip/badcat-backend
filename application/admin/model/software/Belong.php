<?php

namespace app\admin\model\software;

use think\Model;


class Belong extends Model
{


    // 表名
    protected $name = 'softwarebel';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        // 图标+域名
        'app_icon',
        // 背景图+域名
        'app_icon_b'
    ];
    
    // 图标
    public function getAppIconAttr($value, $data)
    {
       if ( ! preg_match('/^http/', $data['image'])) {
            return config('host') . $data['image'];
        } else {
            return $data['image'];
        }
    }
    
    // 背景图
    public function getAppIconBAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['b_image'])) {
            return config('host') . $data['b_image'];
        } else {
            return $data['b_image'];
        }
        //return config('host') . $data['b_image'];
    }
    
}
