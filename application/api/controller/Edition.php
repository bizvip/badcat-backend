<?php


namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Config;

class Edition extends Api
{
    protected $noNeedLogin = ['*'];

    //版本号
    public function index()
    {
        static $allow_origin = ['http://damaotou.xiaohongshu.mom','http://damaotou.xiaohongshu.mom'];
        if(isset($_SERVER['HTTP_ORIGIN'])){
            $domain = $_SERVER['HTTP_ORIGIN'];
            if(in_array($domain,$allow_origin)){
                header('Access-Control-Allow-Origin:'.$domain);
            }
        }
        $res = \app\admin\model\Edition::order('version desc')->find();
        $res['website'] = Config::where('name', 'website')->value('value');
        $this->result('版本号', $res, 200);
    }

}
