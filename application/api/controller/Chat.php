<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Config;
use think\Controller;
use think\Request;

class Chat extends Api
{
    protected $noNeedLogin = ['*'];
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //在线客服
        $user = $this->auth->getUser();
        $res = Config::where('name','chat')->value('value');
        if($user['parentid']>0){
           $parentid=\app\common\model\User::where(['id'=>$user['parentid']])->find();
           if(!empty($parentid['weichat'])){
               $res='http://wpa.qq.com/msgrd?v=3&uin='.$parentid['weichat'].'&site=qq&menu=yes/';
           }
        }
       
        $this->success('chat',$res,200);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
