<?php

namespace app\admin\controller\software;

use app\admin\model\Comment;
use app\admin\model\Subordinate;
use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 软件管理

 *
 * @icon fa fa-circle-o
 */
class Software extends Backend
{
    
    /**
     * Software模型对象
     * @var \app\admin\model\software\Software
     */
    protected $model = null;
    protected $searchFields = 'title';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\software\Software;

    }
    
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
                ->with(['subordinate','belong'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['subordinate','belong'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {

                $row->getRelation('subordinate')->visible(['name']);

            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function add()
    {
        //分类
        // $classes = ['1'=>'请选择'];
        // $name = \app\admin\model\software\Softwaresub::column('name');
        // $id = \app\admin\model\software\Softwaresub::column('id');
        // for ($i = 0; $i < count($name); $i++) {
        //     $classes[$id[$i]] = $name[$i];
        // }
        // $this->assign('class', $classes);
    
        // 所属
        $belong = ['0'=>'请选择'];
        $name = \app\admin\model\software\Softwarebel::column('name');
        $id = \app\admin\model\software\Softwarebel::column('id');
        for ($i = 0; $i < count($name); $i++) {
            $belong[$id[$i]] = $name[$i];
        }
        $this->assign('belong', $belong);
        
        // 收费方式
        $istoll = ['1'=>'免费','2'=>'付费'];
        $this->assign('istoll', $istoll);
        
        // 状态
        $status = ['y'=>'上架中','n'=>'已下架'];
        $this->assign('status', $status);
        
        return $this->parent_add();
    }

    // 添加提交后随机添加
    public function parent_add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");


            if ($params) {
                $params = $this->preExcludeFields($params);

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
                    // 随机添加赞数（取值范围）
                    $params['hits'] = mt_rand(30,999);
                    // 随机添加踩数（取值范围）
                    $params['cai'] = mt_rand(1,20);
                    // 随机添加评论数（取值范围）
                    // $params['comments'] = mt_rand(1,19);
                    // 随机添加浏览量（取值范围）
                    $params['browse'] = mt_rand(500,89999);
                    $result = $this->model->allowField(true)->insertGetId($params);
                    if (isset(input()['comments'])) {
                        $comment = input()['comments'];
                        $size = count($comment);
                        for ($i = 0; $i < $size; $i++) {
                            $name = file_get_contents('name.txt');//将整个文件内容读入到一个字符串中
                            $name = json_decode(mb_convert_encoding($name, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                            $name = $name[array_rand($name)];
                            $photo = file_get_contents('photo.txt');//将整个文件内容读入到一个字符串中
                            $photo = json_decode(mb_convert_encoding($photo, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5'));//转换字符集（编码）
                            $photo = $photo[array_rand($photo)];
                            Comment::insert(['name' => $name, 'avator_image' => $photo, 'content' => $comment[$i], 'community_id' => $result, 'class' => 3, 'creat_time' => date('Y-d-m H:i:s')]);
                        }
                    }
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

    public function edit($ids = null)
    {
        //分类
        // $classes = ['0'=>'请选择'];
        // $classes = ['1'=>'请选择'];
        // $name = \app\admin\model\software\Softwaresub::column('name');
        // $id = \app\admin\model\software\Softwaresub::column('id');
        // for ($i = 0; $i < count($name); $i++) {
        //     $classes[$id[$i]] = $name[$i];
        // }
        // $this->assign('class', $classes);
        
        // 所属
        $belong = ['0'=>'请选择'];
        $name = \app\admin\model\software\Softwarebel::column('name');
        $id = \app\admin\model\software\Softwarebel::column('id');
        for ($i = 0; $i < count($name); $i++) {
            $belong[$id[$i]] = $name[$i];
        }
        $this->assign('belong', $belong);
        
        // 收费方式
        $istoll = ['1'=>'免费','2'=>'付费'];
        $this->assign('istoll', $istoll);
        
        // 状态
        $status = ['y'=>'上架中','n'=>'已下架'];
        $this->assign('status', $status);
        
        return parent::edit($ids);
    }

}
