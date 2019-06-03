<?php

/**
 * 前台首页控制器
 */
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $goodsLogic = $this->app->model('goodsLogic', 'logic');
        // 热门商品
        $hot_goods = $goodsLogic->getGoodsByWhere('index_hot_goods', ['a.is_hot'=>1, 'a.is_on_sale'=>1]);
        // 推荐商品
        $recommend_goods = $goodsLogic->getGoodsByWhere('index_recommend_goods', ['a.is_recommend'=>1, 'a.is_on_sale'=>1]);
        // 商品分类
        $goods_category_tree = $goodsLogic->getGoodsCategoryTree();

        $hot_cat_goods = [];
        foreach ($goods_category_tree as $key=>$val) {
            if ($val['is_hot'] == 1) {
                $val['hot_goods'] = isset($hot_goods[$val['cat_id']])? $hot_goods[$val['cat_id']] : [];
                $val['recommend_goods'] = isset($recommend_goods[$val['cat_id']])? $recommend_goods[$val['cat_id']] : [];
                $hot_cat_goods[] = $val;
            }
        }
        //dump($hot_cat_goods);
        //$this->assign('goods_category_tree', $goods_category_tree);
        $this->assign('hot_cat_goods', $hot_cat_goods);

        return $this->fetch();
    }


}
