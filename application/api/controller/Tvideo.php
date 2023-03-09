<?php


namespace app\api\controller;
use app\admin\model\Publisher;
use app\admin\model\tvideo\Thumbs;
use app\admin\model\Relationship;
use app\common\controller\Api;
use app\admin\model\tvideo\Tvideoa as Tvs;
use think\Request;
use app\admin\model\User;
use app\admin\model\UserWatchLog;
use app\common\model\Config;
use think\Log;

class Tvideo extends Api
{
    protected $noNeedLogin = ['*'];

    // 短视频
    public function video_list(\think\Request $request)
    {
        if ( ! $request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        // $res = Tvs::where(['tong' => 1])->orderRaw('rand()')->limit($req['every'])->select()->toArray();
        $res = Tvs::where(['tong' => 1])->orderRaw('rand()')->select()->toArray();
        // $res = Tvs::where(['tong' => 1])->orderRaw('rand()')->field('id,sid,name,avator_image,title,video_url,thumbs,comment,user_id,playIng,state,playNumber,isShowimage,isShowProgressBarTime,isplay')->select()->toArray();
        $res ?? '';
        
        // Log::record(11111);
        // Log::record($request->header('token'));
        // var_dump($res);die;
        
        if (!empty($request->header('token'))) {
        // if ($request->header('token') !== null) {
            $user = $this->auth->getUserinfo();
            // var_dump($user);die;
            foreach ($res as &$value) {
                $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $value['sid'], 'class' => 0])->find();
                $findisguanzhu = Relationship::where(['user_id' => $user['id'], 'userid' => $value['user_id']])->find();
                if ($find) {
                    $value['give'] = 1;
                } else {
                    $value['give'] = 0;
                }
                if (!empty($findisguanzhu)) {
                    $value['isguanzhu'] = 'y';
                } else {
                    $value['isguanzhu'] = 'n';
                }
            }
        }
        $this->result('小视频', $res, 200);
    }

    // 点赞作品
    public function thumbs(Request $request)
    {
        if ( ! $request->isGet()) {
            $this->error('ＭＵＳＴ　ＢＥ　ＧＥＴ');
        }
        $user = $this->auth->getUser();
        $req = $request->get();
        $find = Thumbs::where(['userid' => $user['id'], 'thumbsid' => $req['id'],'class' => 0])->find();
        if ($find) {
            $this->result('您已经点过赞了', '', 100);
        } else {
            $thumbs = new Thumbs();
            $res = $thumbs->save(['userid' => $user['id'], 'thumbsid' => $req['id'],'class'=>0]);
        }
        if ($res) {
            $res = Tvs::where('id', $req['id'])->setInc('thumbs', 1);
            // $res = Task::upload($user->id, 4);
            if ($res) {
                $this->success('赞一个', '', 200);
            }
        } else {
            $this->error('系统错误或网络错误', '', 100);
        }
    }
    
    // 头像信息（图片）
    public function personal_shortvideo(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $req = $request->post();
        $shortvideo = Tvs::where('id', $req['id'])->find();
        //用户
        if ($shortvideo['status'] == 0) {
            $user = \app\admin\model\User::where('id', $shortvideo['user_id'])->find();
            $user['count'] = Tvs::where(['status' => 0, 'tong' => 1, 'user_id' => $shortvideo['user_id'], 'class' => 0])->count();
            $user['u'] = 0;
        } else {
            //后台
            $data = Publisher::where('id', $shortvideo['user_id'])->find()->toArray();
            $user['username'] = $data['name'];
            $user['avatar'] = $data['image'];
            $user['background'] = $data['background'];
            $user['bio'] = $data['bio'];
            $user['gender'] = $data['gender'];
            $user['fensi'] = $data['fensi'];
            $user['ganzhu'] = $data['guanzhu'];
            $user['count'] = Tvs::where(['status' => 1, 'tong' => 1, 'user_id' => $shortvideo['user_id'], 'class' => 0])->count();
            $user['u'] = 1;
            $user['id'] = $data['id'];
            $user['level'] = $data['level'];
        }
        $shortvideos = Tvs::with(['publisher','user'])->where(['status' => $shortvideo['status'], 'user_id' => $shortvideo['user_id'], 'tong' => 1])->where('class', 'neq', 1)->page($req['current'], $req['every'])->orderRaw('id desc')->select();
        $shortvideos ? $shortvideos = $shortvideos->toArray() : '';
        $this->success('短视频信息返回', ['user' => $user, 'shortvideo' => $shortvideos], 200);
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
    
}
