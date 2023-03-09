<?php

namespace app\admin\controller\community;

use app\admin\model\Comment;
use app\common\controller\Backend;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 问答
 *
 * @icon fa fa-circle-o
 */
class Ask extends Backend
{

    /**
     * Ask模型对象
     * @var \app\admin\model\Ask
     */
    protected $model = null;
    protected $searchFields = 'title';


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\ask\Ask;
        $list = ['待审核', '已通过', '已拒绝'];
        $this->assign('typeList', $list);

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
            $data = [];
            if (isset(input()['type'])) {
                if (input()['type'] == 'all') {
                    $data = [];
                } else {
                    $data = ['tong' => input()['type']];
                }
            }
            $total = $this->model
                // ->with('labels')
                ->where($where)
                ->where('class', 4)
                // 状态：已通过
                ->where('tong',1)
                ->where($data)
                ->order($sort, $order)
                ->count();
            // 待审核
            $list = $this->model
                // ->with('labels')
                ->where($where)
                ->where('class', 4)
                // 状态：已通过
                // ->where('tong',1)
                ->where($data)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();


            // foreach ($list as $row) {
            //     $row->visible(['id', 'name','avator_iamge','title', 'video', 'video_image', 'fabulous', 'stamp', 'browse', 'labels.name','comment']);

            // }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //添加
    public function add()
    {
        // $name = \app\admin\model\ask\Label::column('name');
        // $id = \app\admin\model\ask\Label::column('id');
        // for ($i = 0; $i < count($name); $i++) {
        //     $labels[$id[$i]] = $name[$i];
        // }
        // $this->assign('labels', $labels);
        // return $this->parent_add();
        return $this->parent_add();
    }
    
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
                    $all = suiji();
                    // 随机添加发布者名称
                    $params['name'] = $all['name'];
                    // 随机添加发布者头像
                    $params['avator_image'] = $all['image'];
                    // 随机添加发布者id
                    $params['user_id'] = $all['id'];
                    // 随机添加点赞量
                    $params['fabulous'] = mt_rand(10, 800);
                    // 随机添加浏览量
                    $params['browse'] = mt_rand(20, 10000);
                    // 随机添加评论数（取值范围）
                    $params['comment'] = mt_rand(0,10);
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
                            Comment::insert(['name' => $name, 'avator_image' => $photo, 'content' => $comment[$i], 'ask_id' => $result, 'class' => 4, 'tong' => 1, 'creat_time' => date('Y-d-m H:i:s'), 'zd' => 1,'level'=>mt_rand(0,2)]);
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
    
    // 编辑
    public function edit($ids = null)
    {
        $name = \app\admin\model\ask\Label::column('name');
        $id = \app\admin\model\ask\Label::column('id');
        for ($i = 0; $i < count($name); $i++) {
            $labels[$id[$i]] = $name[$i];
        }
        $this->assign('labels', $labels);
        return parent::edit($ids);
    }
    
    // 审核是否通过
    public function tong()
    {
        $id = input('ids');
        $tong = input('tong');
        $find = $this->model->where('id', $id)->find();
        if ($find['tong'] == '1') {
            return json(['code' => 2, 'msg' => '审核已通过,不能进行操作']);
        }
        if ($find['tong'] == '2') {
            return json(['code' => 2, 'msg' => '审核已通过,不能进行操作']);
        }
        if ($find['tong'] == '0' && $tong == '1') {
            $update = $this->model->where('id', $id)->update(['tong' => $tong]);
            if ($update) {
                return json(['code' => 1, 'msg' => '审核已通过']);
            } else {
                return json(['code' => 2, 'msg' => '系统错误']);

            }
        }
        if ($find['tong'] == '0' && $tong == '2') {
            $update = $this->model->where('id', $id)->update(['tong' => $tong]);
            if ($update) {
                return json(['code' => 1, 'msg' => '审核已拒绝']);
            } else {
                return json(['code' => 2, 'msg' => '系统错误']);

            }
        }
    }
    
}
