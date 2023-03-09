<?php

namespace app\admin\model\software;

use think\Model;


class Sub extends Model
{


    // 表名
    protected $name = 'softwaresub';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $resultSetType = 'collection';
    // 追加属性
    protected $append = [

    ];



}
