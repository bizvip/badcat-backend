<?php


namespace app\api\controller;

// 社区模型
use app\admin\model\Video;
// 影视模型
use app\admin\model\Dvideo;
// 软件模型
use app\admin\model\software\Dsoftware;
// ASMR模型
use app\admin\model\Dasmr;
// 吃瓜模型
use app\admin\model\water\Qia;
use app\common\controller\Api;
use think\Request;
use app\common\model\Config;
use app\admin\model\UserWatchLog;


class Sreach extends Api
{
    protected $noNeedLogin = ['*'];

    // 社区搜索
    public function community(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＳＴＡ　ＧＥＴ');
        }
        $req = $request->get();
        $res = Video::with('publisher')->where('tong', 1)->where('title', 'like', '%' . $req['title'] . '%')->page($req['current'], $req['every'])->select();
        $res ?$res = $res->toArray():'';
      
        $this->success('社区搜索结果',$res,200);
    }
    
    // 吃瓜搜索
    public function watermelon(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＳＴＡ　ＧＥＴ');
        }
        $req = $request->get();
        $res = Qia::where('tong', 1)->where('title', 'like', '%' . $req['title'] . '%')->page($req['current'], $req['every'])->field('id,class,label,qia_image,avator_image,title,istoll,label,fabulous,stamp,browse,comment,status,tong,create_time')->select();
        $res ?$res = $res->toArray():'';
        
        // if (!empty($request->header('token'))) {
        //  $user = $this->auth->getUserinfo();
         
        // }
        
        $this->success('吃瓜搜索结果',$res,200);
    }
    
    // 影视搜索
    public function video(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＳＴＡ　ＧＥＴ');
        }
        $req = $request->get();
        // $res = Dvideo::with('labels')->where('title', 'like', '%' . $req['title'] . '%')->page($req['current'], $req['every'])->select();
        $res = Dvideo::where('vod_name', 'like', '%' . $req['title'] . '%')->page($req['current'], $req['every'])->field('id,vod_area,vod_belong,vod_browse,vod_class,vod_content,vod_create_time,vod_director,vod_hits,vod_istoll,vod_name,vod_pic,vod_year,vod_lang')->select();
        $res ?$res = $res->toArray():'';
        if (!empty($request->header('token'))) {
         $user = $this->auth->getUserinfo();
         
        }
          
        $this->success('搜索结果',$res,200);
    }
    
    // APP搜索
    public function software(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＳＴＡ　ＧＥＴ');
        }
        $req = $request->get();
        // 搜索包含演员列表
        // $res = Dsoftware::with('labels')->where('title', 'like', '%' . $req['title'] . '%')->page($req['current'], $req['every'])->select();
        $res = Dsoftware::with(['belong'])->where('title', 'like', '%' . $req['title'] . '%')->page($req['current'], $req['every'])->select();
        $res ?$res = $res->toArray():'';
        if (!empty($request->header('token'))) {
         $user = $this->auth->getUserinfo();
        
        }
        
        $this->success('搜索结果',$res,200);
    }
    
}
