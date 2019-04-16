<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25
 * Time: 11:21
 */

namespace app\admin\controller;
use think\Controller;

class Base extends Controller
{
    protected $batchValidate = true;

    protected function initialize() {
        define('CONTROLLER_NAME', $this->request->controller());
        define('ACTION_NAME', $this->request->action());
        //echo ACTION_NAME; EXIT;
        $on_need_login = ['verify', 'login'];
        if (in_array(ACTION_NAME, $on_need_login)) {
            return;
        } else {
            if (session('?admin_id')) {
                $this->checkPrivilege();
            } else {
                $this->error('请先登陆！', url('/admin/admin/login'));
            }
        }
    }

    protected function checkPrivilege() {
        $act_list = session('act_list');
        // 超级管理员和首页控制器， 不需要检查权限
        if ($act_list == 'all' || CONTROLLER_NAME == 'Index') {
            return true;
        } else {
            $right = db('system_menu')->where('id', 'in', $act_list)->column('right');
            $right_str = implode(',', $right);

            if (strpos($right_str, CONTROLLER_NAME . '@' . ACTION_NAME) === false) {
                $this->error('您没有访问' . CONTROLLER_NAME . '@' . ACTION_NAME . '的权限', url('/admin/Index/welcome'));
            }
            //dump($right_str);
        }
        //echo CONTROLLER_NAME; EXIT;
    }

    /** 验证数据
     * @param $validate_name  验证器的名称
     */
    protected function validateData($validate_name)
    {
        $res = $this->validate(input('post.'), $validate_name);
        return $res;
    }
}