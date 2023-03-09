<?php

namespace app\admin\model;

use app\common\model\Config;
use think\Model;


class Watermelon extends Model
{


    // 表名
    protected $name = 'watermelon';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        'image_text',
    ];


    public function subordinate()
    {
        return $this->belongsTo('Subordinate', 'class', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    //label
    public function labels()
    {
        return $this->belongsTo('vlabel', 'label', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function actress()
    {
        return $this->belongsTo('actress', 'actress', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    //封面
    public function getImageTextAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['watermelon_image'])) {
            return config('host') . $data['watermelon_image'];
        } else {
            return $data['watermelon_image'];
        }
    }

    public function belong()
    {
        return $this->belongsTo('Belong', 'belong', 'id');
    }

    //视频前缀
    public function getUrlAttr($name)
    {
        $vide_prefix = Config::where('name', 'video_prefix')->value('value');
        if ($vide_prefix != '') {
            return $vide_prefix . substr($name, 24);
        }
        return $name;
    }
}
