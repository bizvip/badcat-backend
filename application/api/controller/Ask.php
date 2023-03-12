<?php


namespace app\api\controller;


// use app\admin\model\ask\Ask;
use app\admin\model\Publisher;
use app\admin\model\community\Thumbs;
use app\admin\model\Relationship;
use app\admin\model\Topping;
use app\admin\model\ask\Wenda;
use app\common\controller\Api;
use app\common\model\Config;
use app\admin\model\ask\Askcomment as Comments;
use think\Request;

use think\Log;
use think\Db;

class Ask extends Api
{
    protected $noNeedLogin = ['*'];

    // 问答列表
    public function index()
    {
        $req = $this->request->post();
        $res = Wenda::where(['class' => 1, 'tong' => 1])->orderRaw('id desc')->select()->toArray();
        $res ?? '';
        $top = [];
        $communityid = Topping::where(['list' => 4])->column('communityid');
        foreach ($communityid as $item) {
            $top[] = Wenda::where(['id' => $item])->find();
        }
        return json(['msg' => 'ok', 'data' => $res, 'top' => $top, 'code' => 200])->header(['Content-Type' => 'application/json']);
    }

    // 问答
    public function wenda(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        // 按最新ID排序
        // $res = Video::with('publisher')->where(['tong' => 1, 'class' => 4])->orderRaw('id desc')->limit($req['every'])->select();
        $res = Wenda::with(['publisher','user'])->where(['tong' => 1, 'class' => 4])->page($req['current'], $req['every'])->orderRaw('id desc')->select();
        $communityid = Topping::where(['list' => 4])->column('communityid');
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            foreach ($res as &$value) {
                $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $value['id'], 'class' => 2])->find();
                
                $Relationship = new Relationship();
                $findisguanzhu = $Relationship->where(['user_id' => $user['id'], 'userid' => $value['user_id']])->find();
                if (!empty($findisguanzhu)) {
                    $value['isguanzhu'] = 'y';
                } else {
                    $value['isguanzhu'] = 'n';
                }
                
