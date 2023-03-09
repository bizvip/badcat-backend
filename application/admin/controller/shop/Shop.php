<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Shop extends Backend
{
    
    /**
     * Shop模型对象
     * @var \app\admin\model\Shop
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Shop;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function addcard()
    {
        $belong = [];
        $cardname = \app\admin\model\Vipcard::column('name');
        $cardid = \app\admin\model\Vipcard::column('id');
        for ($i = 0; $i < count($cardname); $i++) {
            $belong[$cardid[$i]] = $cardname[$i];
        }
        $this->assign('belong', $belong);
        if ($this->request->isPost())
        {
                $insert['card_id'] = input('cardid');
                $insert['title'] = input('title');
                $insert['picurl'] = input('picurl');
                $insert['miaoshu'] = input('miaoshu');
                $insert['mone'] = input('mone');
                $insert['yue'] = input('yue');
                $insert['jifen'] = input('jifen');
                $insert['type'] = 1;
                // $list = \app\admin\model\Shop::column('id');
                // return json(['code' => 2, 'msg' => $list]);
                $model = new \app\admin\model\Shop;
                $model->insert(['card_id' => input('cardid'),'title' => input('title'),'picurl' => input('picurl'),'miaoshu' => input('miaoshu'),'mone' => input('mone'),'yue' => input('yue'),'jifen' => input('jifen'),'type' => 1]);

                $this->success('添加成功');
            //return json(['code' => 2, 'msg' => input('title')]);
            
        }
        return $this->view->fetch();


    }

}
