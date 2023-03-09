<?php


namespace app\api\controller;


use app\common\controller\Api;
use think\Request;

use app\admin\model\App;
use app\admin\model\Other;
use app\admin\model\Rotation;
use app\admin\model\Moneylog as MoneylogModel;

use think\Db;


class Card extends Api
{
  protected $noNeedLogin = ['*'];

  protected function _initialize()
  {
    $this->model = new \app\admin\model\card\Equity();
    $this->model = new \app\admin\model\card\Card();
    $this->dailimodel = new \app\admin\model\Dailicard();
    $this->user = new \app\admin\model\User();
  }

  // 会员卡列表
  public function privilege(Request $request)
  {

    if (!$this->request->isPost()) {
      $this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
    };

    $res = \app\admin\model\card\Equity::select();

    $res = collection($res)->toArray();
    $this->success('ok', $res, 200);
  }

  // 会员卡列表
  public function index(Request $request)
  {

    if (!$this->request->isPost()) {
      $this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
    };

    //$res = $this->model->select();
    // $res = \app\admin\model\card\Card::where('switch', 1)->select();
    $res = \app\admin\model\card\Card::select();


    /*重新定义会员卡内容 libra*/
    $parentid = intval($request->post('parentid'));

    if ($parentid > 0) {
      foreach ($res as $k => $v) {
        $parent = $this->user::Where(['id' => $parentid])->find();
        $dailiCard = $this->dailimodel::Where(['daili_user_id' => $parentid, 'card_id' => $v['id']])->find();
        if ($dailiCard) {
          $res[$k]['money'] = $dailiCard['money'];
          $res[$k]['y_money'] = $dailiCard['y_money'];
          $res[$k]['url'] = $dailiCard['url'];
          $res[$k]['weichat'] = $parent['weichat'];
        }
      }
    }

    $res = collection($res)->toArray();
    $this->success('ok', $res, 200);
  }

}
