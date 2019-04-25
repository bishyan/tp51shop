<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/21
 * Time: 10:25
 */

namespace app\admin\controller;
use think\Db;
use app\admin\model\Category;
use think\facade\App;

class Brand extends Base
{
    public function brandList()
    {
        $keyword = input('keyword', '');
        $where = '';
        if ($keyword != '') {
            $where[] = ['brand_name', 'like', "%{$keyword}%"];
        }

        $brands = Db::name('brand')->where($where)->order('sort_order')->paginate(2);
        //dump($brands->nickname);
        $this->assign('brands', $brands);
        $cats = Db::name('category')->column('cat_id, cat_name');
        //dump($cats);
        $this->assign('cats', $cats);
        //dump(config('paginate'));

        return $this->fetch();
    }

    public function addBrand()
    {
        if ($this->request->isPost()) {
            $success = $this->validateData('Brand');
            if ($success !== true) {
                $err = array_values($success);
                ajaxReturn(['status'=>0, 'msg'=>$err[0], 'result'=>$success]);
            } else {
                $data = input('post.');
                $same_brand = Db::name('brand')->where(['brand_name'=>$data['brand_name'], 'cat_id'=>$data['cat_id']])->find();
                if ($same_brand === null) {
                    $res = App::model('Brand')->save($data);
                    if ($res) {
                        ajaxReturn(['status'=>1, 'msg'=>'品牌添加成功']);
                    } else {
                        ajaxReturn(['status'=>0, 'msg'=>'品牌添加失败']);
                    }
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>'同一分类下已存在相同的品牌']);
                }
            }
            exit;
        }
        $cats = Db::name('category')->where('parent_id', 0)->select();
        $this->assign('cats', $cats);
        return $this->fetch();
    }

    public function editBrand($brand_id)
    {
        if ($this->request->isPost())
        {
            $success = $this->validateData('Brand');
            if (true !== $success) {
                $err = array_values($success);
                ajaxReturn(['status'=>0, 'msg'=>$err[0], 'result'=>$success]);
            } else {
                $data = input('post.');
                $same_brand = Db::name('brand')
                    ->where(['brand_name'=>$data['brand_name'], 'cat_id'=>$data['cat_id']])
                    ->where('brand_id', '<>', $brand_id)->find();

                if ($same_brand === null) {
                    $res = App::model('Brand')->save($data, ['brand_id', $brand_id]);
                    if ($res) {
                        ajaxReturn(['status'=>1, 'msg'=>'品牌编辑成功']);
                    } else {
                        ajaxReturn(['status'=>0, 'msg'=>'品牌编辑失败']);
                    }
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>'同一分类下已存在相同的品牌']);
                }
            }
            exit;
        }

        $info = Db::name('brand')->find($brand_id);
        if (empty($info)) {
            $this->error('参数有误');
        } else {
            $cats = Db::name('category')->where('parent_id', 0)->select();
            $this->assign('cats', $cats);
            $this->assign('info', $info);
        }

        return $this->fetch();
    }

    public function delBrand()
    {
        $brand_id = input('post.ids');
        if (empty($brand_id)) {
            ajaxReturn(['status'=>0, 'msg'=>'参数有误']);
        } else {
            $res = Db::name('brand')->where('brand_id', $brand_id)->delete();
            if ($res) {
                ajaxReturn(['status'=>1, 'msg'=>'删除成功', 'url'=>url('/admin/Brand/brandList')]);
            } else {
                ajaxReturn(['status' => 0, 'msg' => '删除失败']);
            }
        }
    }
}