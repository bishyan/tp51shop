<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/25
 * Time: 23:10
 */

namespace app\admin\model;
use think\Model;
use think\Db;

class Type extends Model
{
    protected $name = 'goods_type';
    protected $pk = 'type_id';
    protected $field = ['type_name'];

    public function saveData($data, $type_id = 0)
    {
        if ($type_id == 0) {
            # 增加
            if (isset($data['type_id'])) {
                unset($data['type_id']);
            }
            $res = $this->save($data);
        } else {
            $res = $this->save($data, ['type_id' => $type_id]);
        }

        if ($res) {
            if ($info = $this->afterSave($this->type_id)) {
                return [
                    'ok' => 1,
                ];
            } else {
                return ['ok' => 0, 'err' => '商品模型的规格或规格项操作失败'];
            }
        } else {
            return ['ok' => 0, 'err' => '商品模型操作失败'];
        }
    }

    private function afterSave($type_id)
    {
        if (empty($type_id)) {
            return false;
        } else {
            $specs = input('post.spec');
            if (!empty($specs)) {
                foreach ($specs as $spec) {
                    if ($spec['spec_name'] !== '') {
                        $spec_data = [
                            'type_id' => $type_id,
                            'sort_order' => $spec['sort_order'],
                            'is_upload_image' => $spec['is_upload_image'],
                            'spec_name' => $spec['spec_name'],
                            'item' => $spec['item'],
                        ];
                        if (isset($spec['spec_id'])) {
                            $spec_data['spec_id'] = $spec['spec_id'];
                        }

                        $this->saveSpec($spec_data);
                    }
                }
            }

            $attrs = input('post.attribute');
            if (!empty($attrs)) {
                foreach ($attrs as $attr) {
                    if ($attr['attr_name'] != '') {
                        $attr_data = [
                            'attr_name' => $attr['attr_name'],
                            'type_id' => $type_id,
                            'attr_index' => $attr['attr_index'],
                            'attr_values' => $attr['attr_values'],
                            'sort_order' => $attr['sort_order']
                        ];
                        if (isset($attr['attr_id'])) {
                            $attr_data['attr_id'] = $attr['attr_id'];
                        }
                        Db::name('attribute')->insert();
                        $this->saveAttr($attr_data);
                    }
                }
            }
            return true;
        }
    }

    private function saveAttr($data)
    {
        if (isset($data['attr_id'])) {
            Db::name('attribute')->update($data);
        } else {
            Db::name('attribute')->insert($data);
        }
    }

    private function saveSpec($data)
    {
        $spec_item = $data['item'];
        unset($data['item']);
        if (isset($data['spec_id'])) {
            # 修改
            Db::name('spec')->update($data);
        } else {
            # 增加
            $data['spec_id'] = Db::name('spec')->insertGetId($data);
        }

        foreach ($spec_item as $item) {
            if ($item['item'] != '') {
                if (isset($item['id'])) {
                    # 修改原有规格项
                    Db::name('spec_item')->update([
                        'item' => $item['item'],
                        'id' => $item['id'],
                        'spec_id' => $data['spec_id']
                    ]);
                } else {
                    # 添加新的规格项
                    Db::name('spec_item')->insert([
                        'item' => $item['item'],
                        'spec_id' => $data['spec_id']
                    ]);
                }
            }
        }
    }
}