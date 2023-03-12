<?php


namespace app\api\controller;


use app\admin\model\App;
use app\admin\model\Other;
// use app\admin\model\Adv;
use app\common\controller\Api;
use think\Request;
use think\Db;

class Adv extends Api
{
    protected $noNeedLogin = ['*'];

    //轮播
    public function adv(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ', '', 100);
        }
        $req = $request->get();
        // $res = Rotation::where('class', $req['class'])->select();
        $res =  \app\admin\model\Adv::where('class', $req['class'])->orderRaw('id desc')->select();
        $res ? $res = $res->toArray() : '';
        $this->success('ok', $res, 200);
    }

    //应用
    public function app(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ', '', 100);
        }
        $req = $request->get();
        $res = App::orderRaw('rand()')->limit($req['limit'])->select();
        $res ? $res = $res->toArray() : '';
        $this->success('ok', $res, 200);
    }

    //其他广告
    public function others(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ', '', 100);
        }
        $req = $request->get();
        // APP启动广告 | 压屏A | 压屏B
        if ($req['class'] == 2 || $req['class'] == 1 || $req['class'] == 4) {
            $res = Other::where('class', $req['class'])->select();
        } else {
            $res = Other::where('class', $req['class'])->orderRaw('rand()')->find();
        }
        $this->success('ok', $res, 200);

    }

    //点击广告
    public function click()
    {
        $user = $this->auth->getUser();
        if ($user) {
            // 任务id10：点击广告
            // $res = Task::upload($user->id, 6)->insert(['create_time'=>date('Y-m-d H:i:s')]);
            $res = Task::upload($user->id, 10);
        } else {
            $res = false;
        }
        if ($res) {
            $this->success('ok', '', 200);
        } else {
            $this->error('errer', '', 100);
        }
    }
    
    public function sqlbullet(){
        $name = 'RoseTang_'.mt_rand(1000,9999);
        $res = db::table('bc_bullet_name')->insert(['name'=>$name,'create_time'=>date('Y-m-d H:i:s'),'update_time'=>date('Y-m-d H:i:s')]);
        if($res){
            echo '添加成功';
        }
    }
    
    public function direct(){
        $res = db::table('bc_direct')->select();
        foreach($res as $item){
            for ($i = 0; $i < $item['vip']; $i++) {
                        $name = file_get_contents('name.txt');//将整个文件内容读入到一个字符串中
                        $name = json_decode(mb_convert_encoding($name, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                        $name = $name[array_rand($name)];
                        $photo = file_get_contents('photo.txt');//将整个文件内容读入到一个字符串中
                        $photo = json_decode(mb_convert_encoding($photo, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                        $photo = $photo[array_rand($photo)];
                        $data[] = ['direct_id' => $item['id'], 'name' => $name, 'image' => $photo, 'sex' => mt_rand(0, 1), 'level' => mt_rand(1, 30), 'contribution' => mt_rand(1, 100), 'class' => 0];
                    }
                    //守护
                    for ($i = 0; $i < $item['guard']; $i++) {
                        $name = file_get_contents('name.txt');//将整个文件内容读入到一个字符串中
                        $name = json_decode(mb_convert_encoding($name, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                        $name = $name[array_rand($name)];
                        $photo = file_get_contents('photo.txt');//将整个文件内容读入到一个字符串中
                        $photo = json_decode(mb_convert_encoding($photo, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                        $photo = $photo[array_rand($photo)];
                        $data[] = ['direct_id' => $item['id'], 'name' => $name, 'image' => $photo, 'sex' => mt_rand(0, 1), 'level' => mt_rand(1, 30), 'contribution' => mt_rand(1, 100), 'class' => 1];
                    }
                    $vip = new \app\admin\model\Vip();
                    $vip->saveAll($data);
        }
    }
}
