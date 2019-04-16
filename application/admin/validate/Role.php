<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/8
 * Time: 21:37
 */

namespace app\admin\validate;
use think\Validate;

class Role extends Validate
{
    protected $rule = [
        'role_name' => 'require|max:60',
        //'right'  => 'require'
    ];

    protected $message = [
        'role_name.require' => '角色名称不能为空',
        'role_name.max'     => '角色名称最多不能超过60个字符',
        'right'          => '请选择权限'
    ];
}