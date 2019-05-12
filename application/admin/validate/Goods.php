<?php
/**
 *  商品验证器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/30
 * Time: 22:45
 */

namespace app\admin\validate;
use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        'goods_name' => 'require|min:3|max:150|unique:goods',
        'cat_id'     => 'number|gt:0',
        'goods_sn'   => 'unique:goods|max:20',
        'shop_price' => 'float|require',
        'market_price' => 'require|float|gt:shop_price',
        'weight'        => 'regex:\d{1,10}(\.\d{1,2})?$',
        //'give_integral' => 'regex:^\d+$',
        'give_integral' => 'integer',
        'is_virtual'    => 'checkVirtualIndate',
        'exchange_integral' => 'checkExchangeIntegral',
        'is_shipping'  => 'require|checkShipping',
        'commission'        => 'checkCommission',
        'ladder_price'      => 'checkLadderAmount',
        'ladder_price'      => 'checkLadderPrice',
        'virtual_limit'     => 'between:1,10',
    ];

    protected $message = [
        'goods_name.require' => '商品名称不能为空',
        'goods_name.min' => '商品名称至少3个字符',
        'goods_name.max' => '商品名称最多150个汉字',
        'goods_name.unique' => '商品名称已存在',
        'cat_id.number' => '商品分类必须填写',
        'cat_id.gt' => '商品分类必须选择',
        'goods_sn.unique' => '商品货号重复',
        'goods_sn.max' => '商品货号最多20个字符',
        'shop_price.require' => '本店售价不能为空',
        'shop_price.float' => '本店售价格式不对',
        'market_price.require' => '市场价不能为空',
        'market_price.float' => '市场价格式不对',
        'market_price.gt' => '市场价不能小于本店价',
        'weight.regex' => '重量格式不对',
        'give_integral.integer' => '赠送积分必须是正整数',
        'exchange_integral.checkExcheangeIntegral' => '积分抵扣金额不能超过商品总额',
        'is_virtual.checkVirtualIndate' => '虚拟商品有效期不能小于当前时间',
        'is_shipping.require' => '请选择商品是否包邮',
        //'virtual_limit.checkVirtualLimit' => '虚拟商品购买上限1~10'
        'virtual_limit.between' => '虚拟商品购买上限1~10'
    ];

    // 检查虚拟商品的有效期
    protected function checkVirtualIndate($value, $rule, $data)
    {
        $virtualIndate = strtotime($data['virtual_indate']);
        if ($value == 1 && $virtualIndate < time()) {
            return false;
        } else {
            return true;
        }
    }


    protected function checkShipping($value, $rule, $data)
    {
        if ($value == 0 && empty($data['template_id'])) {
            return '请选择运费模板';
        } else {
            return true;
        }
    }

    protected function checkLadderAmount($value, $rule, $data)
    {
        if (min($value) != '' && min($value) <= 0) {
            return '无效的积分折扣单次购买个数';
        } else {
            return true;
        }
    }

    protected function checkLadderPrice($value, $tule, $data)
    {
        if (max($value) >= $data['shop_price']) {
            return '积分折扣价格阶梯最大金额不能大于商品原价';
        }
        if (min($value) != '' && min($value) <= 0) {
            return '无效的积分折扣价格';
        }
        return true;
    }

    public function checkExchangeIntegral($value, $rule, $data)
    {
        if($value > 0) {
            $goods = Db::name('goods')->where('goods_id', $data['goods_id'])->find();
            if (!in_null($goods)) {
                if ($goods['prom_type'] > 0) {
                    return '该商品正在参与其他活动, 设置兑换积分无效, 请设置为0';
                }
            }
        }

        return true;
    }
}