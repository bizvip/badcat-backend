<?php

namespace app\admin\model\water;

use think\Model;


class Top extends Model
{


    // 表名
    protected $name = 'watertop';

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
    
    // // 相关分类
    // public function waterclass()
    // {
    //     return $this->belongsTo('Waterclass', 'class', 'id', [], 'LEFT')->setEagerlyType(0);
    // }

    // // 相关标签
    // public function label()
    // {
    //     return $this->belongsTo('Label', 'label', 'id');
    // }
    
    // // 发布者个人信息
    // public function publisher()
    // {
    //     return $this->belongsTo('Publisher', 'user_id', 'id', [], 'Left');
    // }
    
    // 文章类型
    public function getListList()
    {
        return ['1' => __('默认')];
    }

    public function getListTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['list']) ? $data['list'] : '');
        $list = $this->getListList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function water()
    {
        return $this->belongsTo('Water', 'waterid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