                if ($find) {
                    $value['give'] = 1;
                } else {
                    $value['give'] = 0;
                }
            }
        }
        return json(['msg' => '问答', 'data' => $res, 'code' => 200])->header(['Content-Type' => 'application/json']);
    }

    // 问答详情
    public function xq_ask(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $req = $request->get();
        // $res = Wenda::get($req['id']);
        $res = Wenda::with(['publisher','user'])->where('id', $req['id'])->find();
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $res['id']])->find();
            $Relationship = new Relationship();
                $findisguanzhu = $Relationship->where(['user_id' => $user['id'], 'userid' => $res['user_id']])->find();
                if (!empty($findisguanzhu)) {
                    $res['isguanzhu'] = 'y';
                } else {
                    $res['isguanzhu'] = 'n';
                }
            if ($find) {
                $res['give'] = 1;
            } else {
                $res['give'] = 0;
            }
        }

        if ($res) {
            $this->success('内容', $res, 200);
        } else {
            $this->error('系统错误', [], 100);
        }
    }

    // 回答列表
    // public function detail()
    // {
    //     $req = $this->request->post();
    //     $res = Wenda::where('id', $req['id'])->page($req['current'], $req['every'])->find();
    //     $res['answer'] = Answer::where(['cid' => $req['id'], 'tong' => 1])->page($req['current'], $req['every'])->select()->toArray();
    //     $res ?? '';
    //     $this->result('回答', $res, 200);
    // }
    public function ask_answer(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ ＧＥＴ', '', 100);
        }
        $req = $request->get();
        $count = Comments::where(['class' => $req['class'], 'ask_id' => $req['id']])->count();
        if ($count < $req['num']) {
            $size = $req['num'] - $count;
            for ($i = 0; $i < $size; $i++) {
                $name = file_get_contents('name.txt'); //将整个文件内容读入到一个字符串中
                $name = json_decode(mb_convert_encoding($name, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5')); //转换字符集（编码）
                $name = $name[array_rand($name)];
                $photo = file_get_contents('photo.txt'); //将整个文件内容读入到一个字符串中
                $photo = json_decode(mb_convert_encoding($photo, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5')); //转换字符集（编码）
                $photo = $photo[array_rand($photo)];
                $comment = db::table('bc_text')->where(['class' => $req['class']])->orderRaw('rand()')->value('text');
                Comments::insert(['name' => $name, 'avator_image' => $photo, 'content' => $comment, 'ask_id' => $req['id'], 'class' => $req['class'], 'creat_time' => date('Y-m_d H:i:s'), 'level' => mt_rand(0, 2)]);
            }
        }
        $res = Comments::where(['class' => $req['class'], 'ask_id' => $req['id'], 'tong' => 1, 'zd' => 0])->page($req['current'], $req['every'])->order('creat_time desc')->select();
        $res ? $res->toArray() : '';
        $top = Comments::where(['class' => $req['class'], 'ask_id' => $req['id'], 'tong' => 1, 'zd' => 1])->select();
        $top ? $top->toArray() : '';
        return json(['msg' => '评论', 'data' => $res, 'top' => $top, 'code' => 200])->header(['Content-Type' => 'application/json']);
    }

    // 发布问答
    public function add()
    {
        $req = $this->request->post();
        // $user = $this->auth->getUserinfo();
        $user = $this->auth->getUser();
        // 用户名称
        $req['name'] = $user['username'];
        // 用户头像
        $req['avator_image'] = $user['avatar'];
        // 分类（1问答）
        $req['class'] = 4;
        // 审核状态 
        $req['tong'] = 0;
        // 随机添加浏览量
        $req['browse'] = mt_rand(10,3000);
        // 发布时间
        $req['create_time'] = date('Y-m-d H:i:s');
        // 发布人（0用户）
        $req['status'] = 0;
        // 用户id
        $req['user_id'] = $user['id'];
        $res = Wenda::insert($req);
        if ($res) {
            // 任务id9（发布问答）
            $res = Task::upload($user->id, 9);
            $this->success('提交成功，等待审核！', '', 200);
        } else {
            $this->success('失败', '', 100);
        }
    }

    // 发送回答（答案）
    public function huida_add()
    {
        $user = $this->auth->getUserinfo();
        $req = $this->request->post();
        // 用户名称
        $req['name'] = $user['username'];
        // 用户头像
        $req['avator_image'] = $user['avatar'];
        // 审核状态
        $req['tong'] = 0;
        // 评论时间
        $req['creat_time'] = date('Y-m-d H:i:s');
        // 发布人（1用户）
        $req['status'] = 1;
        // 用户id
        $req['user_id'] = $user['id'];
        $res = Comments::insert($req);
        if ($res) {
            $this->success('回答成功，等待审核！', '', 200);
        } else {
            $this->success('错误', '', 100);
        }
    }

    //头像信息（问答）
    public function personal_wenda(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        $ask = Wenda::where('id', $req['id'])->find();
        //用户
        if ($ask['status'] == 0) {
            $user = \app\admin\model\User::where('id', $ask['user_id'])->find();
            $user['count'] = Wenda::where(['status' => 0, 'user_id' => $ask['user_id'], 'class' => 4])->count();
            $user['u'] = 0;
        } else {
            //后台
            $data = Publisher::where('id', $ask['user_id'])->find()->toArray();
            $user['username'] = $data['name'];
            $user['avatar'] = $data['image'];
            $user['background'] = $data['background'];
            $user['bio'] = $data['bio'];
            $user['gender'] = $data['gender'];
            $user['fensi'] = $data['fensi'];
            $user['ganzhu'] = $data['guanzhu'];
            $user['count'] = Wenda::where(['status' => 1, 'user_id' => $ask['user_id'], 'class' => 4])->count();
            $user['u'] = 1;
            $user['id'] = $data['id'];
            $user['level'] = $data['level'];
        }
        $asks = Wenda::with(['publisher','user'])->where(['status' => $ask['status'], 'user_id' => $ask['user_id'], 'tong' => 1])->where('class', 'neq', 0)->page($req['current'], $req['every'])->select();
        $asks ? $asks = $asks->toArray() : '';
        $this->success('社区信息返回', ['user' => $user, 'ask' => $asks], 200);
    }

    //点赞作品（问答）
    public function thumbs_wenda(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $user = $this->auth->getUser();
        $req = $request->get();
        $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $req['id'], 'class' => 3])->find();
        if ($find) {
            $this->result('您已经点过赞了', '', 100);
        } else {
            $thumbs = new Thumbs();
            $res = $thumbs->save(['userid' => $user['id'], 'thumbsid' => $req['id'], 'class' => 3]);
        }
        if ($res) {
            $res = Wenda::where('id', $req['id'])->setInc('fabulous', 1);
            if ($res) {
                // 任务id3（点赞一个作品）
                $res = Task::upload($user->id, 3);
                $this->success('点赞成功', '', 200);
            }
        } else {
            $this->error('系统错误或网络错误', '', 100);
        }
    }

    // 增加问答浏览次数
    public function ask_ll()
    {
        $req = $this->request->post();
        $res = \app\admin\model\ask\Ask::where('id', $req['id'])->setInc('browse', 1);
        if ($res) {
            $this->success('ok', '', 200);
        } else {
            $this->error('error', '', 100);
        }
    }
}
