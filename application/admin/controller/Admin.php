<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25
 * Time: 11:19
 */

namespace app\admin\controller;
use think\Db;

class Admin extends Base
{
    public function login()
    {
        dump(app()->getAppPath());
        return $this->fetch();
    }
}