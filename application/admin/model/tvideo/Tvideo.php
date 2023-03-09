<?php

namespace app\admin\model\tvideo;

use think\Model;


class Tvideo extends Model
{


    // 表名
    protected $name = 'short_video';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        'video_nurl',
    ];
    
    // 视频流链接前缀加域名
    public function getVideoNurlAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['video_url'])) {
            return config('host') . $data['video_url'];
        } else {
            return $data['video_url'];
        }
    }
}
