<?php


namespace app\api\controller;


use app\admin\model\water\Label;
use app\admin\model\water\Publisher;
use app\admin\model\Relationship;
use app\admin\model\water\Thumbs;
use app\admin\model\water\Thumbsc;
use app\admin\model\water\Top;
use app\admin\model\water\Qia;
use app\common\controller\Api;
use app\common\model\Config;
use think\Request;
use app\admin\model\User as UserModel;
use app\admin\model\water\Comment as Comments;

use think\Log;
use think\Db;

use app\admin\model\UserWatchLog;

class Water extends Api
{
    protected $noNeedLogin = ['*'];

    // 评论查询
    public function chagua_comment(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ ＧＥＴ', '', 100);
        }
        $req = $request->get();
        // 共有数据总数
        // $req['count'] = \app\admin\model\water\Qia::where('class', $req['id'])->count();
        $count = Comments::where(['class' => $req['class'], 'water_id' => $req['id']])->count();
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
                Comments::insert(['name' => $name, 'avator_image' => $photo, 'content' => $comment, 'water_id' => $req['id'], 'class' => $req['class'], 'creat_time' => date('Y-m_d H:i:s'), 'level' => mt_rand(0, 2)]);
            }
        }
        $res = Comments::where(['class' => $req['class'], 'water_id' => $req['id'], 'tong' => 1, 'zd' => 0])->page($req['current'], $req['every'])->order('creat_time desc')->select();
        $res ? $res->toArray() : '';
        $top = Comments::where(['class' => $req['class'], 'water_id' => $req['id'], 'tong' => 1, 'zd' => 1])->select();
        $top ? $top->toArray() : '';
        return json(['msg' => '评论', 'data' => $res, 'top' => $top, 'code' => 200])->header(['Content-Type' => 'application/json']);
    }
    
    // 吃瓜列表
    public function qiaqia(Request $request)
    {
        $req = $this->request->post();
        // var_dump($req);die;
        // $res = Qia::with('publisher')->where(['class' => 1, 'tong' => 1])->orderRaw('id desc')->limit($req['every'])->select()->toArray();
        if (empty($req['id'])) {
            $req['id'] = '19';
        }
        $res = Qia::with(['publisher', 'user', 'waterclass', 'label'])->where(['tong' => 1])->where('waterclass.id', $req['id'])->page($req['current'], $req['every'])->orderRaw('id desc')->select()->toArray();
        // var_dump($req);die;
        // $res = Qia::with(['publisher','waterclass','label'])->where(['tong' => 1])->page(['current'], $req['every'])->orderRaw('id desc')->select()->toArray();
        // var_dump($res);die;
        $top = [];
        $waterid = Top::where(['list' => 1])->column('waterid');
        foreach ($waterid as $item) {
            // 发布者个人信息
            $top[] = Qia::with(['publisher', 'user', 'label'])->where(['id' => $item])->find();
        }
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            foreach ($res as &$value) {
                $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $value['id'], 'class' => 4])->find();
                $findc = Thumbsc::where(['userid' => $user['id'], 'thumbscid' => $value['id'], 'class' => 4])->find();

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

        return json(['msg' => 'ok', 'data' => $res, 'top' => $top, 'code' => 200])->header(['Content-Type' => 'application/json']);
    }

    // 吃瓜内容详情
    public function xq_water(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $req = $request->get();
        // $res = Qia::get($req['id']);
        $res = Qia::with(['publisher','user'])->where('id', $req['id'])->find();
        
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $res['id'], 'class' => 4])->find();
            $findc = Thumbsc::where(['userid' => $user['id'], 'thumbscid' => $res['id'], 'class' => 4])->find();
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

            if ($findc) {
                $res['gcai'] = 2;
            } else {
                $res['gcai'] = 0;
            }
        }

        if ($res) {
            $this->success('详情内容', $res, 200);
        } else {
            $this->error('系统错误', [], 100);
        }
    }

    // 分类列表
    public function suoshu()
    {
        $res = \app\admin\model\water\Waterclass::all()->toArray();
        $this->result('分类', $res, 200);
    }

    // 分类详情
    public function detail(Request $request)
    {
        $req = $this->request->post();
        $res = \app\admin\model\water\Waterclass::where('id', $req['id'])->find();
        // 只显示审核通过文章
        // $xitong_time = date('Y-m-d H:i:s');
        // $water = \app\admin\model\water\Qia::with(['Waterclass', 'label'])->where(['tong' => 1])->where('class', ['like',$req['id']], ['like',$req['id'].',%'], ['like','%,'.$req['id']],'0','or')->page($req['current'], $req['every'])->order('id desc')->select()->toArray();
        // var_dump($water);die;
        $water = (new \app\admin\model\water\Qia())->field('id,class,label,qia_image,avator_image,title,istoll,label,fabulous,stamp,browse,comment,status,tong,create_time')->with(['Waterclass','label'])->where(['tong' => 1])->where('class', ['like',$req['id']], ['like',$req['id'].',%'], ['like','%,'.$req['id']],'0','or')->page($req['current'], $req['every'])->order('id desc')->select()->toArray();
        $water ?? ',';
        // if (!empty($request->header('token'))) {
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            foreach ($water as &$value) {
                $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $value['id'], 'class' => 4])->find();
                $findc = Thumbsc::where(['userid' => $user['id'], 'thumbscid' => $value['id'], 'class' => 4])->find();

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
        // foreach ($water as &$value) {
        // }

        $res['water'] = $water;
        $this->result('分类详情', $res, 200);
    }
    
    // 头像信息
    public function personal(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        $water = Qia::where('id', $req['id'])->find();
        // 用户
        if ($water['status'] == 0) {
            $user = \app\admin\model\User::where('id', $water['user_id'])->find();
            $user['count'] = Qia::where(['status' => 0, 'tong' => 1, 'user_id' => $water['user_id']])->count();
            $user['u'] = 0;
        } else {
            // 后台
            $data = Publisher::where('id', $water['user_id'])->find()->toArray();
            $user['username'] = $data['name'];
            $user['avatar'] = $data['image'];
            $user['background'] = $data['background'];
            $user['bio'] = $data['bio'];
            $user['gender'] = $data['gender'];
            $user['fensi'] = $data['fensi'];
            $user['ganzhu'] = $data['guanzhu'];
            $user['count'] = Qia::where(['status' => 1, 'tong' => 1, 'user_id' => $water['user_id']])->count();
            $user['u'] = 1;
            $user['id'] = $data['id'];
            $user['level'] = $data['level'];
        }
        $waters = Qia::with('publisher')->where(['status' => $water['status'], 'user_id' => $water['user_id'], 'tong' => 1])->where('class', 'neq', 0)->page($req['current'], $req['every'])->orderRaw('id desc')->select();
        $waters ? $waters = $waters->toArray() : '';
        $this->success('头像信息返回', ['user' => $user, 'water' => $waters], 200);
    }

    // 关注
    public function follow(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $req = $request->get();
        $user = $this->auth->getUserinfo();
        $Relationship = new Relationship();
        $find = $Relationship->where(['user_id' => $user['id'], 'userid' => $req['id'], 'class' => $req['class']])->find();
        if ($find) {
            $this->error('你已经关注过TA啦！', '', 100);
        }
        $res = $Relationship->save(['user_id' => $user['id'], 'userid' => $req['id'], 'class' => $req['class']]);
        if ($res) {
            // 真实用户个人关注数+被关注者粉丝数+1
            \app\admin\model\User::where('id', $user['id'])->setInc('guanzhu', 1);
            \app\admin\model\User::where('id', $req['id'])->setInc('fensi', 1);
            // 虚拟用户个人粉丝数+1
            // \app\admin\model\Publisher::where('id', $user['id'])->setInc('guanzhu', 1);
            \app\admin\model\Publisher::where('id', $req['id'])->setInc('fensi', 1);
            $this->success('关注成功', '', 200);
        } else {
            $this->success('系统错误', '', 100);
        }
    }

    // 关注显示
    public function followindex(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＭＵＳＴ　ＢＥ　post');
        }
        $req = $request->get();
        $user = $this->auth->getUserinfo();
        $res = [];
        $res = Relationship::where('user_id', $user['id'])->select()->toArray();
        foreach ($res as &$value) {
            //用户
            if ($value['class'] == 0) {
                $value['user'] = \app\admin\model\User::where('id', $value['userid'])->field('id,username name ,avatar')->find()->toArray();
                $value['user']['image'] = $value['user']['avatar_text'];
            }
            //后台
            if ($value['class'] == 1) {
                $value['user'] = Publisher::where('id', $value['userid'])->find()->toArray();
            }
        }
        $this->result('关注列表', $res, 200);
    }

    // 标签列表
    public function label()
    {
        $res = Label::all();
        $this->success('标签', $res, 200);
    }

    // 点赞
    public function thumbs(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $user = $this->auth->getUser();
        $req = $request->get();
        $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $req['id'], 'class' => 4])->find();
        if ($find) {
            $this->result('已经赞过了哟~', '', 100);
        } else {
            $thumbs = new Thumbs();
            $res = $thumbs->save(['userid' => $user['id'], 'thumbsid' => $req['id'], 'class' => 4]);
        }
        if ($res) {
            $res = Qia::where('id', $req['id'])->setInc('fabulous', 1);
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
        $find = Thumbsc::where(['userid' => $user['id'], 'thumbscid' => $req['id'], 'class' => 4])->find();
        if ($find) {
            $this->result('已经踩过了呀！', '', 100);
        } else {
            $thumbsc = new Thumbsc();
            $res = $thumbsc->save(['userid' => $user['id'], 'thumbscid' => $req['id'], 'class' => 4]);
        }
        if ($res) {
            $res = Qia::where('id', $req['id'])->setInc('stamp', 1);
            if ($res) {
                // 任务id4（踩一个作品）
                $res = Task::upload($user->id, 4);
                $this->success('踩一个', '', 200);
            }
        } else {
            $this->error('系统错误或网络错误', '', 100);
        }
    }

    // 投稿
    public function add_throw(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post(false);   //不过滤 P 标签（上传图片）
        $user = $this->auth->getUser();
        // 用户id
        $req['user_id'] = $user['id'];
        // 用户名称
        $req['name'] = $user['username'];
        // 用户头像
        $req['avator_image'] = $user['avatar'];
        // 分类
        $req['class'] = 0;
        // 标签
        $req['label'] = 0;
        // 发布人（0默认用户）
        $req['status'] = 0;
        // 审核状态（默认0 待审核）
        $req['tong'] = 0;
         // 随机添加评论数
        $req['comment'] = 0;
        // 随机添加浏览量
        $req['browse'] = mt_rand(10,3000);
        // 发布时间
        $req['create_time'] = date('Y-m-d H:i:s');
        $add = Qia::insert($req);
        if ($add) {
            // 任务id6（吃瓜投稿）
            $add = Task::upload($user->id, 6);
            $this->success('投稿提交成功，等待审核!');
        } else {
            $this->error('网络错误或系统错误');
        }
    }
    
    // 发送评论
    public function addcomment(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＭＵＳＴ　ＢＥ ＰＯＳＴ', '', 100);
        }
        $req = $request->post();
        // $user = $this->auth->getUserinfo();
        $user = $this->auth->getUser();
        $data = array(
            // 'userid' => $user['id'],
            // 用户名称
            'name' => $user['username'],
            // 用户头像
            'avator_image' => $user['avatar'],
            // 评论分类
            'class' => $req['class'],
            // 被评论文章id
            'water_id' => $req['water_id'],
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
            $this->success('评论成功！等待审核', '', 200);
        } else {
            $this->error('系统错误', '', 100);
        }
    }

    // 增加浏览次数
    public function water_ll()
    {
        $req = $this->request->post();
        $res = \app\admin\model\water\Qia::where('id', $req['id'])->setInc('browse', 1);
        if ($res) {
            $this->success('ok', '', 200);
        } else {
            $this->error('error', '', 100);
        }
    }
}
