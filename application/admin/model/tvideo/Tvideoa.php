<?php

namespace app\admin\model\tvideo;

use think\Model;


class Tvideoa extends Model
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
        // 封面
        'video_image',
        //  视频流
        'video_url',
    ];
    
    public function getAvatorImageAttr($value)
    {
        if (preg_match('/^http/', $value)) {
            return $value;
        } else {
            return config('host') . $value;

        }
    }
    
    // 视频id转为字符串格式
    public function getidAttr($value){
      $id = md5(strval($value));
      return $id;
    //   return json_decode($id);
    // $id =md5($value);
    }
    
    // 输入副ID（sid）为主ID
    public function getsidAttr($value,$data)
    {
      return $data['id'];
    }
    
    // 是否显示封面
    public function getisShowimageAttr($value){
    $isShowimage = ''.$value.'';
        return json_decode($isShowimage);
    }
    
    // 是否显示进度条
    public function getisShowProgressBarTimeAttr($value){
    //   $isShowProgressBarTime = '"'.$value.'"';  
    $isShowProgressBarTime = ''.$value.'';
        return json_decode($isShowProgressBarTime);
    }
    
    // 播放
    public function getplayIngAttr($value){
    $playIng = ''.$value.'';
        return json_decode($playIng);
    }
    
    // 是否播放音频
    public function getisplayAttr($value){
    $isplay = ''.$value.'';
        return json_decode($isplay);
    }
    
    // 封面
    public function getVideoImageAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['video_image'])) {
            return config('host') . $data['video_image'];
        } else {
            return $data['video_image'];
        }
    }
   
    // 视频流
    public function getVideoUrlAttr($value, $data)
    {
        if ( ! preg_match('/^http/', $data['video_url'])) {
            return config('host') . $data['video_url'];
        } else {
            return $data['video_url'];
        }
    }
    
    // 发布者个人信息（后台）
    public function publisher()
    {
        return $this->belongsTo('\app\admin\model\Publisher', 'user_id', 'id', [], 'Left');
    }
    
    // 发布者个人信息（APP）
    // 关联用户信息
    public function user()
    {
        return $this->belongsTo('\app\admin\model\User', 'user_id', 'id', [], 'Left');
    }
}
