<?php


namespace app\api\controller;


use app\common\controller\Api;
use think\Request;
use app\admin\model\Bang as Bangs;

class Bang extends Api
{
    protected $noNeedLogin = ['*'];

    // 提交绑定 支付宝
    public function zhb(Request $request)
    {
        $user = $this->auth->getUser();
        $req = $request->post();
        $bang = new Bangs();
        if($bang->find($user->id)){
           $this->error('记录已存在，请勿重复申请！');
        }
        $res = $bang->save(['userid'=>$user->id,'zfb'=>$req['zfb'],'create_time'=>date('Y-m-d H:i:s')]);
        if($res){
           $this->success('提交成功，等待审核！','',200);
        }else{
            $this->error('网络错误','',100);
        }
    }
    
    // 提交绑定 英航卡
    public function bank(Request $request)
    {
        $user = $this->auth->getUser();
        $req = $request->post();
        $bang = new Bangs();
        $res = $bang->save(['userid'=>$user->id,'bandcard'=>$req['bandcard'],'khh'=>$req['khh'],'name'=>$req['name'],'fl'=>1,'create_time'=>date('Y-m-d H:i:s')]);
        if($res){
            $this->success('提交成功，等待审核！','',200);
        }else{
            $this->error('网络错误','',100);
        }
    }
}
