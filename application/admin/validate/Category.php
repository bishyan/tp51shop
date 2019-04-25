<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 14:24
 */

namespace app\admin\validate;
use think\Validate;

class Category extends Validate
{
    protected $rule = [
        'cat_name|分类名称' => [
            'require',
            'max' => 60,
        ],
        'mobile_name|手机分类名称' => [
            'require',
            'max' => 50,
        ],
        'sort_order|排序' => [
            'require',
            'between' => '0, 255'
        ],
        'commission_rate|分佣比例' => [
            'require',
            'between' => '0,100'
        ],
    ];
}