<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/16
 * Time: 13:00
 */

namespace app\admin\controller;


class System extends Base
{
    public function cleanCache()
    {
        $res = delFile(app()->getRuntimePath());
        if ($res) {
            $script = "<script>parent.layer.msg('缓存清除成功', {time: 2000, icon: 1});window.location='/admin/Index/welcome';</script>";
        } else {
            $script = "<script>parent.layer.msg('缓存清除失败', {time: 2000, icon: 1});window.location='/admin/Index/welcome';</script>";
        }
        //dump($res);
        exit($script);
    }
}