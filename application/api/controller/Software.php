<?php


namespace app\api\controller;
// 获取分类
use app\admin\model\software\Sub;
use app\admin\model\software\Subordinate;
// 点赞模型
use app\admin\model\software\Thumbs;
// 踩模型
use app\admin\model\software\Thumbsc;
use app\admin\model\software\Tsoftware as Tvs;
use app\common\controller\Api;
use think\Request;
use app\admin\model\User as UserModel;
use app\admin\model\UserWatchLog;
use app\common\model\Config;
use think\Log;
use app\admin\model\software\Dsoftware as Softwares;  // as Softwares APP第一个界面列表

class Software extends Api
{
	protected $noNeedLogin = ['*'];

	// 软件列表
	public function software_list(Request $request)
	{
		if (!$request->isPost()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
		}
		$req = $request->post();

		// $res = Softwares::with('labels')->where('class', $req['class'])->page($req['current'], $req['every'])->order('hits desc')->select();
		$res = Softwares::with(['subordinate', 'belong'])->where(['class' => 1])->page($req['current'], $req['every'])->order('update_time desc')->select();
		$res ? $res = $res->toArray() : '';

		if (!empty($request->header('token'))) {
			$user = $this->auth->getUserinfo();
			foreach ($res as &$value) {
				$find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $value['id']])->find();
				$findc = Thumbsc::where(['userid' => $user['id'], 'thumbscid' => $value['id']])->find();
				if ($find) {
					$value['give'] = 1;
				} else {
					$value['give'] = 0;
				}

				if ($findc) {
					$value['gcai'] = 2;
				} else {
					$value['gcai'] = 0;
				}
			}
		}

		$this->result('软件列表', $res, 200);
	}

	// 最新
	public function selected(Request $request)
	{
		$res = Sub::all()->toArray();
		$user = array();
		if (!empty($request->header('token'))) {
			$user = $this->auth->getUser();
		}

		foreach ($res as &$item) {
			//$item['softwares'] = Softwares::with(['subordinate', 'labels'])->orderRaw('rand()')->where('class', $item['id'])->limit('10')->select()->toArray();
			// $softwares=Softwares::with(['subordinate', 'labels'])->orderRaw('rand()')->where('class', $item['id'])->limit('10')->select()->toArray();                                   
			// limit('10')->显示10条数据
			// $res = Softwares::where('class', $req['class'])->page($item['current'], $item['every'])->order('hits desc')->select();
			$softwares = Softwares::with(['subordinate', 'belong'])->orderRaw('id desc')->where('class', $item['id'])->limit('10')->select()->toArray();
			// $softwares=Softwares::orderRaw('id desc')->where('class', $item['id'])->select()->toArray();
			$item['softwares'] = $softwares;
		}
		$this->result('最新发布', $res, 200);
	}

	// 软件分类
	public function classify(Request $request)
	{
		if (!$request->isGet()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
		}
		$res = Subordinate::all();
		$res ? $res = $res->toArray() : '';
		$this->result('分类', $res, 200);
	}

	// 软件详情
	public function xq_software(Request $request)
	{
		if (!$request->isPost()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
		}
		$req = $request->post();
		// $res = Softwares::with('labels')->where('apoccdio_software.id', $req['id'])->find();
		$res = Softwares::where('apoccdio_software.id', $req['id'])->find();
		if ($request->header('token') !== null) {
			$user = $this->auth->getUserinfo();
			$find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $res['id']])->find();
			$findc = Thumbsc::where(['userid' => $user['id'], 'thumbscid' => $res['id']])->find();

			if ($find) {
				$res['give'] = 1;
			} else {
				$res['give'] = 0;
			}

			if ($findc) {
				$res['gcai'] = 2;
			} else {
				$res['gcai'] = 0;
			}
		}
		$this->result('软件详情', $res, 200);
	}

	// 软件所属
	public function app_label()
	{
		$res = \app\admin\model\software\Belong::all()->toArray();
		$this->result('所属', $res, 200);
	}

	// 所属详情
	public function label_detail(Request $request)
	{
		$req = $this->request->post();
		$res = \app\admin\model\software\Belong::where('id', $req['id'])->find();
		// $software = \app\admin\model\software\Software::->where('belong', $req['id'])->page($req['current'], $req['every'])->order('id desc')->select()->toArray();
		$software = \app\admin\model\software\Software::with(['belong'])->where('belong', $req['id'])->page($req['current'], $req['every'])->order('id desc')->select()->toArray();
		$software ?? '';
		if (!empty($request->header('token'))) {
			$user = $this->auth->getUserinfo();
			foreach ($software as &$value) {
			}
		}
		foreach ($software as &$value) {
		}


		$res['software'] = $software;
		$res['count'] = \app\admin\model\software\Software::where('belong', $req['id'])->count();
		$this->result('所属详情', $res, 200);
	}

	//赞
	public function thumbs(Request $request)
	{
		if (!$request->isGet()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
		}
		$user = $this->auth->getUser();
		$req = $request->get();
		$find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $req['id']])->find();
		if ($find) {
			$this->result('已经赞过了哟~', '', 100);
		} else {
			$thumbs = new Thumbs();
			$res = $thumbs->save(['userid' => $user['id'], 'thumbsid' => $req['id']]);
		}
		if ($res) {
			$res = Softwares::where('id', $req['id'])->setInc('hits', 1);
			if ($res) {
				// 任务id3（点赞一个作品）
				$res = Task::upload($user->id, 3);
				$this->success('赞一个', '', 200);
			}
		} else {
			$this->error('系统错误或网络错误', '', 100);
		}
	}

	// 踩
	public function thumbsc(Request $request)
	{
		if (!$request->isGet()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
		}
		$user = $this->auth->getUser();
		$req = $request->get();
		$findc = Thumbsc::where(['userid' => $user['id'], 'thumbscid' => $req['id']])->find();
		if ($findc) {
			$this->result('已经踩过了呀！', '', 100);
		} else {
			$thumbsc = new Thumbsc();
			$res = $thumbsc->save(['userid' => $user['id'], 'thumbscid' => $req['id']]);
		}
		if ($res) {
			$res = Softwares::where('id', $req['id'])->setInc('cai', 1);
			if ($res) {
				// 任务id4（踩一个作品）
				$res = Task::upload($user->id, 4);
				$this->success('踩一个', '', 200);
			}
		} else {
			$this->error('系统错误或网络错误', '', 100);
		}
	}

	// 增加浏览次数
	public function click_ll()
	{
		$req = $this->request->post();
		$res = \app\admin\model\software\Software::where('id', $req['id'])->setInc('browse', 1);  //每次点击增加多个少浏览次数
		if ($res) {
			$this->success('ok', '', 200);
		} else {
			$this->error('error', '', 100);
		}
	}

	// 猜你喜欢
	public function like(Request $request)
	{
		if (!$request->isPost()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
		}
		$req = $request->post();
		$res = Softwares::where('class', $req['class'])->orderRaw('rand()')->limit($req['limit'])->select();
		$res ? $res = $res->toArray() : '';
		$this->result('猜你喜欢', $res, 200);
	}
}
