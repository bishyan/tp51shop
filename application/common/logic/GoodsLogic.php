<?php
/**
 * 商品相关操作逻辑
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/10
 * Time: 15:33
 */

namespace app\common\logic;
use think\Model;
use think\Db;

class GoodsLogic extends Model
{

    /**
     * 获取选中指定分类的家谱树
     * @param $cat_id
     * @return array
     */
    public function find_parent_cat($cat_id)
    {
        if ($cat_id == null) {
            return [];
        }

        $cat_list = Db::name('category')->column('cat_id, parent_id, level');
        if (isset($cat_list[$cat_id])) {
            $cat_level_arr[$cat_list[$cat_id]['level']] = $cat_id;

            // 循环查找上级分类
            $parent_id = $cat_list[$cat_id]['parent_id'];
            while($parent_id > 0) {
                $cat_level_arr[$cat_list[$parent_id]['level']] = $parent_id;
                $parent_id = $cat_list[$parent_id]['parent_id'];
            }

            return $cat_level_arr;
        } else {
            return [];
        }
    }
}