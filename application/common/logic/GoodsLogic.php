<?php
/**
 * 商品相关操作逻辑
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/10
 * Time: 15:33
 */

namespace app\common\logic;
use think\facade\Cache;
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

    // 获取前台商品分类
    public function getGoodsCategoryTree()
    {
        $tree = [];
        $cat_list = Db::name('category')->where('is_show', 1)->order('sort_order')->select();
        if ($cat_list) {
            $two_arr = [];
            $third_arr = [];
            $i = 0;
            foreach($cat_list as $key=>$val) {
                $i++;
                if ($val['level'] == 1) {
                    $tree[$val['cat_id']] = $val;
                }
                if ($val['level'] == 2) {
                    $two_arr[$val['parent_id']][] = $val;
                }

                if ($val['level'] == 3) {
                    $third_arr[$val['parent_id']][] = $val;
                }
            }

            foreach($two_arr as $k=>$v) {
                foreach($v as $kk=>$vv) {
                    $i++;
                    $two_arr[$k][$kk]['sub_menu'] = isset($third_arr[$vv['cat_id']])? $third_arr[$vv['cat_id']] : [];
                }
            }

            foreach ($tree as $key=>$val) {
                $i++;
                $val['sub_menu'] = isset($two_arr[$val['cat_id']])? $two_arr[$val['cat_id']] : [];
                $tree[$key] = $val;
            }
        }

        return $tree;
    }

    /**
     * 在有价格阶梯的情况下，根据商品数量，获取商品价格
     * @param $goods_num|购买的商品数
     * @param $goods_price|商品默认单价
     * @param $price_ladder|价格阶梯数组
     * @return mixed
     */
    public function getGoodsPriceByladder($goods_num, $goods_price, $price_ladder)
    {
        if (empty($price_ladder)) {
            return $goods_price;
        }

        $price_ladder = array_values(array_sort($price_ladder, 'amount', 'asc'));
        //dump($price_ladder);
        $count = count($price_ladder);
        for ($i=0; $i<$count; $i++) {
            if ($i == 0 && $goods_num < $price_ladder[$i]['amount']) {
                return $goods_price;
            }
            if ($goods_num >= $price_ladder[$i]['amount'] && $goods_num < $price_ladder[$i+1]['amount']) {
                return $price_ladder[$i]['price'];
            }

            if ($i == ($count-1)) {
                return $price_ladder[$i]['price'];
            }
        }


    }

    //获取指定条件的按分类分组好的商品
    public function getGoodsByWhere($cache_key, $where=[])
    {
        if (cache::has($cache_key)) {
            return Cache::get($cache_key);
        } else {
            $goods = Db::name('goods')
                ->alias('a')
                ->field('a.goods_id, a.goods_name, a.shop_price, a.market_price, a.cat_id, b.parent_id_path, b.cat_name')
                ->join(config('database.prefix').'category b', 'a.cat_id=b.cat_id', 'left')
                ->where($where)
                ->order('a.sort_order')
                ->select();

            $_goods = [];
            if (count($goods) > 0) {
                foreach ($goods as $k=>$v) {
                    $cat_path = explode('_', $v['parent_id_path']);
                    $_goods[$cat_path[1]][] = $v;
                }
                Cache::set($cache_key, $_goods, 600);
            }

            return $_goods;
        }
    }
}