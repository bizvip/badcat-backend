<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 后台发布者
 *
 * @icon fa fa-circle-o
 */
class Publisher extends Backend
{
    
    /**
     * Publisher模型对象
     * @var \app\admin\model\Publisher
     */
    protected $model = null;
    // 搜索字段
    protected $searchFields = 'name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Publisher;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
     
    // /**
    //  * 查看
    //  */
    // public function index()
    // {
    //     //当前是否为关联查询
    //     $this->relationSearch = false;
    //     //设置过滤方法
    //     $this->request->filter(['strip_tags', 'trim']);
    //     if ($this->request->isAjax())
    //     {
    //         //如果发送的来源是Selectpage，则转发到Selectpage
    //         if ($this->request->request('keyField'))
    //         {
    //             return $this->selectpage();
    //         }
    //         list($where, $sort, $order, $offset, $limit) = $this->buildparams();
    //         $total = $this->model

    //                 ->where($where)
    //                 ->order($sort, $order)
    //                 ->count();

    //         $list = $this->model

    //                 ->where($where)
    //                 ->order($sort, $order)
    //                 ->limit($offset, $limit)
    //                 ->select();

    //         foreach ($list as $row) {
    //             $row->visible(['name','image','fensi','guanzhu','level']);

    //         }
    //         $list = collection($list)->toArray();
    //         $result = array("total" => $total, "rows" => $list);

    //         return json($result);
    //     }
    //     return $this->view->fetch();
    // }
    
    // // 添加
    // public function add()
    // {
    //     if ($this->request->isPost()) {
    //         $params = $this->request->post("row/a");
    //         if ($params) {
    //             $params = $this->preExcludeFields($params);

    //             if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
    //                 $params[$this->dataLimitField] = $this->auth->id;
    //             }
    //             $result = false;
    //             Db::startTrans();
    //             try {
    //                 //是否采用模型验证
    //                 if ($this->modelValidate) {
    //                     $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
    //                     $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
    //                     $this->model->validateFailException(true)->validate($validate);
    //                 }
    //                 // 随机添加粉丝数（取值范围）
    //                 $params['fensi'] = mt_rand(3999,99999);
    //                 // 随机添加关注数（取值范围）
    //                 $params['guanzhu'] = mt_rand(20,100);
    //                 // 随机添加等级数（取值范围）
    //                 $params['level'] = mt_rand(0,2);
    //                 $result = $this->model->allowField(true)->save($params);
    //                 Db::commit();
    //             } catch (ValidateException $e) {
    //                 Db::rollback();
    //                 $this->error($e->getMessage());
    //             } catch (PDOException $e) {
    //                 Db::rollback();
    //                 $this->error($e->getMessage());
    //             } catch (Exception $e) {
    //                 Db::rollback();
    //                 $this->error($e->getMessage());
    //             }
    //             if ($result !== false) {
    //                 $this->success();
    //             } else {
    //                 $this->error(__('No rows were inserted'));
    //             }
    //         }
    //         $this->error(__('Parameter %s can not be empty', ''));
    //     }
    //     return $this->view->fetch();
    // }
    
    // 添加
    // public function add($ids = null)
    // {
    //     // 性别
    //     $gender = ['0'=>'男','1'=>'女'];
    //     $this->assign('gender', $gender);
        
    //     return $this->view->fetch();
    // }
    
    // 编辑
    public function edit($ids = null)
    {
        // 性别
        $gender = ['0'=>'男','1'=>'女'];
        $this->assign('gender', $gender);
        
        return parent::edit($ids);
    }

}
