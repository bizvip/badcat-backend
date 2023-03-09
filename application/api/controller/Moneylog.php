<?php


namespace app\api\controller;


use app\common\controller\Api;
use think\Request;
use app\admin\model\Moneylog as MoneylogModel;
use app\admin\model\User as Usermodel;

class Moneylog extends Api
{
    protected $noNeedLogin = ['*'];

    // 支付宝
    public function zhb(Request $request)
    {
        $user = $this->auth->getUser();
        $req = $request->post();
        $bang = new Bangs();
        if($bang->find($user->id)){
           $this->error('记录已存在，请勿重新申请');
        }
        $res = $bang->save(['userid'=>$user->id,'zfb'=>$req['zfb']]);
        if($res){
           $this->success('绑定成功等待审核','',200);
        }else{
            $this->error('网络错误','',100);
        }
    }
    
    // 转账
    public function zhuanzhang(Request $request)
    {
        $req = $request->post();
        
        $userid = $req['userid'];
        $username = $req['username'];
        $daili_username = $req['daili_username'];
        $yaoqingma = $req['yaoqingma'];
        $jine = $req['jine'];
        $daili_userid = 0;
        $daili_has_money = 0;
        
        $has_money = Usermodel::where('id', $userid)->value('money');
        if($has_money < $jine){
            $this->success('余额不足,请及时充值','',200);
            return;
        }
        
        $moneylogModel = new MoneylogModel();
        if($daili_username){
            $daili_userid = Usermodel::where('username', $daili_username)->value('id');
            $daili_has_money = Usermodel::where('username', $daili_username)->value('money');
        } else {
            $daili_userid = Usermodel::where('number', $yaoqingma)->value('id');
            $daili_has_money = Usermodel::where('number', $yaoqingma)->value('money');
        }
        
        if(!$daili_userid){
            $this->success('未找到此用户','',200);
            return;
        } else{
            if($daili_userid == $userid){
                $this->success('不能转给自己','',200);
                return;
            }
            
            $has_money = $has_money - $jine;
            Usermodel::where('id', $userid)->update(['money' => $has_money]);
            
            $daili_has_money = $daili_has_money + $jine;
            Usermodel::where('id', $daili_userid)->update(['money' => $daili_has_money]);
            //$this->success('转账成123功','',200);
            $res = $moneylogModel->save(['uid'=>$userid,'username'=>$username,'item'=>'代理转账','cid'=>$daili_userid,'c_username'=>$daili_username,'money'=>$jine,'ctime'=>time()]);
            if($res){
                $this->success('转账成功','',200);
            }else{
                $this->error('网络错误','',100);
            } 
        }
    }
}
