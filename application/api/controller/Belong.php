<?php


namespace app\api\controller;
use app\common\controller\Api;
use think\Request;
use app\common\model\Config;
use app\admin\model\UserWatchLog;

class Belong extends Api
{
	protected $noNeedLogin = ['*'];

	//所属专题
	public function index()
	{
		$res = \app\admin\model\Belong::all()->toArray();
		$this->result('所属', $res, 200);
	}

	public function detail(Request $request)
	{
		$req = $this->request->post();
		$res = \app\admin\model\Belong::where('id', $req['id'])->find();
		// 随街排列
		// $video = \app\admin\model\Dvideo::where('belong', $req['id'])->page($req['current'], $req['every'])->select()->toArray();
		// 按ID降序排列
		$video = \app\admin\model\Dvideo::where('vod_belong', $req['id'])->page($req['current'], $req['every'])->order('id desc')->field('id,vod_area,vod_belong,vod_browse,vod_class,vod_content,vod_create_time,vod_director,vod_hits,vod_istoll,vod_name,vod_pic,vod_year,vod_lang')->select()->toArray();
		$video ?? '';
		$res['video'] = $video;
		$res['count'] = \app\admin\model\Dvideo::where('vod_belong', $req['id'])->count();
		$this->result('所属详情', $res, 200);
	}
}
