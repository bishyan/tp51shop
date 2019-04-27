<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/25
 * Time: 23:02
 */

namespace app\admin\validate;
use think\Validate;

class Type extends Validate
{
    protected $rule = [
        'type_name|商品模型名称' => 'require|max:60'
    ];
}