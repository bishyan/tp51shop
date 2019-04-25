<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/21
 * Time: 14:04
 */

namespace app\admin\validate;
use think\Validate;

class Brand extends Validate
{
    protected $rule = [
        'brand_name|品牌名称' => 'require|max:60',
        'sort_order|排序' => 'require|max:255'
    ];
}