<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/1
 * Time: 17:07
 */

namespace app\admin\model;
use think\Model;
use think\Db;

class Goods extends Model
{
    protected $autoWriteTimestamp = true;
    protected $pk = 'goods_id';
    protected $field = [];

    public function GoodsBrand()
    {
        return $this->hasMany('brand', 'brand_id', 'brand_id');
    }

    public function GoodsCat()
    {
        return $this->hasMany('category', 'cat_id', 'cat_id');
    }

    public function getGoodsList($page_size = 20)
    {
        $where = [];
        $request = input('get.');
        if($request){
            foreach($request as $k=>$v) {
                if ($k == 'cat_id' && $v != 0) {
                    $where[] = ['cat_id', '=', $v];
                }
                if ($k == 'brand_id' && $v != 0) {
                    $where[] = ['brand_id', '=', $v];
                }
                if ($k == 'is_on_sale' && $v != '') {
                    $where[] = ['is_on_sale', '=', $v];
                }

                if ($k == 'intro' && $v != "") {
                    $where[] = [$v, '=', 1];
                }

                if ($k == 'key_word' && $v != '') {
                    $where[] = ['goods_name', 'like', "%{$v}%"];
                }
            }
        }

        $order = input('orderby1', 'goods_id');
        $orderby = input('orderby2', 'desc');

        return $this->where($where)
            ->order("$order $orderby")
            ->paginate($page_size, false, ['query'=>$request]);
    }

    public function saveGoods($data, $goods_id = 0)
    {
        if ($goods_id == 0) {
            # 添加
            if (isset($data['goods_id'])) {
                unset($data['goods_id']);
            }
            $this->data($data, true);
            $this->price_ladder = '';
            $res = $this->save();
        } else {
            # 修改
            $this->data($data, true);
            $this->price_ladder = '';
            $res = $this->isUpdate()->save();
        }

        if ($res) {
           return $this->saveAfter($this->goods_id);
        } else {
            $this->error = '添加或修改商品失败';
            return false;
        }
    }

    public function saveAfter($goods_id)
    {
        if ($goods_id <= 0) {
            $this->error = '无效的参数';
            return false;
        } else {
            // 处理商品货号
            $this->handleGoodsSn($goods_id);
            // 处理商品原图
            $this->handleGoodsOriginalImage($goods_id);
            // 商品相册
            $this->handleGoodsImages($goods_id);
            // 商品规格图片
            $this->handleGoodsSpecImages($goods_id);
            // 商品规格
            $this->handleGoodsSpec($goods_id);
            refresh_stock($goods_id); //刷新商品的库存
            // 处理商品的属性
            $this->handleGoodsAttr($goods_id);

            return true;
        }
    }

    private function handleGoodsAttr($goods_id)
    {
        $old_goods_type = input('post.old_goods_type/d');
        $goods_type = Db::name('goods')->where('goods_id', $goods_id)->value('goods_type');
        if ($old_goods_type != $goods_type) {
            # 更改了属性类型, 删除原有的记录
            Db::name('goods_attr')->where('goods_id', $goods_id)->delete();
        }

        $goodsAttrs = Db::name('goods_attr')->where('goods_id', $goods_id)->select();
        $old_goods_attrs = [];
        if (count($goodsAttrs) > 0) {
            foreach ($goodsAttrs as $k => $v) {
                $old_goods_attrs[$v['attr_id']] = $v;
            }
        }

        $post_goods_attr = input('post.goods_attr/a', '');
        if (!empty($post_goods_attr)) {
            foreach ($post_goods_attr as $key => $val) {
                if (!empty($val)) {
                    // 判断属性是否已经存在, g
                    if (array_key_exists($key, $old_goods_attrs)) {
                        $goods_attr_id = $old_goods_attrs[$key]['goods_attr_id'];
                        Db::name('goods_attr')->where('goods_attr_id', $goods_attr_id)->update(['attr_value' => $val]);
                        unset($old_goods_attrs[$key]);
                    } else {
                        Db::name('goods_attr')->insert(['goods_id' => $goods_id, 'attr_id' => $key, 'attr_value' => $val]);
                    }
                }
            }
        }

        if (count($old_goods_attrs) > 0) {
            foreach($old_goods_attrs as $k=>$v) {
                Db::name('goods_attr')->where('goods_attr_id', $v['goods_attr_id'])->delete();
            }
        }
    }

    private function handleGoodsSn($goods_id) {
        $goods_sn = generate_goods_sn($goods_id);
        Db::name('goods')->where(['goods_id'=>$goods_id, 'goods_sn'=>''])->update(['goods_sn'=>$goods_sn]);
    }

