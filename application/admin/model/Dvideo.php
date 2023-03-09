<?php

namespace app\admin\model;

use app\common\model\Config;
use think\Model;


class Dvideo extends Model
{


    // 表名
    protected $name = 'video';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        // 封面图+域名
        // 'vod_pic',
    ];


    public function subordinate()
    {
        return $this->belongsTo('Subordinate', 'vod_class', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function belong()
    {
        // return $this->belongsTo('Belong', 'belong', 'id');
        return $this->belongsTo('Belong', 'vod_belong', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    //label
    // public function labels()
    // {
    //     return $this->belongsTo('vlabel', 'label', 'id', [], 'LEFT')->setEagerlyType(0);
    // }

    // public function actress()
    // {
    //     return $this->belongsTo('actress', 'actress', 'id', [], 'LEFT')->setEagerlyType(0);
    // }

    //封面
    public function getVod_picAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['vod_pic'])) {
            return config('host') . $data['vod_pic'];
        } else {
            return $data['vod_pic'];
        }
    }

    //视频前缀
    public function getVod_play_urlAttr($name)
    {
        $vide_prefix = Config::where('name', 'video_prefix')->value('value');
        if ($vide_prefix != '') {
            return $vide_prefix . substr($name, 24);
        }
        return $name;
    }
}
