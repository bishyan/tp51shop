<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/25
 * Time: 19:53
 */

namespace app\admin\controller;
use think\App;
use think\Collection;
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
                        ajaxReturn(['status'=>1, 'msg'=>'商品模型添加成功', 'type_id'=>$res['type_id']]);
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
                      ajaxReturn(['status'=>1, 'msg'=>'商品模型编辑成功', 'type_id'=>$type_id]);
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
                ->where('a.type_id', $type_id)->order('a.sort_order')->select();
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


    public function ajaxGetSpecSelect()
    {
        $goods_id = input('post.goods_id/d', 0);

        // 获取当前商品的规格项id
        if ($goods_id > 0) {
            $items_id = Db::name('spec_goods_price')->where('goods_id', $goods_id)->value("GROUP_CONCAT(`key` SEPARATOR '_')");
            $items_ids = explode('_', $items_id);
            $this->assign('items_ids', $items_ids);
        }

        $type_id = input('post.type_id/d', 0);
        $specs = Db::name('spec')->where('type_id', $type_id)->column('spec_id, spec_name, type_id, is_upload_image');
        foreach($specs as $k=>$v) {
            // 获取对应的规格项
            $specs[$k]['spec_item'] = Db::name('spec_item')->where('spec_id', $v['spec_id'])->column('id, item');
        }

        $this->assign('specs', $specs);

        echo  $this->fetch();
    }

    public function ajaxGetAttrInput()
    {
        $goods_id = input('get.goods_id/d', 0);
        $type_id = input('get.type_id/d', 0);

        $attrs = Db::name('attribute')->where(['type_id'=>$type_id, 'attr_index'=>1])->order('sort_order')->select();
        if (count($attrs) > 0) {
            foreach($attrs as $key=>$attr) {
                if($goods_id > 0) {
                    $goods_attr = Db::name('goods_attr')->where(['goods_id'=>$goods_id, 'attr_id'=>$attr['attr_id']])->value('attr_value');
                    $attrs[$key]['goods_attr'] = is_null($goods_attr)? '':$goods_attr;
                } else {
                    $attrs[$key]['goods_attr'] = '';
                }
                if ($attr['attr_values'] == '') {
                    $attrs[$key]['attr_values_to_array'] = [];
                } else {
                    $attrs[$key]['attr_values_to_array'] = explode(',', $attr['attr_values']);
                }
            }
        }
             //dump($attrs);

        return $attrs;
    }

    public function ajaxGetSpecInput()
    {
        $goods_id = input('post.goods_id/d', 0);
        if ($goods_id > 0) {
            $goods_spec_price = Db::name('spec_goods_price')->where('goods_id', $goods_id)->column('key, key_name, price, store_count, sku, cost_price, commission');
        }

        $spec_arr = input('post.spec_arr/a', [[]]);
        foreach ($spec_arr as $key => $val) {
            $spec_arr_sort[$key] = count($val);
        }

        foreach ($spec_arr_sort as $k => $v) {
            $spec_arr2[$k] = $spec_arr[$k];
        }

        $clos = array_keys($spec_arr2);

        $spec_arr2 = cartesian_product($spec_arr2);  // 笛卡尔积

        $specs = Db::name('spec')->column('spec_id, spec_name'); //规格表
        $spec_item = Db::name('spec_item')->column('id,item,spec_id');  //规格项
        //dump($spec_item);
        $str = "<table class='table table-bordered' id='spec_input_tab'>";
        $str .= "<tr>";
        $str_fill = "<tr>";

        foreach ($clos as $k => $v) {
            if (isset($specs[$v])) {
                $str .= " <td><b>{$specs[$v]}</b></td>";
            } else {
                $str .= " <td><b></b></td>";
            }

            $str_fill .= " <td><b></b></td>";
        }

        $str .= "<td><b>购买价</b></td>
               <td><b>成本价</b></td>
               <td><b>佣金</b></td>
               <td><b>库存</b></td>
               <td><b>SKU</b></td>
               <td><b>操作</b></td>
             </tr>";
        if (count($spec_arr2) > 0) {
            $str_fill .= '<td><input id="item_price" value="0" onkeyup="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)" onpaste="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)"></td>
               <td><input id="item_cost_price" value="0" onkeyup="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)" onpaste="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)"></td>
               <td><input id="item_commission" value="0" onkeyup="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)" onpaste="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)"></td>
               <td><input id="item_store_count" value="0" onkeyup="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)" onpaste="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)"></td>
               <td><input id="item_sku" value="" onkeyup="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)" onpaste="this.value=this.value.replace(/[^\d.]/g,&quot;&quot;)"></td>
               <td><button id="item_fill" type="button" class="btn btn-success">批量填充</button></td>
             </tr>';
            $str .= $str_fill;
        }

        // 第二行
        foreach($spec_arr2 as $k=>$v)
        {
            //dump($v);
            $str .= "<tr>";
            $item_key_name = [];
            foreach($v as $k1=>$v1) {
                $str .= "<td>{$spec_item[$v1]['item']}</td>";
                $item_key_name[$v1] = $spec_item[$v1]['spec_id'] . ":" . $spec_item[$v1]['item'];
            }

            ksort($item_key_name);
            $item_key = implode('_', array_keys($item_key_name));
            $item_name = implode(' ', $item_key_name);

            $price = isset($goods_spec_price[$item_key])? $goods_spec_price[$item_key]['price'] : 0;
            $store_count = isset($goods_spec_price[$item_key])? $goods_spec_price[$item_key]['store_count'] : 0;
            $cost_price = isset($goods_spec_price[$item_key])? $goods_spec_price[$item_key]['cost_price'] : 0;
            $commission = isset($goods_spec_price[$item_key])? $goods_spec_price[$item_key]['commission'] : 0;

            $str .="<td>
                <input name='item[$item_key][price]' value='".$price."' 
                onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' 
                onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
            $str .="<td>
                <input name='item[$item_key][cost_price]' value='".$cost_price."' 
                onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' 
                onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
            $str .="<td>
                <input name='item[$item_key][commission]' value='".$commission."' 
                onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' 
                onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
            $str .="<td>
                <input name='item[$item_key][store_count]' value='".$store_count."' 
                onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' 
                onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
            $str .="<td>
                <input name='item[$item_key][sku]' value='' /><input type='hidden' name='item[$item_key][key_name]' value='$item_name' /></td>";
            $str .= "<td><button type='button' class='btn btn-default delete_item'>无效</button></td>";
            $str .= "</tr>";
        }
        $str .= "</table>";

        echo $str;
    }

    public function ajaxGetGoodsTypeList()
    {
        $type_id = input('post.type_id');
        $goods_types = Db::name('goods_type')->select();
        //dump($goods_types); exit;
        $html = '<option value="0">选择商品模型';
        foreach($goods_types as $k=>$v) {
            $html .= "<option value='".$v['type_id']."'";
            if ($v['type_id'] == $type_id) {
                $html .= " selected='selected'";
            }
            $html .= ">".$v['type_name']."</option>";
        }

        ajaxReturn($html);
    }
}