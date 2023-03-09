<?php

namespace app\admin\model\software;

use app\common\model\Config;
use think\Model;


class Software extends Model
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
    ];

    // 相关分类
    public function subordinate()
    {
        return $this->belongsTo('Subordinate', 'class', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    //封面图（前缀加上域名）
    public function getAppImageAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['app_image'])) {
            return config('host') . $data['app_image'];
        } else {
            return $data['app_image'];
        }
    }
    
    // 相关所属
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