    private function handleGoodsOriginalImage($goods_id)
    {
        $original_image = input('post.original_img', '');
        $has_num = Db::name('goods_image')->where(['goods_id'=>$goods_id, 'image_url'=>$original_image])->count();

        if ($has_num == 0 && $original_image != '' && file_exists(app()->getRootPath() . 'public' . $original_image)) {
            Db::name('goods_image')->insert(['goods_id'=>$goods_id, 'image_url'=>$original_image]);
        }
    }

    private function handleGoodsImages($goods_id)
    {
        $goods_images = input('post.goods_images/a');
        if (count($goods_images) > 1) {
            array_pop($goods_images);
            $goodsImagesArr = Db::name('goods_image')->where('goods_id', $goods_id)->column('img_id, image_url');

            foreach($goodsImagesArr as $k=>$v) {
                if(!in_array($v, $goods_images)) {
                    Db::name('goods_image')->where('img_id', $k)->delete();
                }
            }

            foreach($goods_images as $key=>$val) {
                if (!in_array($val, $goodsImagesArr)) {
                    Db::name('goods_image')->insert(['goods_id'=>$goods_id, 'image_url'=>$val]);
                }
            }
        }
    }

    private function handleGoodsSpecImages($goods_id)
    {
        $item_img = input('post.item_img/a');
        if ($item_img !== null) {
            // 先删除旧的记录
            Db::name('spec_image')->where('goods_id', $goods_id)->delete();
            foreach($item_img as $k=>$v) {
                if ($v != '') {
                    Db::name('spec_image')->insert([
                        'goods_id' => $goods_id,
                        'spec_image_id' => $k,
                        'src' => $v
                    ]);
                }
            }
        }
    }

    private function handleGoodsSpec($goods_id)
    {
        $item_img = input('post.item_img/a');
        $goods_item = input('post.item/a');
        if (!is_null($goods_item)) {
            $item_key_arr = '';
            foreach($goods_item as $k=>$v) {
                if($v['price'] > 0) {
                    if ($item_key_arr == '') {
                        $item_key_arr = $k;
                    } else {
                        $item_key_arr .= ',' . $k;
                    }

                    $data = [
                        'goods_id' => $goods_id,
                        'key' => $k,
                        'key_name' => $v['key_name'],
                        'price' => trim($v['price']),
                        'cost_price' => trim($v['cost_price']),
                        'commission' => trim($v['commission']),
                        'store_count' => trim($v['store_count']),
                        'sku' => trim($v['sku']),
                    ];
                    if ($item_img) {
                        $spec_key_arr = explode('_', $k);
                        foreach ($item_img as $k1 => $v1) {
                            if (in_array($k1, $spec_key_arr)) {
                                $data['spec_img'] = $v1;
                                break;
                            }
                        }
                    }

                    // 查询是否是新添加的数据
                    $specGoodsPrice = Db::name('spec_goods_price')->where(['goods_id' => $goods_id, 'key' => $data['key']])->find();
                    if (is_null($specGoodsPrice)) {
                        #添加
                        Db::name('spec_goods_price')->insert($data);
                    } else {
                        Db::name('spec_goods_price')->where(['goods_id' => $goods_id, 'key' => $data['key']])->update($data);
                    }
                }
            }

            if($item_key_arr != '') {
                Db::name('spec_goods_price')->where('goods_id', $goods_id)->whereNotIn('key', $item_key_arr)->delete();
            }
        } else {
            # 清空$goods_id的商品规格关联项
            Db::name('spec_goods_price')->where('goods_id', $goods_id)->delete();
        }
    }

    public function setCatIdAttr($value, $data) {
        if (isset($data['cat_id_3']) && $data['cat_id_3'] > 0) {
            return $data['cat_id_3'];
        }

        if (isset($data['cat_id_2']) && $data['cat_id_2'] > 0) {
            return $data['cat_id_2'];
        }

        return $value;
    }

    public function setVirtualIndateAttr($value)
    {
        $virtual_time = strtotime($value);
        if ($virtual_time > time()) {
            return $virtual_time;
        } else {
            return 0;
        }
    }

    public function setPriceLadderAttr($value, $data)
    {
        if ($data['ladder_amount'][0] > 0) {
            $price_ladder = [];
            foreach($data['ladder_amount'] as $key=>$val) {
                $price_ladder[$key]['amount'] = intval($data['ladder_amount'][$key]);
                $price_ladder[$key]['price'] = floatval($data['ladder_price'][$key]);
            }
            $price_ladder = array_values(array_sort($price_ladder, 'amount', 'asc'));
            return json_encode($price_ladder);
        } else {
            return '';
        }
    }

    public function getPriceLadderAttr($value) {
        if(empty($value)) {
            return [];
        } else {
            return json_decode($value, true);
        }
    }


}