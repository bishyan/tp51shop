<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 21:29
 */

namespace app\common\logic;


class ModuleLogic1
{
    /** 所有模块
     * @var
     */
    private $modules;

    /** 可见模块
     * @var
     */
    private $showModules;

    public function getModules($onlyShow = true) {
        if ($this->modules) {
            return $onlyShow? $this->showModules : $this->modules;
        }

        $isShow = 1;
        $modules = [
            [
                'name'  => 'admin', 'title' => '平台后台', 'show' => 1,
                'privilege' => [
                    'system'=>'系统设置','content'=>'内容管理','goods'=>'商品中心','member'=>'会员中心','finance'=>'财务管理',
                    'order'=>'订单中心','marketing'=>'营销推广','tools'=>'插件工具','count'=>'统计报表','distribut'=>'分销中心','weixin'=>'微信管理'
                ],
            ],
            [
                'name'  => 'home', 'title' => 'PC端', 'show' => $isShow,
                'privilege' => [
                    'buy' => '购物流程', 'user' => '用户中心', 'article' => '文章功能', 'activity' => '活动优惠',
                    'virtual' => '虚拟商品', 'wechat' => '微信功能'
                ],
            ],
            [
                'name'  => 'mobile', 'title' => '手机端','show' => $isShow,
                'privilege' => [
                    'buy' => '购物流程', 'user' => '用户中心', 'article' => '文章功能', 'activity' => '活动优惠', 'distribut' => '分销功能',
                    'virtual' => '虚拟商品'
                ],
            ],
            [
                'name'  => 'api', 'title' => 'api接口', 'show' => $isShow,
                'privilege' => [
                    'buy' => '购物流程', 'user' => '用户中心', 'article' => '文章功能', 'activity' => '活动优惠', 'distribut' => '分销功能',
                    'virtual' => '虚拟商品', 'wechat' => '微信功能', 'message' => '消息推送', 'supplier' => '供应商', 'app' => '应用管理'
                ],
            ],
        ];

        $this->modules = $modules;
        foreach($modules as $key=>$module) {
            if (!$module['show']) {
                unset($modules[$key]);
            }
        }
        $this->showModules = $modules;

        return $onlyShow? $this->showModules : $this->modules;
    }

    public function getModule($type, $onlyShow = true) {
        if (!$this->isModuleExist($type, $onlyShow)) {
            return [];
        } else {
            return $onlyShow? $this->showModules[$type] : $this->modules[$type];
        }
    }

    protected function isModuleExist($type, $onlyShow = true) {
        return array_key_exists($type, $this->getModules($onlyShow));
    }

    public function getPrivilege($type, $onlyShow = true) {
        if (!$this->isModuleExist($type, $onlyShow)) {
            return [];
        } else {
            return $onlyShow? $this->showModules[$type]['privilege'] : $this->modules[$type]['privilege'];
        }
    }
}