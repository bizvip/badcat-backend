<?php

namespace app\admin\model;

use think\Model;


class Topping extends Model
{


    // 表名
    protected $name = 'topping';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'list_text'
    ];


    public function getListList()
    {
        return ['2' => __('图片'),'0' => __('短文'),'1' => __('ASMR')];
    }


    public function getListTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['list']) ? $data['list'] : '');
        $list = $this->getListList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    // 关联文章
    public function community()
    {
        return $this->belongsTo('Community', 'communityid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
