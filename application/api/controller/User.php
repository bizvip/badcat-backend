<?php

namespace app\api\controller;

use app\admin\model\Admin as AdminModel;
use app\admin\model\Ext;
use app\admin\model\Relationship;
use app\admin\model\Video;
use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use app\common\model\Config;
use fast\Random;
use think\Cache;
use think\Request;
use think\Validate;
use app\admin\model\User as Usermodel;
use think\Db;

/**
 * 会员接口
 */
class User extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
     *
     * @param string $account 账号
     * @param string $password 密码
     */
    public function login()
    {
        $mobile = $this->request->request('mobile');
        $password = $this->request->request('password');
        if (!$mobile || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($mobile, $password);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data, 200);
        } else {
            $this->error($this->auth->getError());
        }
    }

    //    //短信发送
    //    public function sendMsm()
    //    {
    //        $code =mt_rand(10000,99999);
    //        $url = "http://intapi.253.com/send/json";
    //        //headers数组内的格式
    //
    //        // 参数
    //        $data['msg'] = $code.'is your code to register your account. Don\'t reply to this message with your code.';
    //        $data['mobile'] = input('mobile');
    //        $data['account'] = 'I2732666';
    //        $data['password'] = '0851Xlr7v393f3';
    //        $res = $this->curlPost($url,$data);
    //        if(json_decode($res,true)['code']=='0'){
    //            return $code;
    //        };
    //    }
    //    private function curlPost($url, $postFields)
    //    {
    //
    //        $postFields = json_encode($postFields);
    //        $ch = curl_init();
    //        curl_setopt($ch, CURLOPT_URL, $url);
    //        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //                'Content-Type: application/json; charset=utf-8'
    //            )
    //        );
    //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //        curl_setopt($ch, CURLOPT_POST, 1);
    //        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    //        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    //        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //        $ret = curl_exec($ch);
    //        if (false == $ret) {
    //            $result = curl_error($ch);
    //        } else {
    //            $rsp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //            if (200 !== $rsp) {
    //                $result = "请求状态 " . $rsp . " " . curl_error($ch);
    //            } else {
    //                $result = $ret;
    //            }
    //        }
    //        curl_close($ch);
    //        return $result;
    //    }
    public function sendMsm()
    {
        $code = mt_rand(100000, 999999);
        /*
        $url = 'https://106.ihuyi.com/webservice/sms.php?method=Submit';
        $data['mobile'] = input('mobile');
        $data['content'] = '您的验证码是：' . $code . '。请不要把验证码泄露给其他人。';
        $data['account'] = 'C51625091';
        $data['password'] = 'f78be9de8659c46756a97f36a22320d3';
        $data['format'] = 'json';
        $res = $this->post($url, $data);
        */
        return $code;
    }

    /**
     * 发送短信
     */
    public function post($url, $data, $proxy = null, $timeout = 20)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //在HTTP请求中包含一个"User-Agent: "头的字符串。
        curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。
        curl_setopt($curl, CURLOPT_POST, true); //发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); //Post提交的数据包
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //文件流形式
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //设置cURL允许执行的最长秒数。
        $content = curl_exec($curl);
        curl_close($curl);
        unset($curl);
        return $content;
    }

    /**
     * 注册会员
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email 邮箱
     * @param string $mobile 手机号
     * @param string $code 验证码
     */
    public function register()
    {
        $zcs = Config::where('name', 'zcs')->value('value');
        $imeicount = Usermodel::where('imei', input('imei'))->count();
        if ($zcs == $imeicount) {
            $this->error('同一设备只能注册' . $zcs . '个账户');
        }

        // $chang = Config::where('name', 'zcc')->value('value');
        // $duan = Config::where('name', 'tvideo')->value('value');
        // 用户名前缀_ + 随机值
        $username = '坏猫小分队_' . mt_rand(38800, 999999);
        $password = $this->request->request('password');
        $mobile = $this->request->request('mobile');
        $avatars = array("1_1588736866.jpg", "2_1588736906.jpg", "3_1588736916.jpg", "4_1588736927.jpg", "5_1588736939.jpg", "7_1588737044.jpg", "8_1588737058.jpg", "10_1588737071.jpg", "11_1588737096.jpg", "12_1588737120.jpg", "13_1588737130.jpg", "14_1588737138.jpg", "15_1588737244.jpg", "16_1588737259.jpg", "17_1588737267.jpg", "18_1588737279.jpg", "19_1588737290.jpg", "20_1588737303.jpg", "21_1588740537.jpg", "22_1588740556.jpg", "23_1588740572.jpg", "24_1588740586.jpg", "26_1588741401.jpg", "27_1588741443.jpg", "28_1588741455.jpg", "29_1588741475.jpg", "31_1588741495.jpg", "32_1588741508.jpg", "33_1588741526.jpg", "34_1588741536.jpg", "35_1588741549.jpg", "36_1588741559.jpg", "37_1588741571.jpg", "38_1588741586.jpg", "39_1588741596.jpg", "40_1588741605.jpg", "41_1588741614.jpg", "42_1588741624.jpg", "43_1588741635.jpg", "44_1588741645.jpg", "45_1588741657.jpg", "46_1588741666.jpg", "47_1588741676.jpg", "48_1588741687.jpg", "49_1588741697.jpg", "50_1588741707.jpg");
        $avatar = $avatars[array_rand($avatars)];
        $numeber = $this->creatInvCode();
        // 生成二维码
        // $photo = Code::c_qrcode('http://damaotou.xiaohongshu.mom/', time());
        $extends = [
            'imei' => input('imei'),
            'brand' => input('brand'),
            'model' => input('model'),
            'osName' => input('osName'),
            'number' => $numeber,
            'avatar' => '/mrtx/' . $avatar,
            // 'photo' => $photo,
        ];
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $t_number = input('t_number');
        if (!empty($t_number)) {
            $tuiguang_user = Usermodel::where('number', input('t_number'))->find();
            if (!$tuiguang_user) {
                $this->error('未找到此邀请码用户');
            }
        }
        $invite_code = Config::where('name', 'invite_code')->value('value');
        if ($invite_code == 1) {
            if (empty($t_number)) {
                $this->error('请填写分享人的邀请码！');
            }
        }

        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $ret = $this->auth->register($username, $password, '', $mobile, $extends);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            if (null !== input('t_number') && !empty(input('t_number'))) { //邀请码
                /*重写 libra 
                *    
                *填写邀请码时绑定上级用户信息，如果上级是普通用户再往上级找一级，所有上级都是普通用户时为系统
                */
                $tuiguang_user = Usermodel::where('number', input('t_number'))->find();

                $getParentid = $this->getParentid($tuiguang_user['id']);
                Usermodel::where('id', $data['userinfo']['id'])->update(['t_number' => $getParentid]);
                /**原始文件
                //代理设置
                $tuiguang_user = Usermodel::where('number', input('t_number'))->find();
                $beituiguang_user = Usermodel::where('id', $data['userinfo']['id'])->find();
                //如果推广人是代理,那么该新注册用户的上级就是代理,如推广人是某代理下的用户,那么该新注册用户的上级为某代理
                if($tuiguang_user['agent'] == 1){
                    //如果推广人是代理
                    Usermodel::where('id', $data['userinfo']['id'])->update(['t_number' => $tuiguang_user['id']]);
                }
                if($tuiguang_user['agent'] == 0 && $tuiguang_user['t_number'] != 0){
                    //如果推广人是用户,并且是某个代理下的
                    Usermodel::where('id', $data['userinfo']['id'])->update(['t_number' => $tuiguang_user['t_number']]);
                }
                
                //返利推广
                $this->tg(input('t_number'), $data['userinfo']['id']);
                **/
            }
            $this->success(__('Sign up successful'), $data, 200);
        } else {
            $this->error($this->auth->getError());
        }
    }
    /*
     *libra
     *无限极查找上级用户ID 
     */
    public function getParentid($uid)
    {
        $parent = Usermodel::where(['id' => $uid])->find();
        $parentid = 0;
        if ($parent['agent'] == 1) {
            $parentid = $parent['id'];
        } else {
            if ($parent['t_number'] > 0) {
                $parentid = $this->getParentid($parent['t_number']);
            }
        }
        return $parentid;
    }

    // 推广码
    public function creatInvCode()
    {
        $code = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $arr = [];
        for ($i = 0; $i < 6; $i++) {
            $arr[$i] = $code[mt_rand(0, 35)];
        }
        $code = implode('', $arr);
        $number = usermodel::where('number', $code)->find();
        if ($number) {
            $code = $this->creatInvCode();
        }
        return $code;
    }

    public function creatInvCode1()
    {
        $code = "1234567890";
        $arr = [];
        for ($i = 0; $i < 6; $i++) {
            $arr[$i] = $code[mt_rand(0, 9)];
        }
        $code = implode('', $arr);
        $number = usermodel::where('number', $code)->find();
        if ($number) {
            $code = $this->creatInvCode();
        }
        return $code;
    }

    // 个人信息
    public function personal()
    {
        $user = $this->auth->getUserinfo();
        $user = Usermodel::where('id', $user['id'])->find();
        $user['photo'] = Video::where(['user_id' => $user['id'], 'tong' => 1, 'class' => '2'])->count();
        $user['video'] = Video::where(['user_id' => $user['id'], 'tong' => 1, 'class' => '1'])->count();
        $user['fabu'] = Video::where(['user_id' => $user['id'], 'tong' => 1, 'status' => 0])->count();
        $arr = Config::where('name', 'like', '%' . 'integral' . '%')->column('value');

        if ($user['t_number'] > 0) {
            $parent = Usermodel::where('id', $user['t_number'])->find();
            // 不注释普通用户会出现登录不上的问题
            // $user['wechat']=$parent['wechat'];
        } else {
            $admin = AdminModel::where(['id' => 1])->find();
            // $user['wechat']=$admin['wechat'];
        }

        if ($user['integral'] < $arr[1] && $user['integral'] >= $arr[0]) {
            $user['vip'] = 0;
        }
        if ($user['integral'] < $arr[2] && $user['integral'] >= $arr[1]) {
            $user['vip'] = 1;
        }
        if ($user['integral'] >= $arr[2]) {
            $user['vip'] = 2;
        }
        $this->success('个人信息', $user, 200);
    }

    /**
     * 重置密码
     *
     * @param string $mobile 手机号
     * @param string $newpassword 新密码
     * @param string $captcha 验证码
     */
    public function resetpwd()
    {
        $type = $this->request->request("type");
        $mobile = $this->request->request("mobile");
        $email = $this->request->request("email");
        $newpassword = $this->request->request("newpassword");
        $captcha = $this->request->request("captcha");
        if (!$newpassword || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if ($type == 'mobile') {
            if (!Validate::regex($mobile, "^1\d{10}$")) {
                $this->error(__('Mobile is incorrect'));
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Sms::flush($mobile, 'resetpwd');
        } else {
            if (!Validate::is($email, "email")) {
                $this->error(__('Email is incorrect'));
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Ems::check($email, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Ems::flush($email, 'resetpwd');
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 修改密码
     */
    public function changepwd()
    {
        $oldpassword = $this->request->request("oldpassword");
        $newpassword = $this->request->request("newpassword");
        // 新密码长度
        if (strlen($newpassword) < 4 || strlen($newpassword) > 20) {
            $this->error("新密码必须介于4到20个字符之间！");
        }

        $ret = $this->auth->changepwd($newpassword, $oldpassword);
        if ($ret) {
            $this->success(__('Change password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    // 推广分享二维码
    public function qrcode()
    {
        $user = $this->auth->getUserinfo();
        $find = Usermodel::where('id', $user['id'])->field('avatar,photo,number')->find();
        $vipCard = db('vipcard')->where(['id' => 7])->find();

        $find['daili_money'] = floatval($vipCard['money']);

        $this->success('推广码', $find, 200);
    }

    // 个人信息修改
    public function edit(Request $request)
    {
        if (!$request->isPost()) {
            $this->error('ＯＰＴＩＯＮ　ＩＳ　ＮＯＴ');
        }
        $user = $this->auth->getUser();
        $find = Usermodel::get($user->id)->toArray();
        if (!$find) {
            $this->error('ＮＯ　ＯＮＥ');
        }
        $req = $request->post();
        $users = new Usermodel();
        $res = $users->save($req, ['id' => $user->id]);
        if ($res) {
            // 任务id2：更改头像和ID
            $res = Task::upload($user->id, 2);
            $this->success('修改成功', '', 200);
        } else {
            $this->error('系统错误', '', 100);
        }
    }

    // 我的余额
    public function money()
    {
        $res = $this->auth->getUserinfo();
        $this->result('余额', $res['money'], 200);
    }
    
}
