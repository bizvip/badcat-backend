<?php


namespace app\api\controller;

// 获取分类
use app\admin\model\asmr\Sub;
use app\admin\model\asmr\Subordinate;
use app\admin\model\Thumbs;
use app\admin\model\asmr\Tasmr as Tvs;
use app\common\controller\Api;
use think\Request;
use app\admin\model\User as UserModel;
use app\admin\model\UserWatchLog;
use app\common\model\Config;
use think\Log;
use app\admin\model\asmr\Dasmr as Asmrs;  // as Asmrs APP第一个界面列表

class Asmr extends Api
{
	protected $noNeedLogin = ['*'];

	//ASMR列表
	public function asmr_list(Request $request)
	{
		if (!$request->isPost()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
		}
		$req = $request->post();

		$res = Asmrs::where('class', $req['class'])->page($req['current'], $req['every'])->order('id desc')->select();

		$res ? $res = $res->toArray() : '';

		if (!empty($request->header('token'))) {

			$user = $this->auth->getUserinfo();

			foreach ($res as &$value) {
				$find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $value['id'], 'class' => 1])->find();

				if ($find) {
					$value['give'] = 1;
				} else {
					$value['give'] = 0;
				}
			}
		}

		$this->result('分类', $res, 200);
	}

	//ASMR分类
	public function classify(Request $request)
	{
		if (!$request->isGet()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
		}
		$res = Subordinate::all();
		$res ? $res = $res->toArray() : '';
		$this->result('分类', $res, 200);
	}

	//ASMR详情
	public function xq_asmr(Request $request)
	{
		if (!$request->isPost()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
		}
		$req = $request->post();
		$res = Asmrs::where('bc_asmr.id', $req['id'])->find();
		if ($request->header('token') !== null) {
			$user = $this->auth->getUserinfo();
			$find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $res['id'], 'class' => 1])->find();
			if ($find) {
				$res['give'] = 1;
			} else {
				$res['give'] = 0;
			}
		}
		$this->result('ASMR详情', $res, 200);
	}
}
