<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 15:24
 */

namespace app\admin\model;
use think\Model;
use think\Db;

class Category extends Model
{
    protected $pk = 'cat_id';
    protected $field = ['cat_name', 'mobile_name', 'parent_id', 'is_show', 'cat_group', 'sort_order', 'commission_rate'];


    public function saveData($data, $cat_id=0)
    {
        // 判断是新增还是编辑数据
        if ($cat_id == 0) {
            # 新增
            if (isset($data['cat_id'])) {
                unset($data['cat_id']);
            }
            $res = $this->save($data);
        } else {
            # 编辑
            $res = $this->save($data, ['cat_id'=>$cat_id]);
        }

        if ($res) {
            if ($this->refreshCategory($this->cat_id)) {
                return ['ok'=>1];
            } else {
                return ['ok'=>0, 'err'=>'分类等级和家族图谱更改失败'];
            }
        }
    }

    protected function refreshCategory($cat_id)
    {
        $cat = $this->find($cat_id);
        if (is_null($cat)) {
            return false;
        }

        $table_prefix = config('database.prefix');
        if ($cat['parent_id_path'] == '')
        {
            if ($cat['parent_id'] == 0) {
                Db::execute("UPDATE `{$table_prefix}category` SET parent_id_path = '0_$cat_id', level=1 WHERE cat_id=:cat_id", ['cat_id'=>$cat_id]);
            } else {
                Db::execute("UPDATE `{$table_prefix}category` AS a, `{$table_prefix}category` AS b SET a.parent_id_path = CONCAT_WS('_',b.parent_id_path,'$cat_id'), a.level=(b.level+1) WHERE a.parent_id=b.cat_id AND a.cat_id=:cat_id", ['cat_id'=>$cat_id]);
            }
            $cat = $this->find($cat_id);
        }

        if ($cat['parent_id'] == 0) {
            $parent_cat['parent_id_path'] = '0';
            $parent_cat['level'] = 0;
        } else {
            $parent_cat = $this->find($cat['parent_id']);
        }

        $replace_level = $cat['level'] - ($parent_cat['level'] + 1);
        $replace_str = $parent_cat['parent_id_path'] . '_' .$cat_id;

        Db::execute("UPDATE `{$table_prefix}category` SET parent_id_path = REPLACE(parent_id_path, '{$cat['parent_id_path']}','$replace_str'), level=(level - $replace_level) WHERE parent_id_path LIKE '{$cat['parent_id_path']}%'");
        return true;
    }

    /** 获取指定分类下子分类数据
     * @param int $cat_id
     * @return array
     */
    public function getCatList($cat_id=0) {
        $cats = $this->order('parent_id, sort_order')->select();
        return $this->resortCate($cats, $cat_id);
    }

    /**
     * 获取指定分类及其子孙分类以外的上级分类(取前两级)
     * @param int $cat_id        指定的分类id
     * @param int $selected_id   当前选中的分类id
     */
    public function getParentCate($cat_id=0, $selected_id=0)
    {
        $parent_cat = $this->where('level', '<', 3)
            ->column('cat_id, parent_id, cat_name, level', 'cat_id');
        $parent_cat = $this->resortCate($parent_cat);

        foreach($parent_cat as $k=>$v)
        {
            if ($v['cat_id'] == $cat_id || ($v['parent_id'] == $cat_id && $v['parent_id'] != 0)) {
                unset($parent_cat[$k]);
            } else {
                $parent_cat[$k]['is_selected'] =  $v['cat_id'] == $selected_id ? true : false;
            }
        }

        return $parent_cat;
    }

    /**
     *  根据parent_id重新排序
     * @param array $data
     * @param int $parent_id    上级id
     * @param int $selected     当前选中的分类id
     * @param bool $is_clear    是否清空
     * @return array            排序后的数组
     */
    public function resortCate($data = [], $parent_id = 0, $is_clear = true) {
        static $tree = [];
        if ($is_clear) {
            $tree = [];
        }

        foreach($data as $key=>$val) {
            if ($val['parent_id'] == $parent_id) {
                $tree[] = $val;
                $this->resortCate($data, $val['cat_id'], false);
            }
        }

        return $tree;
    }

}