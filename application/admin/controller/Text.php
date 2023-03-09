<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Text extends Backend
{

    /**
     * Text模型对象
     * @var \app\admin\model\Text
     */
    protected $model = null;
    // 搜索字段
    protected $searchFields = 'text';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Text;

    }
    
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    // 添加 
    public function add()
    {
        // if ($this->request->isPost()) {
        //     $params = input('row/a');
        //     foreach ($params['content'] as $key => $item) {
        //         if ($item['text'] == '') {
        //             unset($params['content'][$key]);
        //         }
        //     }
        //     $res = $this->model->saveAll($params['content']);
        //     if ($res) {
        //         $this->success('添加成功');
        //     } else {
        //         $this->error('添加失败');
        //     }
        // }
        // 所属
        $class = ['0'=>'社区短文','1'=>'社区图片','2'=>'社区ASMR','3'=>'影视','4'=>'社区回答','5'=>'短视频','6'=>'吃瓜专区'];
        $this->assign('class', $class);
        
        // return $this->view->fetch();
        return parent::add();
    }
    
    // 编辑
    public function edit($ids = null)
    {
        $class = ['0'=>'社区短文','1'=>'社区图片','2'=>'社区ASMR','3'=>'影视','4'=>'社区回答','5'=>'短视频','6'=>'吃瓜专区'];
        $this->assign('class', $class);
        
        return parent::edit($ids);
    }


}
