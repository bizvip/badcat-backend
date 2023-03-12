<?php

namespace app\admin\model;

use think\Model;

final class AuthGroup extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    public function getNameAttr($value, $data)
    {
        return __($value);
    }

}
