<?php

namespace app\common\model;

use think\Model;

class UserRule extends Model
{

    // 表名
    protected $name = 'user_rule';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 追加属性
    protected $append = [
    ];

}
