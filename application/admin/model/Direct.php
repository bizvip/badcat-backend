<?php

namespace app\admin\model;

use think\Model;

use app\admin\model\Anchor;


class Direct extends Model
{


    // 表名
    protected $name = 'direct';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';
    // 追加属性
    protected $append = [
        'list_text',
        'direct_image_text'
    ];

    // 选择主播
    public function getanchorid()
    {
        $data = ['0' => '请选择主播'];
        $res = Anchor::select()->toArray();
        foreach ($res as $v) {
            $data[$v['id']] = $v['name'];
        }
        return $data;
    }
    
    // 选择分类
    public function getListList()
    {
        $data = ['0' => '请选择分类'];
        $res = Directclass::select()->toArray();
        foreach ($res as $v) {
            $data[$v['id']] = $v['title'];
        }
        return $data;
    }
    
    public function getListTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['list']) ? $data['list'] : '');
        $list = $this->getListList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getDirectImageTextAttr($name, $data)
    {
        if ( ! preg_match('/^http/', $data['direct_image'])) {
            return config('host') . $data['direct_image'];
        } else {
            return $data['direct_image'];
        }
    }
    
    // 主播
    public function anchor()
    {
        return $this->belongsTo('Anchor', 'anchor_id', 'id', [], 'LEFT');
    }
    
    // 主播搜索
    public function anchor_search()
    {
        return $this->belongsTo('Anchor', 'anchor_id', 'id', [], 'LEFT')->bind([
            'anchor_name'=>'name'
            ]);
    }
    
    // 分类
    public function directclass()
    {
        return $this->belongsTo('Directclass', 'list', 'id', [], 'LEFT');
    }
    
    // 贵宾
    public function vips()
    {
        return $this->hasMany('Vip', 'direct_id', 'id')->where('class',0);
    }
    
    // 真爱守护
    public function guards()
    {
        return $this->hasMany('Vip', 'direct_id', 'id')->where('class',1);
    }
}
