<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/21
 * Time: 23:29
 */

namespace app\admin\model;
use think\Model;
use think\Db;
class Brand extends Model
{
    protected $pk = 'brand_id';
    protected $autoWriteTimestamp = true;
    protected $field = ['brand_name', 'url', 'logo', 'sort_order', 'brand_desc', 'cat_name', 'parent_cat_id', 'cat_id', 'is_hot'];

    public function getSortBrand($cat_id=0)
    {
        $brand_where = [];
        if ($cat_id) {
            $brand_where['cat_id|parent_cat_id'] = $cat_id;
        }
        $brand_list = Db::name('brand')->where($brand_where)->column('brand_id, brand_name, parent_cat_id, cat_id');
        // 查询在双重品牌的id
        $query = Db::name('brand')->group('brand_name')->having("COUNT('brand_id')>1")->fetchSql()->column('brand_name');
        $brand_ids = Db::name('brand')->where($brand_where)->where('brand_name', 'exp', ' in ('.$query.')')->column('brand_id, cat_id');
        $cate_list = Db::name('category')->column('cat_id, cat_name');
        $name_list = [];
        foreach($brand_list as $k=>$v) {
            $name = getFirstCharter($v['brand_name']) . ' -- ' . $v['brand_name'];
            if (array_key_exists($v['brand_id'], $brand_ids) && $v['cat_id']) {
                // 双重品牌的加上分类名称
                $name .= '('.$cate_list[$v['cat_id']].')';
            }

            $name_list[] = $v['brand_name'] = $name;
            $brand_list[$k] = $v;
        }

        array_multisort($name_list, SORT_ASC,SORT_STRING, $brand_list);
        return $brand_list;
    }
}