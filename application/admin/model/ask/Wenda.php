<?php

namespace app\admin\model\ask;

use app\common\model\Config;
use think\Model;


class Wenda extends Model
{


    // 表名
    protected $name = 'ask';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        'ask_texts'
    ];

    // 发布者头像
    public function getAvatorImageAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['avator_image'])) {
            return config('host') . $data['avator_image'];
        } else {
            return $data['avator_image'];
        }
    }
    
    // 问题图片
    public function getAskTextsAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['ask_image'])) {
            return config('host') . $data['ask_image'];
        } else {
            return $data['ask_image'];
        }
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

    public function getVideoAttr($name)
    {

        $vide_prefix = Config::where('name', 'video_prefix')->value('value');
        if ($vide_prefix != '') {
            return $vide_prefix . substr($name, 24);
        }
        return $name;
    }

}
