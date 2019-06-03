<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/12
 * Time: 16:32
 */

namespace app\admin\controller;
use think\Request;

class Error
{
    public function index(Request $request) {
        $controllerName = $request->controller();

        return $controllerName . "模块未完成";
    }

    public function _empty(Request $request, $name) {
        $controllerName = $request->controller();
        return $controllerName . '模块的' .$name . '方法未完成';
    }
}