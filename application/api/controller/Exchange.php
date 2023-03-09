<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Request;
use think\Db;

class Exchange extends Api
{
  protected $noNeedLogin = ['*'];
  
  // 使用
  public function add(Request $request)
  {
    if (!$request->isPost()) {
      $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
    }
    $exchange = new \app\admin\model\Exchange();
    $user1 = new \app\admin\model\User();
    $req = $request->post();
    $user = $this->auth->getUserinfo();

    $res = $this->exchangePost($req['code'], $user['id']);

    if ($res == 1) {
      $this->diankaPost($req['code'], $user['id']);
    }
  }

  public function exchangePost($code, $userid)
  {
    $user1 = new \app\admin\model\User();
    $exchange = new \app\admin\model\Exchange();
    $find = $exchange->where('code', $code)->find();
    if (!$find) {
      return 1;
    }
    if ($find['list'] == 1) {
      $this->error('激活码已失效', '', 100);
    }
    $res = $exchange->where('code', $code)->update(['list' => 1, 'userid' => $userid, 'use_time' => time()]);
    if ($res) {

      $user = $user1->where(['id' => $userid])->find();
      if ($find['class'] == 0) {
        //日会员卡
        $time = 1;
      }
      if ($find['class'] == 1) {
        //周会员卡
        $time = 7;
      }
      if ($find['class'] == 2) {
        //月会员卡
        $time = 30;
      }
      if ($find['class'] == 3) {
        //季会员卡
        $time = 90;
      }
      if ($find['class'] == 4) {
        //半年会员卡
        $time = 180;
      }
      if ($find['class'] == 5) {
        //一年会员卡
        $time = 365;
      }
      if ($find['class'] == 6) {
        //永久会员卡
        $time = 99999;
      }

      if ($user['vip_time'] > date('Y-m-d H:i:s')) {
        //没到期时间
        $time = strtotime($user['vip_time']) + ($time * 1 * 60 * 60 * 24);
      } else {
        //到期时间
        $time = ($time * 1 * 60 * 60 * 24) + time();
      }

      $res = $user1->where('id', $userid)->update(['vip_time' => date('Y-m-d H:i:s', $time)]);
      if ($res) {
        $this->success('您已成功兑换VIP！', '', 200);
      } else {
        $this->success('失败', '', 100);
      }
    }
  }

  public function diankaPost($code, $userid)
  {
    $user1 = new \app\admin\model\User();
    $dianka = new \app\admin\model\Dianka();
    $find = $dianka->where('dianka', $code)->find();
    if (!$find) {
      $this->error('激活码不存在', '', 100);
    }
    if ($find['yid'] > 0) {
      $this->error('激活码已失效', '', 100);
    }
    $res = $dianka->where('id', $find['id'])->update(['yid' => $userid, 'stime' => time()]);
    if ($res) {
      $user = $user1->where(['id' => $userid])->find();
      $time = 0;
      if ($user['vip_time'] > date('Y-m-d H:i:s')) {
        //没到期时间
        $time = strtotime($user['vip_time']) + $find['time'];
      } else {
        //到期时间
        $time = $find['time'] + time();
      }
      $result = $user1->where('id', $userid)->update(['vip_time' => date('Y-m-d H:i:s', $time)]);
      if ($result) {
        $this->success('兑换成功', '', 200);
      } else {
        $this->success('失败', '', 100);
      }
    }
  }
}
