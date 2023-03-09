<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;
use app\admin\model\AdminLog;

/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class User extends Backend
{

    protected $relationSearch = true;
    

    /**
     * @var \app\admin\model\User
     */
    protected $model = null;
    // 搜索字段
    // protected $searchFields = 'username';
    protected $searchFields = 'username,mobile';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('User');
    }

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                // ->with('group')
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                // ->with('group')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => &$v) {
                $v->hidden(['password', 'salt']);
                if($v['bio']==null){
                    $v['bio'] = '无签名';
                }
                if($v['email']==null){
                    $v['email'] = '未绑定';
                }
                if($v['vip_time']<=date('Y-m-d H:i:s')){
                    $v['vip_time'] = '无会员';
                }
                if($v['brand']==null){
                    $v['brand'] = '未知';
                }
                if($v['model']==null){
                    $v['model'] = '未知';
                }
                if($v['osName']==null){
                    $v['osName'] = '未知';
                }
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->view->assign('groupList', build_select('row[group_id]', \app\admin\model\UserGroup::column('id,name'), $row['group_id'], ['class' => 'form-control selectpicker']));
        return parent::edit($ids);
    }
    
    public function daili($ids = null,$username,$number)
    {
        if ($this->request->isPost())
        {
            $data = input();
            $adminid = $this->auth->id;
    		$jiage = db('vipcard')->where('name','代理商')->value('money');
    		//$id =$data['id'];
    // 		if(session('power')=='1' && $data['power']=='1')
    //             {
    //                 $money  =   db('user')->where('id='.session('user'))->value('money');
    //                 if($money < $jiage)
    //                 {
    //                     Session::flash('code','err3');
    //                     //$this->redirect('vip/update', ['id'=>$data['id']]);
    //                     return json(['code'=>'0','msg'=>'开通失败,您的余额不足']);
    
    //                 }else{
    //                     //$insert['parentid'] =   session('user');
    //                     $insert['power']    =   1;
    //                 }
    //             }
                

                
                    $insert['agent']   =   1;
                    $insert['number']   =   $number;
                    $insert['weichat']   =   $data['weichat'];
                  	$insert['fencheng']   =   $data['fencheng'];
                    $sha_count = db('user')->where('id!='.$ids.' and number="'.$insert['number'].'"')->count();
                    
                    if($sha_count>0)
                    {
                        Session::flash('code','err4');
                        return json(['code'=>'0','msg'=>'开通失败,存在相同的推荐码']);
                    }
                    
                $insert['jointime'] = time();
    
    
                $count = db('user')->where('username="'.$username.'" and id != '.$ids)->count();
                if($count>0)
                {
                    return json(['code'=>'0','msg'=>'开通失败,用户名重复']);
                }
                
                if(db('user')->where('id',$ids)->update($insert))
                {
                    
                    $data1['uid']    =   $this->auth->id;
                    $data1['ctime']  =   time();
                    $data1['cid']    =   $ids;
                    db('kai')->insert($data1);
                    //db('kai')->insert(['uid'=>133,'cid'=>$ids,'ctime'=>time()]);
                    $this->success('开通代理成功');
                    //Session::flash('code','2');
                    //return json(['code'=>'1']);
                }else{
                    Session::flash('code','err2');
                    return json(['code'=>'0','msg'=>'系统异常,请稍后重试']);
                };
                
        }
        //return json(['code' => 2, 'msg' => $ids]);
        return $this->view->fetch();

        
    }
    
    
    public function canceldaili()
    {
        $user_id = input('user_id');
    }

}
