<?php

namespace app\admin\controller\exchange;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
//修改最大执行时间
ini_set('max_execution_time','300');
//修改此次最大运行内存
ini_set('memory_limit','128M');
set_time_limit('300');
/**
 *
 *
 * @icon fa fa-exchange
 */
class Exchange extends Backend
{

    /**
     * Exchange模型对象
     * @var \app\admin\model\Exchange
     */
    protected $model = null;
    // 搜索字段
    protected $searchFields = 'code';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Exchange;
        $this->view->assign("classList", $this->model->getClassList());
        $this->view->assign("listList", $this->model->getListList());
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $total = $this->model
                ->with(['user'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['user'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {
                if($row->use_time>0){
                    $row->use_time=date('Y-m-d H:i', $row->use_time);
                }else{
                    $row->use_time='';
                }
                $row->getRelation('user')->visible(['mobile']);
                $row->getRelation('user')->visible(['username']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function add()
    {
        // ini_set('max_execution_time','0');
        if ($this->request->isPost()) {
            $tiao = $this -> request -> post();
            $tiaos = $tiao['tiao'];
            $params = $this->request->post("row/a");
            if($tiaos > 0 && $tiaos != ''){
                $code = $params['code'];
                // var_dump($tiao['note']);exit;
                if($code != ''){
                    // echo 333;exit;
                    // 激活码
                    $data['code'] = $code;
                    // 备注
                    $data['note'] = $tiao['note'];
                    // 兑换种类
                    $data['class'] = $params['class'];
                    // 状态
                    $data['list'] = $params['list'];
                    // 添加时间是
                    $data['create_time'] = $params['create_time'];
                    for($i = 0; $i < $tiaos; $i++){
                        $this->model->insert($data);
                    }
                    $this->success();
                } else {
                    $data = [];
                    $s = '';
                    // $q = 0;
                    $a = 'abckef1234567890ghijklmnopqrst1234567890uvwxyzA1234567890BCDEFGHI1234567890JKLMNOPQRSTUVWXYZ1234567890';
                    $chang = strlen($a) - 1;
                    
                    $data['note'] = $tiao['note'];
                    $data['class'] = $params['class'];
                    $data['list'] = $params['list'];
                    $data['create_time'] = $params['create_time'];
                    for($c = 0; $c < $tiaos; $c++){
                        // $q++;
                        $str = '';
                        // for($i = 0;$i < 12; $i++){
                        $str .= $a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)].$a[mt_rand(0, $chang)];
                        // }
                        $data['code'] = $str;
                        //$s .= '("'.$tiao['note'].'","'.$params['class'].'","'.$params['list'].'","'.$params['create_time'].'","'.$str.'"),';
                        $this->model->insert($data);
                        //db::table('bc_exchange')->insert($data);
                    }
                    //$as = Db::execute('insert into bc_exchange(note, class, list, create_time, code) values'.trim($s, ','));
                    // echo 11;exit;
                    // var_dump($s);exit;
                    // if($as){
                        $this->success();
                    // }
                }
            }
            
            if ($params) {
                $params = $this->preExcludeFields($params);
                $find = db::table('bc_exchange')->where('code', $params['code'])->find();
                if ($find) {
                    $this->error(__('激活码已存在'));
                }

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }
    
    public function piadd(){
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $find = db::table('bc_exchange')->where('code', $params['code'])->find();
                if ($find) {
                    $this->error(__('激活码已存在'));
                }

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }
}
