<?php

namespace app\admin\model;

use app\common\model\Config;
use think\Model;


class Video extends Model
{


    // 表名
    protected $name = 'community';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;
    // protected $autoWriteTimestamp = 'datetime';
    // protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';

    // 追加属性
    protected $append = [
        // 图片
        'images_text',
        // 视频预览图
        'video_text',
        // 'app_text',
        // 'fl_text',
        // 发布者头像
        // 'avatar_text',
        // 'ask_texts'
    ];

    // 标签
    public function labels()
    {
        return $this->belongsTo('label', 'label', 'id', '', 'left')->setEagerlyType(0);
    }

    // 视频封面图
    public function getVideoTextAttr($video, $data)
    {
        if ( ! preg_match('/^http/', $data['video_image'])) {
            return config('host') .$data['video_image'];
        } else {
            return $data['video_image'];
        }
        /*
        if ( ! preg_match('/^http/', $data['video_image'])) {
            return config('host') . $data['video_image'];
        }
        */
    }

    // public function getAppTextAttr($video, $data)
    // {
    //     if ( ! preg_match('/^http/', $data['app_image'])) {
    //         return config('host') .$data['app_image'];
    //     } else {
    //         return $data['app_image'];
    //     }
    //     /*
    //     if ( ! preg_match('/^http/', $data['app_image'])) {
    //         return config('host') . $data['app_image'];
    //     }
    //     */
    // }

    // public function getFlTextAttr($video, $data)
    // {
    //     if ( ! preg_match('/^http/', $data['fh_image'])) {
    //         return config('host') .$data['fh_image'];
    //     } else {
    //         return $data['fh_image'];
    //     }
    //     /*
    //     if ( ! preg_match('/^http/', $data['fh_image'])) {
    //         return config('host') . $data['fh_image'];
    //     }
    //     */
    // }

    public function getFhImageAttr($value)
    {
        if ( ! preg_match('/^http/', $value)) {
            return config('host') .$value;
        } else {
            return $value;
        }
        //return config('host') . $value;

    }
    
    // 图片
    public function getImagesTextAttr($value, $data)
    {
        $arr = explode(',', $data['images']);
        foreach ($arr as &$item) {
            if ( ! preg_match('/^http/', $item)) {
                $item = config('host') . $item;
            }
        }
        return $arr;
    }
    
    // // 发布者头像
    // public function getAvatarTextAttr($value, $data)
    // {
    //     if ( ! preg_match('/^http/', $data['avator_image'])) {
    //         return config('host') . $data['avator_image'];
    //     } else {
    //         return $data['avator_image'];
    //     }
    // }
    
    // 发布者头像
    public function getAvatorImageAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['avator_image'])) {
            return config('host') . $data['avator_image'];
        } else {
            return $data['avator_image'];
        }
    }

    // public function getAskTextsAttr($value, $data)
    // {
    //     if ( ! preg_match('/^http/', $data['ask_image'])) {
    //         return config('host') . $data['ask_image'];
    //     } else {
    //         return $data['ask_image'];
    //     }
    // }
    
    // 发布者个人信息（后台）
    public function publisher()
    {
        return $this->belongsTo('Publisher', 'user_id', 'id', [], 'Left');
    }
    
    // 发布者个人信息（APP）
    // 关联用户信息
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
