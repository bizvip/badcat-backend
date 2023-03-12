<?php


namespace app\api\controller;

use app\admin\model\Sub;
use app\admin\model\Subordinate;
use app\admin\model\video\Thumbs;
use app\common\controller\Api;
use think\Request;
use app\admin\model\User as UserModel;
use app\admin\model\UserWatchLog;
use app\common\model\Config;
use think\Log;
use app\admin\model\Dvideo as Videos;

class Dvideo extends Api
{
	protected $noNeedLogin = ['*'];
	
	// 最新
	public function selected(Request $request)
	{
		$res = Sub::all()->toArray();
		$user = array();
		if (!empty($request->header('token'))) {
			$user = $this->auth->getUser();
		}

		foreach ($res as &$item) {
			// 按随机排列
			// $videos=Videos::with(['subordinate', 'labels'])->orderRaw('rand()')->where('class', $item['id'])->limit('10')->select()->toArray();
			// 按ID降序排列
			//$videos = Videos::with(['subordinate'])->orderRaw('id desc')->where('vod_class', $item['id'])->limit('10')->field('id,vod_area,vod_belong,vod_browse,vod_class,vod_content,vod_create_time,vod_director,vod_hits,vod_name,vod_pic,vod_year')->select()->toArray();
			$videos = (new \app\admin\model\Dvideo())->field('id,vod_area,vod_belong,vod_browse,vod_class,vod_content,vod_create_time,vod_director,vod_hits,vod_istoll,vod_name,vod_pic,vod_year,vod_lang')->with(['subordinate'])->where('vod_class', $item['id'])->limit('10')->orderRaw('id desc')->select()->toArray();
			
			$item['videos'] = $videos;
		}
		$this->result('精选影视', $res, 200);
	}

	// 影视列表
	public function video_list(Request $request)
	{
		if (!$request->isPost()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
		}
		$req = $request->post();

		$res = Videos::where('vod_class', $req['class'])->page($req['current'], $req['every'])->order('id desc')->field('id,vod_area,vod_belong,vod_browse,vod_class,vod_content,vod_create_time,vod_director,vod_hits,vod_istoll,vod_name,vod_pic,vod_year,vod_lang')->select();

		$res ? $res = $res->toArray() : '';

		if (!empty($request->header('token'))) {

			$user = $this->auth->getUserinfo();

			foreach ($res as &$value) {
				$find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $value['id']])->find();

				if ($find) {
					$value['give'] = 1;
				} else {
					$value['give'] = 0;
				}
			}
		}

		$this->result('分类', $res, 200);
	}

	// 影视分类
	public function fl(Request $request)
	{
		if (!$request->isGet()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
		}
		$res = Subordinate::all();
		$res ? $res = $res->toArray() : '';
		$this->result('分类', $res, 200);
	}

	// 影视详情
	public function xq_video(Request $request)
	{
		if (!$request->isPost()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
		}
		$req = $request->post();
		$res = Videos::with(['subordinate'])->where('bc_video.id', $req['id'])->find();
		if ($request->header('token') !== null) {
			$user = $this->auth->getUserinfo();
			$find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $res['id']])->find();

			if ($find) {
				$res['give'] = 1;
			} else {
				$res['give'] = 0;
			}
		}
		$this->result('影视详情', $res, 200);
	}

	// 猜你喜欢
	public function like(Request $request)
	{
		if (!$request->isPost()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
		}
		$req = $request->post();
		$res = Videos::where('vod_class', $req['class'])->orderRaw('rand()')->limit($req['limit'])->select();
		$res ? $res = $res->toArray() : '';
		$this->result('猜你喜欢', $res, 200);
	}

	// 点赞
	public function thumbs(Request $request)
	{
		if (!$request->isGet()) {
			$this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
		}
		$user = $this->auth->getUser();
		$req = $request->get();
		$find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $req['id']])->find();
		if ($find) {
			$this->result('您已经点过赞了', '', 100);
		} else {
			$thumbs = new Thumbs();
			$res = $thumbs->save(['userid' => $user['id'], 'thumbsid' => $req['id']]);
		}
		if ($res) {
			$res = Videos::where('id', $req['id'])->setInc('vod_hits', 1);
			if ($res) {
				// 任务id3（点赞一个作品）
				$res = Task::upload($user->id, 3);
				$this->success('点赞成功', '', 200);
			}
		} else {
			$this->error('系统错误或网络错误', '', 100);
		}
	}

}
