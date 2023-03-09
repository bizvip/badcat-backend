<?php


namespace app\api\controller;


use app\admin\model\Label;
use app\admin\model\Publisher;
use app\admin\model\Relationship;
use app\admin\model\Fanlistindex;
use app\admin\model\community\Homeclass;
use app\admin\model\community\Thumbs;
use app\admin\model\Topping;
use app\admin\model\Video;
use app\admin\model\water\Qia;
use app\common\controller\Api;
use app\common\model\Config;
use think\Request;
use app\admin\model\Game;
use app\admin\model\User as UserModel;

use app\admin\model\Tvideo as Tvs;
use app\admin\model\Dvideo as Dvs;

use think\Log;


use app\admin\model\UserWatchLog;

class Community extends Api
{
    protected $noNeedLogin = ['*'];

    // 首页分类
    public function homelist(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $res = Homeclass::all();
        $res ? $res = $res->toArray() : '';
        $this->result('分类', $res, 200);
    }

    // 图片列表
    public function photo(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        // 随机排序
        // $res = Video::with('publisher')->where(['tong' => 1, 'class' => 2])->orderRaw('rand()')->limit($req['every'])->select();
        // 限度 limit($req['every'])
        // 按最新ID排序
        $res = Video::with(['publisher','user'])->where(['tong' => 1, 'class' => 2])->page($req['current'], $req['every'])->orderRaw('id desc')->select();
        $top = [];
        $communityid = Topping::where(['list' => 2])->column('communityid');
        
        // $user = $this->auth->getUserinfo();
        foreach ($communityid as $item) {
            // 发布者个人信息
            $top[] = Video::with(['publisher','user'])->where(['id' => $item])->find();
            
            // $findisguanzhu = Relationship::where(['user_id' => $user['id'], 'userid' => $item['user_id']])->find();
            // if (!empty($findisguanzhu)) {
            //     $communityid['isguanzhu'] = 'y';
            // } else {
            //     $communityid['isguanzhu'] = 'n';
            // }
        }
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            foreach ($res as &$value) {
                $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $value['id'], 'class' => 1])->find();
                // $Relationship = new Relationship();
                // $findisguanzhu = $Relationship->where(['user_id' => $user['id'], 'userid' => $value['user_id']])->find();
                $findisguanzhu = Relationship::where(['user_id' => $user['id'], 'userid' => $value['user_id']])->find();
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
        return json(['msg' => '图片', 'data' => $res, 'top' => $top, 'code' => 200])->header(['Content-Type' => 'application/json']);
    }

    // 短文列表
    public function content(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        // 按最新ID排序
        $res = Video::with(['publisher','user'])->where(['tong' => 1, 'class' => 0])->page($req['current'], $req['every'])->orderRaw('id desc')->select();
        $top = [];
        // 置顶文章列表
        $communityid = Topping::where(['list' => 0])->column('communityid');
        foreach ($communityid as $item) {
            // 发布者个人信息
            $top[] = Video::with(['publisher','user'])->where(['id' => $item])->find();
        }
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
        return json(['msg' => '短文', 'data' => $res, 'top' => $top, 'code' => 200])->header(['Content-Type' => 'application/json']);
    }

    // ASMR列表
    public function videos(Request $request)
    {
        $req = $this->request->post();
        // 按最新ID排序
        $res = Video::with('publisher')->where(['class' => 1, 'tong' => 1])->page($req['current'], $req['every'])->orderRaw('id desc')->field('id,user_id,class,label,title,images,video_image,istoll,label,fabulous,stamp,browse,comment,status,create_time')->select()->toArray();
        //$res = Video::with('publisher')->where(['class' => 1, 'tong' => 1,'id'=>146])->select()->toArray();
        $top = [];
        $communityid = Topping::where(['list' => 1])->column('communityid');
        foreach ($communityid as $item) {
            // 发布者个人信息
            $top[] = Video::with('publisher')->where(['id' => $item])->find();
        }
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

        return json(['msg' => '视频', 'data' => $res, 'top' => $top, 'code' => 200])->header(['Content-Type' => 'application/json']);
    }

    // 发布图片
    public function add_photo(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        $user = $this->auth->getUser();
        // 用户id
        $req['user_id'] = $user['id'];
        // 用户名称
        $req['name'] = $user['username'];
        // 用户头像
        $req['avator_image'] = $user['avatar'];
        // 分类
        $req['class'] = 2;
        // 标签
        $req['label'] = 0;
        // 发布人（0 默认用户）
        $req['status'] = 0; //用户
        // 审核状态（默认0 待审核）
        $req['tong'] = 0;
        // 随机添加浏览量
        $req['browse'] = mt_rand(10,3000);
        // 发布时间
        $req['create_time'] = date('Y-m-d H:i:s');
        $add = Video::insert($req);
        if ($add) {
            // 任务id7（发布图片）
            $add = Task::upload($user->id, 7);
            $this->success('信息已提交，等待审核!');
        } else {
            $this->error('网络错误或系统错误');
        }
    }
    
    // 发布短文审核
    public function add_dw(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
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
        // 发布人（0 默认用户）
        $req['status'] = 0;
        // 审核状态（默认0 待审核）
        $req['tong'] = 0;
        // 随机添加浏览量
        $req['browse'] = mt_rand(10,3000);
        // 发布时间
        $req['create_time'] = date('Y-m-d H:i:s');
        // halt($req);
        $add = Video::insert($req);
        if ($add) {
            // 任务id8（发布短文）
            $add = Task::upload($user->id, 8);
            $this->success('提交成功，等待审核！');
        } else {
            $this->error('网络错误或系统错误');
        }
    }

    // 发布视频
    // public function add_video(Request $request)
    // {
    //     if ( ! $request->isPost()) {
    //         $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
    //     }
    //     $req = $request->post();
    //     $user = $this->auth->getUser();
    //     // 用户id
    //     $req['user_id'] = $user['id'];
    //     // 用户名称
    //     $req['name'] = $user['username'];
    //     // 用户头像
    //     $req['avator_image'] = $user['avatar'];
    //     // 分类
    //     $req['class'] = 1;
    //     // 发布人（0默认用户）
    //     $req['status'] = 0;//用户
    //     // 审核状态（默认0 待审核）
    //     $req['tong'] = 0;
    //     // 发布时间
    //     $req['create_time'] = date('Y-m-d H:i:s');
    //     $add = Video::insert($req);
    //     if ($add) {
    //         $this->success('信息已提交，等待审核!');
    //     } else {
    //         $this->error('网络错误或系统错误');
    //     }
    // }

    // 头像信息（图片）
    public function personal_photo(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        $community = Video::where('id', $req['id'])->find();
        //用户
        if ($community['status'] == 0) {
            $user = \app\admin\model\User::where('id', $community['user_id'])->find();
            $user['count'] = Video::where(['status' => 0, 'tong' => 1, 'user_id' => $community['user_id'], 'class' => 2])->count();
            $user['u'] = 0;
        } else {
            //后台
            $data = Publisher::where('id', $community['user_id'])->find()->toArray();
            $user['username'] = $data['name'];
            $user['avatar'] = $data['image'];
            $user['background'] = $data['background'];
            $user['bio'] = $data['bio'];
            $user['gender'] = $data['gender'];
            $user['fensi'] = $data['fensi'];
            $user['ganzhu'] = $data['guanzhu'];
            $user['count'] = Video::where(['status' => 1, 'tong' => 1, 'user_id' => $community['user_id'], 'class' => 2])->count();
            $user['u'] = 1;
            $user['id'] = $data['id'];
            $user['level'] = $data['level'];
        }
        $communitys = Video::with(['publisher','user'])->where(['status' => $community['status'], 'user_id' => $community['user_id'], 'tong' => 1])->where('class', 'neq', 1)->page($req['current'], $req['every'])->orderRaw('id desc')->select();
        $communitys ? $communitys = $communitys->toArray() : '';
        $this->success('社区信息返回', ['user' => $user, 'community' => $communitys], 200);
    }

    // 头像信息（短文）
    public function personal_duanwen(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        $community = Video::where('id', $req['id'])->find();
        //用户
        if ($community['status'] == 0) {
            $user = \app\admin\model\User::where('id', $community['user_id'])->find();
            $user['count'] = Video::where(['status' => 0, 'tong' => 1, 'user_id' => $community['user_id'], 'class' => 0])->count();
            $user['u'] = 0;
        } else {
            //后台
            $data = Publisher::where('id', $community['user_id'])->find()->toArray();
            $user['username'] = $data['name'];
            $user['avatar'] = $data['image'];
            $user['background'] = $data['background'];
            $user['bio'] = $data['bio'];
            $user['gender'] = $data['gender'];
            $user['fensi'] = $data['fensi'];
            $user['ganzhu'] = $data['guanzhu'];
            $user['count'] = Video::where(['status' => 1, 'tong' => 1, 'user_id' => $community['user_id'], 'class' => 0])->count();
            $user['u'] = 1;
            $user['id'] = $data['id'];
            $user['level'] = $data['level'];
        }
        $communitys = Video::with(['publisher','user'])->where(['status' => $community['status'], 'user_id' => $community['user_id'], 'tong' => 1])->where('class', 'neq', 1)->page($req['current'], $req['every'])->orderRaw('id desc')->select();
        $communitys ? $communitys = $communitys->toArray() : '';
        $this->success('社区信息返回', ['user' => $user, 'community' => $communitys], 200);
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
            $this->error('你已经关注过Ta啦！', '', 200);
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

    // 已关注列表
    public function followindex(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＭＵＳＴ　ＢＥ　post');
        }
        $req = $request->post();
        $user = $this->auth->getUser();
        $res = Relationship::with(['user','publisher'])->where('user_id', $user['id'])->select();
        $res ? $res->toArray() : '';
        $this->result('关注列表', $res, 200);
    }
    
    // 粉丝列表
    public function fanindex(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＭＵＳＴ　ＢＥ　post');
        }
        $req = $request->post();
        $user = $this->auth->getUser();
        $res = Fanlistindex::with(['user','publisher'])->where('userid', $user['id'])->select();
        $res ? $res->toArray() : '';
        // 是否已关注
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            foreach ($res as &$value) {
                $Relationship = new Relationship();
                $findisguanzhu = $Relationship->where(['user_id' => $user['id'], 'userid' => $value['user_id']])->find();
                if (!empty($findisguanzhu)) {
                    $value['isguanzhu'] = 'y';
                } else {
                    $value['isguanzhu'] = 'n';
                }
            }
        }
        $this->result('粉丝列表', $res, 200);
    }
    
    // 发布者主页
    // public function click_follow(Request $request)
    // {

    //     if (!$request->isPost()) {
    //         $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
    //     }
    //     $req = $request->post();
    //     if ($req['class'] == '0') {
    //         //用户
    //         $user = \app\admin\model\User::where('id', $req['id'])->find();
    //         $user['count'] = Video::where(['status' => 0, 'tong' => 1, 'user_id' => $req['id']])->count();
    //         $user['u'] = 0;
    //     } else {
    //         //后台
    //         $data = Publisher::where('id', $req['id'])->find()->toArray();
    //         $user['username'] = $data['name'];
    //         $user['avatar'] = $data['image'];
    //         $user['background'] = $data['background'];
    //         $user['bio'] = $data['bio'];
    //         $user['gender'] = $data['gender'];
    //         $user['fensi'] = $data['fensi'];
    //         $user['guanzhu'] = $data['guanzhu'];
    //         $user['count'] = Video::where(['status' => 1, 'user_id' => $req['id']])->count();
    //         $user['u'] = 1;
    //         $user['id'] = $data['id'];
    //         $user['level'] = $data['level'];
    //     }
    //     $communitys = Video::with(['publisher','user'])->where(['status' => $req['class'], 'user_id' => $req['id'], 'tong' => 1])->page($req['current'], $req['every'])->select();
    //     $communitys ? $communitys = $communitys->toArray() : '';
    //     $this->success('信息返回', ['user' => $user, 'community' => $communitys], 200);
    // }
    
    // 已发布
    public function click_homepage(Request $request)
    {

        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        
        if ($req['class'] == '0') {
            //用户
            $user = \app\admin\model\User::where('id', $req['id'])->find();
            $user['count'] = Video::where(['status' => 0, 'tong' => 1, 'user_id' => $req['id']])->count();
            $user['u'] = 0;
        } else {
            //后台
            $data = Publisher::where('id', $req['id'])->find()->toArray();
            $user['username'] = $data['name'];
            $user['avatar'] = $data['image'];
            $user['background'] = $data['background'];
            $user['bio'] = $data['bio'];
            $user['gender'] = $data['gender'];
            $user['fensi'] = $data['fensi'];
            $user['guanzhu'] = $data['guanzhu'];
            $user['count'] = Video::where(['status' => 1, 'user_id' => $req['id']])->count();
            $user['u'] = 1;
            $user['id'] = $data['id'];
            $user['level'] = $data['level'];
        }
        
        // 社区数据
        // 已通过
        $community_t = Video::with(['publisher','user'])->where(['status' => $req['class'], 'user_id' => $req['id'], 'tong' => 1])->page($req['current'], $req['every'])->select();
        $community_t ? $community_t = $community_t->toArray() : '';
        // 待审核
        $community_d = Video::with(['publisher','user'])->where(['status' => $req['class'], 'user_id' => $req['id'], 'tong' => 0])->page($req['current'], $req['every'])->select();
        $community_d ? $community_d = $community_d->toArray() : '';
        // 驳回
        $community_s = Video::with(['publisher','user'])->where(['status' => $req['class'], 'user_id' => $req['id'], 'tong' => 2])->page($req['current'], $req['every'])->select();
        $community_s ? $community_s = $community_s->toArray() : '';
        
        // 吃瓜数据
        // 已通过
        $qia_t = Qia::with(['publisher','user'])->where(['status' => $req['class'], 'user_id' => $req['id'], 'tong' => 1])->page($req['current'], $req['every'])->select();
        $qia_t ? $qia_t = $qia_t->toArray() : '';
        // 待审核
        $qia_d = Qia::with(['publisher','user'])->where(['status' => $req['class'], 'user_id' => $req['id'], 'tong' => 0])->page($req['current'], $req['every'])->select();
        $qia_d ? $qia_d = $qia_d->toArray() : '';
        // 驳回
        $qia_s = Qia::with(['publisher','user'])->where(['status' => $req['class'], 'user_id' => $req['id'], 'tong' => 2])->page($req['current'], $req['every'])->select();
        $qia_s ? $qia_s = $qia_s->toArray() : '';
        
        $this->success('信息返回', ['total' => ($community_t + $community_d + $community_s + $qia_t + $qia_d + $qia_s), 'user' => $user, 'community_adopt' => $community_t, 'community_examine' => $community_d, 'community_fail' => $community_s, 'water_adopt' => $qia_t, 'water_examine' => $qia_d, 'water_fail' => $qia_s], 200);
    }
    
    // 图片详情
    public function xq_community_photo(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $req = $request->get();
        // $res = Video::get($req['id']);
        $res = Video::with(['publisher','user'])->where('id', $req['id'])->find();
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $res['id'], 'class' => 1])->find();
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

    // 短文详情
    public function xq_community_duanwen(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $req = $request->get();
        // $res = Video::get($req['id']);
        $res = Video::with(['publisher','user'])->where('id', $req['id'])->find();
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $res['id'], 'class' => 2])->find();
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

    // ASMR详情
    public function xq_community_asmr(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $req = $request->get();
        $res = Video::get($req['id']);
        // $res = Video::with(['publisher'])->where('id', $req['id'])->find();
        if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            $Relationship = new Relationship();
            $findisguanzhu = $Relationship->where(['user_id' => $user['id'], 'userid' => $res['user_id']])->find();
            if (!empty($findisguanzhu)) {
                $res['isguanzhu'] = 'y';
            } else {
                $res['isguanzhu'] = 'n';
            }
        }

        if ($res) {
            $this->success('内容', $res, 200);
        } else {
            $this->error('系统错误', [], 100);
        }
    }

    // 上传文件（发布）
    public function upload(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $files = $_FILES;
        $imageArr = array();
        foreach ($files as $file) {
            $imageName = $file['name'];
            //后缀名
            $ext = strtolower(substr(strrchr($imageName, '.'), 1));
            //保存文件名
            $fileName = uniqid();
            // 时间
            $creat_time = date('Y-m-d');
            $tmp = $file['tmp_name'];
            //保存 = 路径 + 文件名 + 后缀名
            $imageSavePath = ROOT_PATH . 'public' . DS . 'upload/front_end/data/' . $fileName . '.' . $ext;
            $info = move_uploaded_file($tmp, $imageSavePath);
            if ($info) {
                $path = "/upload/front_end/data/" . $fileName . '.' . $ext;
                array_push($imageArr, $path);
            }
        }
        //最终生成的字符串路径
        $imagePathStr = implode(',', $imageArr);
        $this->success('路径', $imagePathStr, 200);
    }

    // 上传文件
    // public function uploada(Request $request)
    // {
    //     if (!$request->isPost()) {
    //         $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
    //     }
    //     $files = $_FILES;
    //     $imageArr = array();
    //     foreach ($files as $file) {
    //         $imageName = $file['name'];
    //         //后缀名
    //         $ext = strtolower(substr(strrchr($imageName, '.'), 1));
    //         //保存文件名
    //         $fileName = uniqid();
    //         $tmp = $file['tmp_name'];
    //         //保存 = 路径 + 文件名 + 后缀名
    //         $imageSavePath = ROOT_PATH . 'public' . DS . 'upload/user/images/new_file/' . $fileName . '.' . $ext;
    //         $info = move_uploaded_file($tmp, $imageSavePath);
    //         if ($info) {
    //             $path = config('host') . "/upload/user/images/new_file/" . $fileName . '.' . $ext;
    //             array_push($imageArr, $path);
    //         }
    //     }
    //     //最终生成的字符串路径
    //     $imagePathStr = implode(',', $imageArr);
    //     $this->success('路径', $imagePathStr, 200);
    // }

    // 标签
    public function label()
    {
        $res = Label::all();
        $this->success('标签', $res, 200);
    }

    // 观看视频
    public function edit(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＰＯＳＴ');
        }

        $req = $request->post();

        $user = $this->auth->getUser();
        $class_id = $req['class'];

        if ($class_id == 0) {
            $class_id = 1; //长视频
        } elseif ($class_id == 1) {
            $class_id = 2; //直播
        } else {
            $class_id = 3; //短视频
        }

        Log::record(777788811);
        Log::record($class_id);
        // Log::record($user);

        // Log::record($class_id);
        if ($class_id > 0 && !empty($req['video_id'])) {

            $user_watch_log = UserWatchLog::where(['user_id' => $user->id, 'video_id' => $req['video_id'], 'class_id' => $class_id])->find();

            if ($user_watch_log) {
                if ($user_watch_log['endtime'] > time()) {
                    $this->success('ok', 'yyy', 200);
                }
            }
        }


        // $allowall = Config::where('name', 'allowfree')->value('value');
        // if ($allowall == '1') {
        //     //次数全部免费
        //     $this->success('ok', '', 200);
        // }

        Log::record(777788812);
        Log::record($class_id);

        // Log::record('video:'.$req['video_id']);
        // 直播和短视频
        if ($class_id != 1) {

            $watch_price = 0;

            // 直播
            if ($class_id == 2) {
                $video = Tvs::where('id', $req['video_id'])->find();

                $watch_price = $video['watch_price'];
                // 短视频  
            } elseif ($class_id == 3) {
                $video = Video::get($req['video_id']);

                $watch_price = $video['watch_price'];
            }
            Log::record(777788813);
            // Log::record($watch_price);
            Log::record($user['vip_time']);
            Log::record($user->id);
            // Log::record($user->num);

            // 试看视频
            if ($watch_price == 0) {
                Log::record(777788814);

                if (date('Y-m-d H:i:s') > $user['vip_time']) {

                    Log::record(777788815);

                    if ($user->num <= 0) {
                        $this->error('您的视频观看次数不足', '', 100);
                    } else {
                        $res = \app\admin\model\User::where('id', $user->id)->setDec('num', 1);
                        if ($res) {
                            Log::record(777788816);
                            $this->success('ok', '', 200);
                        } else {
                            $this->success('error', '', 100);
                        }
                    }
                } else {
                    $this->success('未扣除免费试看', '', 200);
                }
            } else {
                $this->success('error', '', 200);
            }
        } else {
            $video = Dvs::where('id', $req['video_id'])->find();

            $watch_price = $video['watch_price'];

            if ($watch_price == 0) {
                if (date('Y-m-d H:i:s') > $user['vip_time']) {
                    // if ($user->num_t <= 0) {
                    //     $this->error('您的视频观看次数不足', '', 100);
                    // } else {
                    //     $res = \app\admin\model\User::where('id', $user->id)->setDec('num_t', 1);
                    //     if ($res) {
                    //         $this->success('ok', '', 200);
                    //     } else {
                    //         $this->success('error', '', 100);
                    //     }
                    // }

                } else {
                    $this->success('ok', '', 200);
                }
            } else {
                $this->success('未扣除免费试看', '', 100);
            }
        }

        // //长视频
        // if ($req['class'] == 0) {
        //     if (date('Y-m-d H:i:s') > $user['vip_time']) {
        //         if ($user->num <= 0) {
        //             $this->error('您的长视频观看次数不足', '', 100);
        //         } else {
        //             $res = \app\admin\model\User::where('id', $user->id)->setDec('num', 1);
        //             if ($res) {
        //                 $this->success('ok', '', 200);
        //             } else {
        //                 $this->success('error', '', 100);
        //             }
        //         }

        //     } else {
        //         $this->success('ok', '', 200);
        //     }
        // }

        // //短视频
        // if ($req['class'] == 1) {
        //     if (date('Y-m-d H:i:s') > $user['vip_time']) {
        //         if ($user->num_t <= 0) {
        //             $this->error('您的长视频观看次数不足', '', 100);
        //         } else {
        //             $res = \app\admin\model\User::where('id', $user->id)->setDec('num_t', 1);
        //             if ($res) {
        //                 $this->success('ok', '', 200);
        //             } else {
        //                 $this->success('error', '', 100);
        //             }
        //         }

        //     } else {
        //         $this->success('ok', '', 200);
        //     }
        // }


        // //社区视频
        // if ($class_id == 3) {
        //     if (date('Y-m-d H:i:s') > $user['vip_time']) {
        //         if ($user->num_t <= 0) {
        //             $this->error('您的长视频观看次数不足', '', 100);
        //         } else {
        //             $res = \app\admin\model\User::where('id', $user->id)->setDec('num_t', 1);
        //             if ($res) {
        //                 $this->success('ok', '', 200);
        //             } else {
        //                 $this->success('error', '', 100);
        //             }
        //         }

        //     } else {
        //         $this->success('ok', '', 200);
        //     }

        // }

    }

    // 点赞作品（图片）
    public function thumbs_photo(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $user = $this->auth->getUser();
        $req = $request->get();
        $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $req['id'], 'class' => 1])->find();
        if ($find) {
            $this->result('已经赞过了哟~', '', 100);
        } else {
            $thumbs = new Thumbs();
            $res = $thumbs->save(['userid' => $user['id'], 'thumbsid' => $req['id'], 'class' => 1]);
        }
        if ($res) {
            $res = Video::where('id', $req['id'])->setInc('fabulous', 1);
            if ($res) {
                // 任务id3（点赞一个作品）
                $res = Task::upload($user->id, 3);
                $this->success('点赞成功', '', 200);
            }
        } else {
            $this->error('系统错误或网络错误', '', 100);
        }
    }

    // 点赞作品（短文）
    public function thumbs_duanwen(Request $request)
    {
        if (!$request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $user = $this->auth->getUser();
        $req = $request->get();
        $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $req['id'], 'class' => 2])->find();
        if ($find) {
            $this->result('已经赞过了哟~', '', 100);
        } else {
            $thumbs = new Thumbs();
            $res = $thumbs->save(['userid' => $user['id'], 'thumbsid' => $req['id'], 'class' => 2]);
        }
        if ($res) {
            $res = Video::where('id', $req['id'])->setInc('fabulous', 1);
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
