<?php
/**
 * 商品控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/27
 * Time: 20:57
 */

namespace app\admin\controller;
use think\Db;
use app\admin\controller\Category;

class Goods extends Base
{
    public function goodsList()
    {
        $cat_list = $this->app->model('category')->getSortCategory();
        $brand_list = $this->app->model('brand')->getSortbrand();
        $goods_list = $this->app->model('Goods')->getGoodsList(5);

        $this->assign('cat_list', $cat_list);
        $this->assign('brand_list', $brand_list);
        $this->assign('goods_list', $goods_list);

        return $this->fetch();
    }

    public function addGoods()
    {
        // 获取顶级分类
        $cats = Db::name('category')->field('cat_id, cat_name')->where('parent_id', 0)->select();
        // 获取品牌
        $brands = $this->app->model('brand')->getSortbrand();
        // 获取模型
        $types = Db::name('goods_type')->field('type_id, type_name')->where('1=1')->select();

        $this->assign('brands', $brands);
        $this->assign('cats', $cats);
        $this->assign('types', $types);

        return $this->fetch('goods_info');
    }

    public function editGoods($goods_id)
    {
        $goods_info = $this->app->model('goods')->find($goods_id);
        //dump($goods_info);
        if(is_null($goods_info)) {
            $this->error('参数错误');
        } else {
            // 获取顶级分类
            $cats = Db::name('category')->field('cat_id, cat_name')->where('parent_id', 0)->select();
            // 获取品牌
            $brands = $this->app->model('brand')->getSortbrand();
            // 获取模型
            $types = Db::name('goods_type')->field('type_id, type_name')->where('1=1')->select();
            // 获取选中的分类
            $selected_cat = $this->app->model('GoodsLogic', 'logic')->find_parent_cat($goods_info['cat_id']);
            // 商品相册
            $goods_images = Db::name('goods_image')->where('goods_id', $goods_info['goods_id'])->select();

            $this->assign('goods_images', $goods_images);
            $this->assign('selected_cat', $selected_cat);
            $this->assign('goods_info', $goods_info);
            $this->assign('brands', $brands);
            $this->assign('cats', $cats);
            $this->assign('types', $types);

            return $this->fetch('goods_info');
        }
    }

    public function saveGoods()
    {
        if ($this->request->isPost()) {
            $success  = $this->validateData('Goods');
            if (true !== $success) {
                $err = array_values($success);
                ajaxReturn(['status'=>0, 'msg'=>$err[0], 'result'=>$success]);
            } else {
                $data = input('post.');
                //dump(input('post.')); exit;
                $goodsModel = $this->app->model('goods');
                $res = $goodsModel->saveGoods($data, $data['goods_id']);
                if ($res) {
                    ajaxReturn(['status'=>1, 'msg'=>'操作成功']);
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>$goodsModel->getError()]);
                }
            }
        }
    }

    public function del_goods_images()
    {
        $image_url = input('get.filename');
        if ($image_url != '') {
            Db::name('goods_image')->where('image_url', $image_url)->delete();
        }
    }

    public function delGoods()
    {
        $goods_ids = input('ids/s');
        if(empty($goods_ids)) {
            ajaxReturn(['status'=>0, 'msg'=>'参数错误']);
        } else {
            $res = Db::name('goods')->where('goods_id', 'in', $goods_ids)->delete();
            if ($res) {
                ajaxReturn(['status'=>1, 'msg'=>'删除商品成功', 'url'=>url('/admin/Goods/goodsList')]);
            } else {
                ajaxReturn(['status'=>0, 'msg'=>'删除商品失败']);
            }
        }
    }
}