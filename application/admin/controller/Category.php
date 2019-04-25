<?php
/**
 *  商品分类控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 11:00
 */

namespace app\admin\controller;
use think\Db;
use app\admin\model\Category as CateModel;

class Category extends Base
{
    public function cateList()
    {
        $cateModel = new CateModel();
        $cates = $cateModel->getCatList();
        $this->assign('cates', $cates);

        return $this->fetch();
    }

    public function addCate()
    {
        if ($this->request->isPost()) {
            // 验证数据
            $success = $this->validateData('Category');
            if ($success !== true) {
                $err = array_values($success);
                ajaxReturn(['status'=>0, 'msg'=>$err[0], 'data'=>$success]);
            } else {
                $data = input('post.');
                $category = Db::name('Category')->where(['cat_name'=>$data['cat_name'], 'parent_id'=>$data['parent_id']])->find();
                if ($category === null)
                {
                    $cateModel = new CateModel();
                    $res = $cateModel->saveData($data);
                    if ($res['ok'] == 1) {
                        ajaxReturn(['status'=>1, 'msg'=>'分类添加成功', 'data'=>['url'=>url('/admin/Category/cateList')]]);
                    } else {
                        ajaxReturn(['status'=>0, 'msg'=>$res['err']]);
                    }
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>'同级下已有相同分类']);
                }
            }
        }

        //$cats = Db::name('Category')->where('level', '<', 3)->select();
        $cateModel = new CateModel();
        $parent_cats = $cateModel->getParentCate();

        $this->assign('parent_cats', $parent_cats);

        return $this->fetch();
    }

//    public function insertCate()
//    {
//        if ($this->request->isPost()) {
//            // 验证数据
//            $success = $this->validateData('Category');
//            if ($success !== true) {
//                $err = array_values($success);
//                ajaxReturn(['status'=>0, 'msg'=>$err[0], 'data'=>$success]);
//            } else {
//                $data = input('post.');
//                $category = Db::name('Category')->where(['cat_name'=>$data['cat_name'], 'parent_id'=>$data['parent_id']])->find();
//                if ($category === null) {
//                    $cateModel = new CateModel();
//
//                    $res = $cateModel->saveData($data);
//                    if ($res['ok'] == 1) {
//                        ajaxReturn(['status'=>1, 'msg'=>'分类添加成功', 'data'=>['url'=>url('/admin/Category/cateList')]]);
//                    } else {
//                        ajaxReturn(['status'=>0, 'msg'=>$res['err']]);
//                    }
//                } else {
//                    ajaxReturn(['status'=>0, 'msg'=>'同级下已有相同分类']);
//                }
//            }
//        }
//    }

    public function editCate($cat_id)
    {
        if ($this->request->isPost()) {
            // 验证数据
            $success = $this->validateData('Category');
            if ($success !== true) {
                $err = array_values($success);
                ajaxReturn(['status'=>0, 'msg'=>$err[0], 'data'=>$success]);
            } else {
                $data = input('post.', true);

                //判断分类是否超过3级
                // 首先计算出当前分类下有几级子类
                $max_level = Db::name('category')
                    ->where('parent_id_path', 'like', '%_'.$cat_id.'_%')
                    ->whereOr('parent_id_path', 'like', '%_'.$cat_id)
                    ->max('level');
                $cur_level = Db::name('category')->where('cat_id', $cat_id)->value('level');
                $child = $max_level - $cur_level;
                $parent = $data['parent_id'] == 0? 0 : Db::name('category')->where('cat_id', $data['parent_id'])->value('level');
                if (($child + $parent) > 2) {
                    ajaxReturn(['status'=>0, 'msg'=>'上级分类选择错误, 分类不能超过3级', 'data'=>'']);
                }


                // 查询同级分类下是否有重复分类
                $same_cate = Db::name('category')->where([
                    'cat_name' => $data['cat_name'],
                    'parent_id' => $data['parent_id'],
                    ])->where('cat_id', '<>', $cat_id)->find();
                if (!is_null($same_cate)) {
                    ajaxReturn(['status'=>0, 'msg'=>'同一分类已有相同的分类存在', 'data'=>'']);
                }

                // 查询上级分类是否是自己
                if ($data['parent_id'] == $data['cat_id']) {
                    ajaxReturn(['status'=>0, 'msg'=>'不能选择自己作为上级分类', 'data'=>'']);
                }

                // 判断上级分类不能是子类
                $son_cat = Db::name('category')->where('parent_id', $cat_id)->column('cat_id');
                if (in_array($data['parent_id'], $son_cat)) {
                    ajaxReturn(['status'=>0, 'msg'=>'上级分类不能是自己的子类', 'data'=>'']);
                }

                if ($data['commission_rate'] > 100) {
                    ajaxReturn(['status'=>0, 'msg'=>'分佣比例不能超级100%', 'data'=>'']);
                }

                $cateModel = new CateModel();
                $res = $cateModel->saveData($data, $cat_id);

                if($res['ok'] ==1) {
                    ajaxReturn(['status'=>1, 'msg'=>'分类编辑成功', 'data'=>['url'=>url('/admin/Category/cateList')]]);
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>$res['err']]);
                }
            }
            exit;
        }


        $info = Db::name('category')->where('cat_id', $cat_id)->find();
        if ($info !== null) {
            $this->assign('info', $info);
            $cateModel = new CateModel();
            $parent_cats = $cateModel->getParentCate($info['cat_id'], $info['parent_id']);
            $this->assign('parent_cats', $parent_cats);
        } else {
            $this->error('参数错误');
        }

        return $this->fetch();
    }



    public function delCate()
    {
        $cat_id = input('ids', 0);
        if ($cat_id == 0) {
            ajaxReturn(['status'=>0, 'msg'=>'参数有误']);
        } else {
//            $goods = Db::name('goods')->where('cat_id', $cat_id)->find();
//            if ($goods !== null) {
//                ajaxReturn(['status'=>0, 'msg'=>'该分类下有商品, 不能删除']);
//            }

            $sub_cat = Db::name('category')->where('parent_id', $cat_id)->find();

            if ($sub_cat !== null) {
                ajaxReturn(['status'=>0, 'msg'=>'该分类下有子分类, 不能删除']);
            } else {
                $res = Db::name('category')->where('cat_id', $cat_id)->delete();
                if ($res) {
                    ajaxReturn(['status'=>1, 'msg'=>'分类删除成功', 'url'=>url('/admin/category/cateList')]);
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>'分类删除失败']);
                }
            }
        }
    }

    public function getCategory(){
        $parent_id = input('get.parent_id');
        $list = Db::name('category')->where('parent_id', $parent_id)->field('cat_id, cat_name')->select();
        //dump($list); exit;
        if ($list) {
            ajaxReturn(['status'=>1, 'msg'=>'获取成功', 'result'=>$list]);
        } else {
            ajaxReturn(['status'=>0, 'msg'=>'获取失败', 'result'=>[]]);
        }
    }
}