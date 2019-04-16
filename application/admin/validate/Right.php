<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 21:56
 */

namespace app\admin\validate;
use think\Validate;

class Right extends Validate
{
    protected $rule = [
        'name' => 'require|max:60',
        'right' => 'require'
    ];

    protected $message = [
        'name.require' => '权限资源名称不能为空',
        'name.max'     => '权限资源名称最多不能超过60个字符',
        'right.require'=> '请添加权限码'
    ];

    //protected $scene = [];
}