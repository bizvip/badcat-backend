<?php

namespace app\admin\model\software;

use app\common\model\Config;
use think\Model;


class Dsoftware extends Model
{


    // 表名
    protected $name = 'software';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        'app_image',
        'screenshots_just',
        'screenshots_long',
        'videourl'
    ];


    public function subordinate()
    {
        return $this->belongsTo('Subordinate', 'class', 'id', [], 'LEFT')->setEagerlyType(0);
        // $name = \app\admin\model\software\Subordinate::column('name');
    }

    public function getAppImageAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['app_image'])) {
            return config('host') . $data['app_image'];
        } else {
            return $data['app_image'];
        }
    }
    
    public function getScreenshotsJustAttr($value, $data)
    {
        $arr = explode(',', $data['screenshots_just']);
        foreach ($arr as &$software) {
            // if ( ! preg_match('/^http/', $software)) {
            if ($software) {
                $software = config('host') . $software;
            }
        }
        return $arr;
    }
    
    public function getScreenshotsLongAttr($value, $data)
    {
        $arr = explode(',', $data['screenshots_long']);
        foreach ($arr as &$software) {
            // if ( ! preg_match('/^http/', $software)) {
            if ($software) {
                $software = config('host') . $software;
            }
        }
        return $arr;
    }
    
    // public function getVideourlAttr($value, $data)
    // {
    //     if ( ! preg_match('/^http/', $data['videourl'])) {
    //         return config('host') . $data['videourl'];
    //     } else {
    //         return $data['videourl'];
    //     }
    // }
    
    public function getVideourlAttr($value, $data)
    {
        // 如果'videourl'字段数值不为空
        if ($data['videourl']) {
         return config('host') . $data['videourl'];
        } else {
            return $data['videourl'];
        }
        
    }
    
    // 相关所属
    public function belong()
    {
        return $this->belongsTo('Belong', 'belong', 'id');
    }
}
