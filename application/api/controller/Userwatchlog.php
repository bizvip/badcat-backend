<?php


namespace app\api\controller;


use app\common\controller\Api;
use think\Request;
use app\admin\model\UserWatchLog as Uwl;

use app\admin\model\Video as Vs;

use app\admin\model\Dvideo as Dvs;

use app\admin\model\Tvideo as Tvs;

class UserWatchLog extends Api
{
    protected $noNeedLogin = ['*'];

     public function get_list(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $user = $this->auth->getUser();
        $req = $request->get();
        $page=$req['page'];
        $limit=10;
        $start=($page-1)*$limit;
        $res = Uwl::where(['user_id' => $user['id']])->order('create_time desc,endtime desc')->limit($start,$limit)->select();
        //$res = Uwl::where(['user_id' => 176])->order('create_time desc,endtime desc')->limit($start,$limit)->select();
        $time=time();
        $list=array();
        foreach($res as $k=>$v){
            $list[$k]['id']=$v['id'];
            $list[$k]['class_id']=$v['class_id'];
            $list[$k]['total']=$v['total'];
            $list[$k]['day']=$v['day'];
            $list[$k]['create_time']=date("Y-m-d H:i",$v['create_time']);
            $list[$k]['endtime']=date("Y-m-d H:i",$v['endtime']);
            if($v['endtime']>$time){
                $list[$k]['status']='使用中';
            }else{
                 $list[$k]['status']='已过期';
            }
            
            
            if($v['class_id']==1){
                $video=Dvs::where(['id'=>$v['video_id']])->find();
                $list[$k]['video_id']=$video['id'];
                 $list[$k]['video_type']='长视频';
                $list[$k]['video_title']=$video['title'];
                $list[$k]['video_image']=$video['video_image'];
               
            }else{
                if($v['class_id']==2){
                    $video=Tvs::where(['id'=>$v['video_id']])->find();
                    $list[$k]['video_id']=$video['id'];
                     $list[$k]['video_type']='短视频';
                    $list[$k]['video_title']=$video['title'];
                    $list[$k]['video_image']=$video['video_image'];
                   
                }else{
                    $video=Vs::where(['id'=>$v['video_id']])->find();
                    $list[$k]['video_id']=$video['id'];
                     $list[$k]['video_type']='社区视频';
                    $list[$k]['video_title']=$video['title'];
                    $list[$k]['video_image']=$video['video_image'];
                    
                    
                }
            }

            
        }
        
        $this->result('购买列表', $list, 200);
    }

   
   
    
    
}
