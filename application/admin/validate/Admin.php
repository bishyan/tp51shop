<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10
 * Time: 22:00
 */

namespace app\admin\validate;
use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'admin_name' => 'require|max:60',
        'email' => 'require|max:60|email',
        'password'   => 'require|length:6, 12|alphaDash',
    ];

    protected $message = [
        'admin_name.require' => '用户名不能为空',
        'admin_name.max'     => '用户名最多不能超过60个字符',
        'email.require'      => 'email不能为空',
        'email.max'          => 'email最多不能超过60个字符',
        'email.email'        => 'email格式不正确',
        'password.require'   => '密码不能为空',
        'password.length'    => '密码必须为6-12位的字母+数字的组合',
        'password.alphaDash' => '密码必须为6-12位的字母+数字的组合'
    ];

    protected $scene = [
        'insert' => ['admin_name', 'email', 'password'],
        'login'  => ['admin_name'=>'require', 'password'=>'require'],
        'update' => ['admin_name', 'email', 'password'=>'length|alphaDash'],
    ];
}