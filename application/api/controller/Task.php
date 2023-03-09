<?php


namespace app\api\controller;


use app\common\controller\Api;
use app\admin\model\Task as Tasks;
use app\common\model\Config;
use think\Request;
use app\admin\model\Complete;

class Task extends Api
{
  protected $noNeedLogin = ['*'];

  // 任务列表
  public function index(Request $request)
  {
    if (!$request->isPost()) {
      $this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
    }
    $user = $this->auth->getUser();
    $req = $request->post();
    $res = Tasks::all()->toArray();
    foreach ($res as $key => &$value) {
      $find = Complete::where(['userid' => $user->id, 'tid' => $value['id']])->find();
      if ($find) {
        $value['tong'] = 1;
      } else {
        $value['tong'] = 0;
      }
    }
    $this->success('任务列表', $res, 200);
  }

  // 任务完成
  public static function upload($userid, $id)
  {
    $find = Tasks::find($id);

    // 若任务为0则不可重复写入列表
    if ($find['repeatable'] == 0) {
      $output = Complete::where(['userid' => $userid, 'tid' => $id])->find();
      // 任务已经完成，不写入  
      if ($output) {
        return false;
      } else {
        // 任务未完成 只执行一遍 并获得对应积分
        $integral = \app\admin\model\User::where('id', $userid)->setInc('integral', $find['agent']);
        if ($integral) {
          $com = new Complete();
          $com->save(['userid' => $userid, 'tid' => $id, 'create_time' => date('Y-m-d H:i:s')]);
          return true;
        }
      }
    } else {
      // 不论任务完成与否 多次重复执行 并获得对应待审核积分
      $integral = \app\admin\model\User::where('id', $userid)->setInc('integral_examine', $find['agent']);
      if ($integral) {
        $com = new Complete();
        // 用户ID 任务ID 写入完成时间
        $com->save(['userid' => $userid, 'tid' => $id, 'create_time' => date('Y-m-d H:i:s')]);
        return true;
      }
    }

    // 获得VIP天数无或等于0
    if ($find['vip'] != 0) {
      $time = $find['vip'] * (1 * 60 * 60 * 24);
      $find = \app\admin\model\User::where('id', $userid)->find();
      $addtime = strtotime($find['vip_time']);
      if (!$find['vip_time'] = '') {
        $shijian = $time + $addtime;
      } else {
        $shijian = $time;
      }
      $shijian = date('Y-m-d H:i:s', $shijian);
      \app\admin\model\User::where('id', $userid)->update(['vip_time' => $shijian]);
    } else {
      $time = $find['vip'] * (1 * 60 * 60 * 24);
      $find = \app\admin\model\User::where('id', $userid)->find();
      $addtime = strtotime($find['vip_time']);
      if (!$find['vip_time'] = '') {
        $shijian = $time + $addtime;
      } else {
        $shijian = $time;
      }
      $shijian = date('Y-m-d H:i:s', $shijian);
      \app\admin\model\User::where('id', $userid)->update(['vip_time' => $shijian]);
    }
  }

  // 分享作品
//   public function share()
//   {
//     $user = $this->auth->getUser();
//     if ($user) {
//       // 任务id1（推广送VIP）
//       $res = $this->upload($user->id, 1);
//     } else {
//       $res = false;
//     }
//     if ($res) {
//       $this->success('ok', '', 200);
//     } else {
//       $this->error('errer', '', 100);
//     }
//   }

}
