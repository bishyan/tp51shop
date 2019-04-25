<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/21
 * Time: 23:29
 */

namespace app\admin\model;
use think\Model;

class Brand extends Model
{
    protected $pk = 'brand_id';
    protected $autoWriteTimestamp = true;
    protected $field = ['brand_name', 'url', 'logo', 'sort_order', 'brand_desc', 'cat_name', 'parent_cat_id', 'cat_id', 'is_hot'];
}