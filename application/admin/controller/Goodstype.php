<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/25
 * Time: 19:53
 */

namespace app\admin\controller;
use think\App;
use think\Db;


class GoodsType extends Base
{
    public function typeList()
    {
        $types = Db::name('goods_type')->order('type_id desc')->select();
        $this->assign('types', $types);

        return $this->fetch();
    }

    public function addType()
    {
        if ($this->request->isPost()) {

            $success = $this->validateData('Type');
            if (true !== $success) {
                ajaxReturn(['status'=>0, 'msg'=>'商品模型名称不能为空']);
            } else {
                $data['type_id'] = input('post.type_id');
                $data['type_name'] = input('post.type_name');
                // 检查是否有相同的模型
                $same_type = Db::name('goods_type')->where('type_name', $data['type_name'])->find();
                if (null == $same_type) {
                    $typeModel = $this->app->model('Type');
                    $res = $typeModel->saveData($data);
                    if ($res['ok'] == 1) {
                        ajaxReturn(['status'=>1, 'msg'=>'商品模型添加成功']);
                    } else {
                        ajaxReturn(['status'=>0, 'msg'=>$res['err']]);
                    }
                }
            }
            exit;
        }

        return $this->fetch();
    }

    public function editType($type_id)
    {
        if ($this->request->isPost()) {
            // 验证数据
            $success = $this->validateData('Type');
            if (true !== $success) {
                ajaxReturn(['status'=>0, 'msg'=>'商品模型名称不能为空']);
            } else {
              $data['type_id'] = input('post.type_id');
              $data['type_name'] = input('post.type_name');
              // 检查是否有相同的模型
              $same_type = Db::name('goods_type')
                  ->where('type_name', $data['type_name'])
                  ->where('type_id', '<>', $data['type_id'])
                  ->find();
              if ($same_type == null) {
                  $res = $this->app->model('Type')->saveData($data, $type_id);
                  if($res['ok'] == 1) {
                      ajaxReturn(['status'=>1, 'msg'=>'商品模型编辑成功']);
                  } else {
                      ajaxReturn(['status'=>0, 'msg'=>$res['err']]);
                  }
              } else {
                  ajaxReturn(['status'=>0, 'msg'=>'商品模型已存在']);
              }
            }
            exit;
        }

        $type_info = Db::name('goods_type')->find($type_id);
        if (null !== $type_info) {
            // 获取规格和规格项
            $table_prefix = config('database.prefix');
            $spec_list = Db::name('spec')->alias('a')
                ->field('a.*, b.id, b.item')
                ->join($table_prefix.'spec_item b', 'a.spec_id=b.spec_id', 'left')
                ->where('a.type_id', $type_id)->order('a.spec_id desc')->select();
            $specs = [];
            foreach($spec_list as $key=>$val) {
                $item['id'] = $val['id'];
                $item['item'] = $val['item'];
                $specs[$val['spec_id']]['spec_id'] = $val['spec_id'];
                $specs[$val['spec_id']]['spec_name'] = $val['spec_name'];
                $specs[$val['spec_id']]['type_id'] = $val['type_id'];
                $specs[$val['spec_id']]['sort_order'] = $val['sort_order'];
                $specs[$val['spec_id']]['is_upload_image'] = $val['is_upload_image'];
                $specs[$val['spec_id']]['spec_item'][] = $item;
            }

            // 属性
            $attrs = Db::name('attribute')->where('type_id', $type_id)->select();

            $this->assign('attrs', $attrs);
            $this->assign('specs', $specs);
            $this->assign('type_info', $type_info);

        }

        return $this->fetch('add_type');
    }

    //删除模型及与其相关的数据
    public function delType()
    {
        $type_id = input('post.id/d');
        if ($type_id > 0) {
            $res = Db::name('goods_type')->delete($type_id);
            if ($res) {
                # 删除对应的属性
                Db::name('attribute')->where('type_id', $type_id)->delete();

                # 删除对应的规格及规格项
                $spec_ids = Db::name('spec')->where('type_id', $type_id)->field('GROUP_CONCAT(spec_id) as spec_id')->find();

                $del_res = Db::name('spec')->where('type_id', $type_id)->delete(); //规格
                if ($del_res) {
                    Db::name('spec_item')->whereIn('spec_id', $spec_ids['spec_id'])->delete();  //规格项
                }

                ajaxReturn(['status'=>1, 'msg'=>'商品模型删除成功']);
            } else {
                ajaxReturn(['status'=>0, 'msg'=>'商品模型删除失败']);
            }
        } else {
            ajaxReturn(['status'=>0, 'msg'=>'无效的参数']);
        }
    }


    //删除商品规格
    public function delSpe()
    {
        $id = input('post.id/d', 0);
        if ($id > 0) {
            $res = Db::name('spec')->delete($id);
            if ($res) {
                // 删除对应的规格项
                $info = Db::name('spec_item')->where('spec_id', $id)->delete();
                if ($info) {
                    ajaxReturn(['status' => 1]);
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>'商品规格删除成功, 对应的规格项删除失败']);
                }
            } else {
                ajaxReturn(['status'=>0, 'msg'=>'商品规格删除失败']);
            }
        } else {
            ajaxReturn(['status'=>0, 'msg'=>'无效的参数']);
        }
    }

    //删除规格项
    public function delSpeItem()
    {
        $id = input('post.id/d', 0);
        if ($id > 0) {
            $res = Db::name('spec_item')->delete($id);
            if ($res) {
                ajaxReturn(['status'=>1]);
            } else {
                ajaxReturn(['status'=>0, 'msg'=>'规格项删除失败']);
            }
        } else {
            ajaxReturn(['status'=>0, 'msg'=>'无效的参数']);
        }
    }

    //删除商品属性
    public function delAttr()
    {
        $attr_id = input('post.attr_id/d', 0);
        if ($attr_id > 0) {
            $res = Db::name('attribute')->delete($attr_id);
            if ($res) {
                ajaxReturn(['status'=>1]);
            } else {
                ajaxReturn(['status'=>0, 'msg'=>'属性删除失败']);
            }
        } else {
            ajaxReturn(['status'=>0, 'msg'=>'无效的参数']);
        }
    }



}