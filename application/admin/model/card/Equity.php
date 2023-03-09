<?php

namespace app\admin\model\card;

use think\Model;


class Equity extends Model
{


    // 表名
    protected $name = 'vipequity';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'image_list'
    ];
    
    // 图片
    public function getImageListAttr($value, $data)
    {
        if (!preg_match('/^http/', $data['icon'])) {
            return config('host') . $data['icon'];
        } else {
            return $data['icon'];
        }
    }

    public function getTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['time']) ? $data['time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
