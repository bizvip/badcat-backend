<?php


namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\Config;
use think\Request;
use app\admin\model\User as UserModel;

use think\Log;

class Userupload extends Api
{
    protected $noNeedLogin = ['*'];

    // 上传图片（编辑器）
    public function upload_img(Request $request)
    {
        if ( ! $request->isPost()) {
            $this->error('ＤＯＮ＇Ｔ　ＧＥＴ');
        }
        $files = $_FILES;
        $imageArr = array();
        foreach ($files as $file) {
            $imageName = $file['name'];
            //后缀名
            $ext = strtolower(substr(strrchr($imageName, '.'), 1));
            //保存文件名
            $fileName = uniqid();
            $tmp = $file['tmp_name'];
            //保存 = 路径 + 文件名 + 后缀名
            $imageSavePath = ROOT_PATH . 'public' . DS . 'upload/user/images/new_file/' . $fileName . '.' . $ext;
            $info = move_uploaded_file($tmp, $imageSavePath);
            if ($info) {
                $path = config('host') . "/upload/user/images/new_file/" . $fileName . '.' . $ext;
                array_push($imageArr, $path);
            }
        }
        //最终生成的字符串路径
        $imagePathStr = implode(',', $imageArr);
        $this->success('ok', $imagePathStr, 200);
    }

}
