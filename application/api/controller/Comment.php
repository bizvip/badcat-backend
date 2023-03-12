<?php


namespace app\api\controller;


// use app\admin\model\Dvideo;
// use app\admin\model\Tvideo;
use app\admin\model\Video;
// use app\admin\model\water\Qia;
use app\common\controller\Api;
use app\common\model\Config;
use think\Request;
use app\admin\model\community\Comment as Comments;
use think\Db;

class Comment extends Api
{
    protected $noNeedLogin = ['*'];

    //社区评论查询
    public function community(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ ＧＥＴ', '', 100);
        }
        $req = $request->get();
        $count = Comments::where(['class' => $req['class'], 'community_id' => $req['id']])->count();
        if ($count < $req['num']) {
            $size = $req['num'] - $count;
            for ($i = 0; $i < $size; $i++) {
                $name = file_get_contents('name.txt');//将整个文件内容读入到一个字符串中
                $name = json_decode(mb_convert_encoding($name, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                $name = $name[array_rand($name)];
                $photo = file_get_contents('photo.txt');//将整个文件内容读入到一个字符串中
                $photo = json_decode(mb_convert_encoding($photo, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                $photo = $photo[array_rand($photo)];
                $comment = db::table('bc_text')->where(['class' => $req['class']])->orderRaw('rand()')->value('text');
                Comments::insert(['name' => $name, 'avator_image' => $photo, 'content' => $comment, 'community_id' => $req['id'], 'class' => $req['class'], 'creat_time' => date('Y-m_d H:i:s'),'level'=>mt_rand(0,2)]);
            }
        }
        $res = Comments::where(['class' => $req['class'], 'community_id' => $req['id'], 'tong' => 1, 'zd' => 0])->page($req['current'], $req['every'])->order('creat_time desc')->select();
        $res ? $res->toArray() : '';
        $top = Comments::where(['class' => $req['class'], 'community_id' => $req['id'], 'tong' => 1, 'zd' => 1])->select();
        $top ? $top->toArray() : '';
        return json(['msg' => '评论', 'data' => $res, 'top' => $top, 'code' => 200])->header(['Content-Type' => 'application/json']);
    }

    // 发送评论
    public function addcomment(Request $request)
    {
        if ( ! $request->isPost()) {
            $this->error('ＭＵＳＴ　ＢＥ ＰＯＳＴ', '', 100);
        }
        $req = $request->post();
        // $user = $this->auth->getUserinfo();
        $user = $this->auth->getUser();
        $data = array(
            // 用户名称
            'name' => $user['username'],
            // 用户头像
            'avator_image' => $user['avatar'],
            // 评论分类
            'class' => $req['class'],
            // 被评论的图片/短文id
            'community_id' => $req['community_id'],
            // 评论内容
            'content' => $req['content'],
            // 等级
            'level' => $req['level'],
            // 审核状态
            'tong' => 0,
            // 评论时间
            'creat_time' => date('Y-m-d H:i:s'),
            // 发布人
            'status' => 1
        );
        $comments = new Comments();
        $res = $comments->save($data);
        if ($res) {
            // 任务id5（评论一个作品）
            $res = Task::upload($user->id, 5);
//            if($req['class']=='0'||$req['class']=='1'||$req['class']=='2'){
//                Video::where(['id'=>$req['community_id']])->setInc('comment',1);
//            }
//            if($req['class']=='4'){
//                Tvideo::where(['id'=>$req['community_id']])->setInc('comment',1);
//            }
//            if($req['class']=='3'){
//                Dvideo::where(['id'=>$req['community_id']])->setInc('comments',1);
//            }
            $this->success('评论成功，等待审核！', '', 200);
        } else {
            $this->error('系统错误', '', 100);
        }
    }

    //增加社区浏览次数
    public function community_ll()
    {
        $req = $this->request->post();
        $res = \app\admin\model\Video::where('id', $req['id'])->setInc('browse', 1);
        if ($res) {
            $this->success('ok', '', 200);
        } else {
            $this->error('error', '', 100);
        }
    }
    
    //小视频浏览
    public function tvideo_ll()
    {

    }
}
