<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/16
 * Time: 16:26
 */

namespace app\index\controller;
use think\Controller;
use app\admin\model\Goods as GoodsModel;

class Goods extends Controller
{
    // 判断商品是否参加了某项活动, 并返回商品信息
    public function activity()
    {
        $goods_id = input('goods_id/d');
        $item_id = input('item_id/d');
        $goods_num = input('goods_num/d');
        $goodsModel = new GoodsModel();
        $goods = $goodsModel::get($goods_id);
        $goods['activity_is_on'] = 0;
        if (!empty($goods['price_ladder'])) {
            $goods['shop_price'] = $this->app->model('goods_logic', 'logic')->getGoodsPriceByladder($goods_num, $goods['shop_price'], $goods['price_ladder']);
        }
        ajaxReturn(['status'=>1, 'msg'=>'这件商品没有参与活动', 'result'=>['goods'=>$goods]]);
    }

    public function dispatching()
    {
        return ['status'=>1, 'msg'=>'kkk'];
    }


    public function goodsInfo($goods_id)
    {
        $goodsModel = new GoodsModel();
        $goods = $goodsModel->where(['goods_id'=>$goods_id, 'is_on_sale'=>1])->find();
        if (empty($goods)) {
            $this->error('商品不存在', url('/index/index/index'));
        } else {
            if (($goods['is_virtual']) == 1 && ($goods['virtual_indate'] <= time())) {
                $this->error('商品不存在或已下架', url('/index/index/index'));
            }

            $goods->setInc('click_count');

            $goods['goods_desc'] = htmlspecialchars_decode($goods['goods_desc']);
            $this->assign('goods', $goods);
        }

        return $this->fetch();
    }

    public function combination()
    {
        $goods_id = input('goods_id/d');
        $item_id = input('item_id/d');
        dump($item_id);
        dump($goods_id);
        if (empty($goods_id)) {
            ajaxReturn(['status'=>0, 'msg'=>'参数错误']);
        }
        $combination= $this->app->model('Combination', 'logic');
    }
}