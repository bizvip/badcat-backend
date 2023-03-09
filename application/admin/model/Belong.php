<?php

namespace app\admin\model;

use think\Model;


class Belong extends Model
{


    // 表名
    protected $name = 'belong';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        'video_icon'
    ];

    public function getVideoIconAttr($value, $data)
    {
       if ( ! preg_match('/^http/', $data['image'])) {
            return config('host') . $data['image'];
        } else {
            return $data['image'];
        }
        //return config('host') . $data['image'];
    }
   
    public function Dvideo(){
        return $this->hasMany('Dvideo','belong','id');
    }



}
