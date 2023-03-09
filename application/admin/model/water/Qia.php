<?php

namespace app\admin\model\water;

use app\common\model\Config;
use think\Model;


class Qia extends Model
{


    // 表名
    protected $name = 'water';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        // 封面图
        'qia_text',
        // 发布者头像
        // 'avatar_text'
        // 'ask_texts'
    ];

    // 相关分类
    public function waterclass()
    {
        return $this->belongsTo('Waterclass', 'class', 'id')->setEagerlyType(0);
    }

    // 相关标签
    public function label()
    {
        return $this->belongsTo('Label', 'label', 'id');
    }
    
    // 发布者个人信息（后台）
    public function publisher()
    {
        return $this->belongsTo('Publisher', 'user_id', 'id', [], 'Left');
    }
    
    // 发布者个人信息（APP）
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'Left');
    }

    // // 标签
    // public function labels()
    // {
    //     return $this->belongsTo('label', 'label', 'id', '', 'left')->setEagerlyType(0);
    // }

    // 封面图
    public function getQiaTextAttr($value, $data)
    {
        if (!preg_match('/^http/', $data['qia_image'])) {
            return config('host') . $data['qia_image'];
        } else {
            return $data['qia_image'];
        }
        /*
        if ( ! preg_match('/^http/', $data['video_image'])) {
            return config('host') . $data['video_image'];
        }
        */
    }

    // // 发布者头像
    // public function getAvatarTextAttr($value, $data)
    // {
    //     if (!preg_match('/^http/', $data['avator_image'])) {
    //         return config('host') . $data['avator_image'];
    //     } else {
    //         return $data['avator_image'];
    //     }
    // }
    
    // 用户头像
    public function getAvatorImageAttr($value, $data)
    {
        if (!preg_match('/^http/', $data['avator_image'])) {
            return config('host') . $data['avator_image'];
        } else {
            return $data['avator_image'];
        }
    }
    
    // 视频流
    public function getVideoAttr($value, $data)
    {
        if (!preg_match('/^http/', $data['video'])) {
            return config('host') . $data['video'];
        } else {
            return $data['video'];
        }
    }

    public function getQiaAttr($name)
    {
        $vide_prefix = Config::where('name', 'qia_prefix')->value('value');
        if ($vide_prefix != '') {
            return $vide_prefix . substr($name, 24);
        }
        return $name;
    }
}
