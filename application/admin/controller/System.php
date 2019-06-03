<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/16
 * Time: 13:00
 */

namespace app\admin\controller;


use think\facade\Cache;

class System extends Base
{
    /**
     *  清除缓存
     */
    public function cleanCache()
    {
        $res = delFile(app()->getRuntimePath());
        if ($res) {
            $script = "<script>parent.layer.msg('缓存清除成功', {time: 2000, icon: 1});window.location='/admin/Index/welcome';</script>";
        } else {
            $script = "<script>parent.layer.msg('缓存清除失败', {time: 2000, icon: 1});window.location='/admin/Index/welcome';</script>";
        }

        exit($script);
    }

    /**
     * 清除商品的缩略图
     */
    public function clearGoodsThumb()
    {
        $goods_id = input('goods_id/d', '');
        if (empty($goods_id)) {
            ajaxReturn(['msg'=>'参数错误']);
        } else {
            delFile(getcwd(). '/upload/goods/thumb/' . $goods_id);
            //Cache::clear('original_img_cache');
            ajaxReturn(['msg'=>'缓存清除成功']);
        }
    }
}