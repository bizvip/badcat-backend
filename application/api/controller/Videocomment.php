<?php


namespace app\api\controller;


use app\admin\model\DVideo;
use app\common\controller\Api;
use app\common\model\Config;
use think\Request;
use app\admin\model\video\Comment as Comments;
use think\Db;

class Videocomment extends Api
{
    protected $noNeedLogin = ['*'];

    // 评论查询
    public function video_ping(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ ＧＥＴ', '', 100);
        }
        $req = $request->get();
        $count = Comments::where(['class' => 3, 'video_id' => $req['id']])->count();
        if ($count < $req['num']) {
            $size = $req['num'] - $count;
            for ($i = 0; $i < $size; $i++) {
                $name = file_get_contents('name.txt');//将整个文件内容读入到一个字符串中
                $name = json_decode(mb_convert_encoding($name, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                $name = $name[array_rand($name)];
                $photo = file_get_contents('photo.txt');//将整个文件内容读入到一个字符串中
                $photo = json_decode(mb_convert_encoding($photo, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                $photo = $photo[array_rand($photo)];
                $comment = db::table('apoccdio_text')->where(['class' => 3])->orderRaw('rand()')->value('text');
                Comments::insert(['name' => $name, 'avator_image' => $photo, 'content' => $comment, 'video_id' => $req['id'], 'class' => 3, 'creat_time' => date('Y-m_d H:i:s'),'level'=>mt_rand(0,2)]);
            }
        }
        $res = Comments::where(['class' => 3, 'video_id' => $req['id'], 'tong' => 1])->page($req['current'], $req['every'])->order('creat_time desc')->select();
        $res ? $res->toArray() : '';
        $this->success('视频评论查询', $res, 200);
    }

    // 发送评论
    public function addcomment(Request $request)
    {
        if ( ! $request->isPost()) {
            $this->error('ＭＵＳＴ　ＢＥ ＰＯＳＴ', '', 100);
        }
        $req = $request->post();
        $user = $this->auth->getUser();
        $data = array(
            'name' => $user['username'],
            'avator_image' => $user['avatar'],
            'class' => $req['class'],
            'video_id' => $req['video_id'],
            'content' => $req['content'],
            'tong' => 0,
            'creat_time' => date('Y-m-d H:i:s'),
            'status' => 1
        );
        $comments = new Comments();
        $res = $comments->save($data);
        if ($res) {
            // 任务id5（评论一个作品）
            $res = Task::upload($user->id, 5);
//            if($req['class']=='0'||$req['class']=='1'||$req['class']=='2'){
//                Video::where(['id'=>$req['video_id']])->setInc('comment',1);
//            }
//            if($req['class']=='4'){
//                Tvideo::where(['id'=>$req['video_id']])->setInc('comment',1);
//            }
//            if($req['class']=='3'){
//                Dvideo::where(['id'=>$req['video_id']])->setInc('comments',1);
//            }
            $this->success('评论成功等待审核', '', 200);
        } else {
            $this->error('系统错误', '', 100);
        }
    }

    // 增加浏览量
    // public function video_ll()
    // {
    //     $req = $this->request->post();
    //     Dvideo::where('id', $req['id'])->setInc('', 1);
    // }
    public function video_ll()
    {
        $req = $this->request->post();
        $res = \app\admin\model\Dvideo::where('id', $req['id'])->setInc('vod_browse', 1);
        if ($res) {
            $this->success('ok', '', 200);
        } else {
            $this->error('error', '', 100);
        }
    }
    
}
