<?php


namespace app\api\controller;

use app\admin\model\Encounter;
use app\common\controller\Api;
use think\Request;

class Feedback extends Api
{
    protected $noNeedLogin = ['*'];
    
    // 问题列表
    public function doubt_list()
    {
        $res = Encounter::select()->toArray();
        $this->result('ok', $res, 200);
    }
    
    // 提交反馈
    public function add(Request $request)
    {
        if ( ! $request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        $user = $this->auth->getUserinfo();
        // 用户id
        $req['userid'] = $user['id'];
        // 提交时间
        $req['create_time'] = date('Y-m-d H:i:s');
        $feedback = new \app\admin\model\Feedback();
        $res = $feedback->save($req);
        if ($res) {
            $this->success('ok', '', 200);
        } else {
            $this->success('error', '', 100);
        }
    }
}
