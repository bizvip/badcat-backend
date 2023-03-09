<?php


namespace app\api\controller;


use app\admin\model\Ext;
use app\common\controller\Api;
use app\common\model\Config;
use think\Request;

class Notify extends Api
{
    protected $noNeedLogin = ['*'];

    //会员卡回调
    public function card(Request $request)
    {
        $req = $request->post();
        $find = \app\admin\model\Order::where('code', $_REQUEST['param'])->find();//订单
        $user = \app\admin\model\User::where('id', $find['userid'])->find();//用户
        $card = \app\admin\model\Card::where('id', $find['cardid'])->find();//会员卡
        $arr = Config::where('name', 'like', '%' . 'integral' . '%')->column('value');

        //订单状态改变
        $res = \app\admin\model\Order::where('code', $_REQUEST['param'])->update(['list' => 1, 'pay_time' => date('Y-m-d H:i:s')]);
        if ($res) {
            //上级代理返利
            $ext = Ext::where('user_id', $user['id'])->find();//推广
            if ($ext) {
                $t_user = \app\admin\model\User::where('id', $ext['userid'])->find();
                if ($t_user['agent'] == 1) {
                    //代理
                    $money = 0;
                    if ($t_user['integral'] < $arr[1] && $t_user['integral'] >= $arr[0]) {
                        //等级1
                        $money = $find['price'] * 0.15;
                    }
                    if ($t_user['integral'] < $arr[2] && $t_user['integral'] >= $arr[1]) {
                        //等级二
                        $money = $find['price'] * 0.25;

                    }
                    if ($t_user['integral'] >= $arr[2]) {
                        //等级三
                        $money = $find['price'] * 0.45;
                    }
                } else {
                    //非代理
                }
                \app\admin\model\User::where('id', $ext['userid'])->setInc('money', $money);
                Ext::where('user_id', $user['id'])->setInc('money', $money);//推广

            }
            //会员时间
            if ($user['vip_time'] > date('Y-m-d H:i:s')) {
                //没到期时间
                $time = strtotime($user['vip_time']) + ($card['time'] * 1 * 60 * 60 * 24);
            } else {
                //到期时间
                $time = ($card['time'] * 1 * 60 * 60 * 24) + time();
            }
            \app\admin\model\User::where('id', $find['userid'])->update(['vip_time' => date('Y-m-d H:i:s', $time)]);
        }
        //$this->success('回调成功', '', 200);
		echo('success');
    }

    public function agent(Request $request)
    {
        $req = $request->post();
        $find = \app\admin\model\Order::where('code', $req['order_id'])->find();//订单
        $user = \app\admin\model\User::where('id', $find['userid'])->find();//用户
        $arr = Config::where('name', 'like', '%' . 'integral' . '%')->column('value');

        //订单状态改变
        $res = \app\admin\model\Order::where('code', $req['order_id'])->update(['list' => 1, 'pay_time' => date('Y-m-d H:i:s')]);
        if ($res) {
            //上级代理返利
            $ext = Ext::where('user_id', $user['id'])->find();//推广
            if ($ext) {
                $t_user = \app\admin\model\User::where('id', $ext['userid'])->find();
                if ($t_user['agent'] == 1) {
                    //代理
                    $money = 0;
                    if ($t_user['integral'] < $arr[1] && $t_user['integral'] >= $arr[0]) {
                        //等级1
                        $money = $find['price'] * 0.15;
                    }
                    if ($t_user['integral'] < $arr[2] && $t_user['integral'] >= $arr[1]) {
                        //等级二
                        $money = $find['price'] * 0.25;

                    }
                    if ($t_user['integral'] >= $arr[2]) {
                        //等级三
                        $money = $find['price'] * 0.45;
                    }
                } else {
                    //非代理
                }
                \app\admin\model\User::where('id', $ext['userid'])->setInc('money', $money);
                Ext::where('user_id', $user['id'])->setInc('money', $money);//推广
            }
            \app\admin\model\User::where('id', $find['userid'])->update(['agent' => 1]);

        }
        $this->success('回调成功', '', 200);
    }

    public function recharge(Request $request)
    {
        $req = $request->post();
        $find = \app\admin\model\Order::where('code', $_REQUEST['param'])->find();//订单
        $list = \app\admin\model\Paylist::where('cardid', $find['cardid'])->value('c_price');
        $update = \app\admin\model\User::where('id', $find['userid'])->setInc('money', $list);
        $res = \app\admin\model\Order::where('code', $_REQUEST['param'])->update(['list' => 1, 'pay_time' => date('Y-m-d H:i:s')]);
        //$this->success('回调成功', '', 200);
		echo "success";
    }
}
