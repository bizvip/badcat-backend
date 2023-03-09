<?php


namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Config;
use think\Request;
use app\admin\model\Withdrawal as Withs;

class Withdrawal extends Api
{
    protected $noNeedLogin = ['*'];

    public function add_drawal(Request $request)
    {
        if ( ! $request->isPost()) {
            $this->error('请求方式不正确');
        }
        $user = $this->auth->getUser();
        $find = \app\admin\model\Bang::where(['class' => 1, 'fl' => 0, 'userid' => $user->id])->find();
        $find1 = \app\admin\model\Bang::where(['class' => 1, 'fl' => 1, 'userid' => $user->id])->find();
        if ( ! $find && ! $find1) {
            $this->error('您未绑定支付宝或银行卡，或未通过审核', '', 100);
        }
        $req = $request->post();
        $withs = new Withs();
        $res = $withs->allowField(true)->save(['userId' => $user->id, 'money' => $req['money']]);
        if ($res) {
            \app\admin\model\User::where('id', $user->id)->setDec('money', $req['money']);
            $this->success('审核成功，等待审核', '', 200);

        }

    }

    //
    public function http_down($url = '图片路径', $timeout = 60)
    {
        $long = '{"code": "200",
	    "data": [{
		"cid": 16,
		"cname": "国产",
		"fl": 0,
		"sort": 2,
		"video": [{
			"vid": 4109,
			"vname": "这样的口活你能坚持2分钟？",
			"vphoto": "https:\/\/saohu38.com\/upload\/2019-09-28\/3afda038479f14b56ca7c18c2fb1e455\/cover\/cover.jpg",
			"vtime": "",
			"vap": null,
			"vau": null,
			"vurl": "https:\/\/play.sugar-z.com\/upload\/2019-09-28\/3afda038479f14b56ca7c18c2fb1e455\/m3u8\/index.m3u8",
			"aid": 1,
			"cid": 16,
			"clicks": 31.4,
			"evaluate": 13423,
			"profit": null,
			"class": 1,
			"create_time": "1589078526",
			"state": 0
		}]
	}]}';
        halt(json_decode($long,true));
        $filename = ROOT_PATH . 'public' . DS . 'url' . DS . time() . '.jpg';
        echo $path = dirname($filename);
        if ( ! is_dir($path) && ! mkdir($path, 0755, true)) {
            return false;
        }
        $fp = fopen($filename, 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $filename;
    }

}
