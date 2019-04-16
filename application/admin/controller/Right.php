<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/26
 * Time: 17:33
 */

namespace app\admin\controller;
use app\common\logic\ModuleLogic1;
use app\admin\validate\Right as RightValidate;
use think\Validate;

//use think\facade\Validate;

class Right extends Base
{
    public function rightList()
    {
        $type = input('type', 0);
        $moduleLogic = new ModuleLogic1();
        $module = $moduleLogic->getModule($type);  //当前模块
        $modules = $moduleLogic->getModules();  //所有模块
        //dump($modules);
        if (!$module) {
            exit('模块不存在或不可见');
        } else {

            if (input('name', '')) {
                //$where = ['name','like', '%'.input('name').'%'];
                $rightList = model('Right')->whereLike('name|right', '%'.input('name').'%')->where('type', $type)->select();
            } else {
                $rightList = model('Right')->where('type', $type)->select();
            }
            $this->assign('rights', $rightList);
            $this->assign('module', $module);
            $this->assign('modules', $modules);
        }

        return $this->fetch();
    }

    // 展示添加权限表单
    public function addRight()
    {
        $type = input('type', 0);
        $moduleLogic = new ModuleLogic1();
        $modules = $moduleLogic->getModules();
        $group = $moduleLogic->getPrivilege($type);

        $controllerPath = app()->getAppPath() . $modules[$type]['name'] . '/controller';
        $controllers = [];
        if (is_dir($controllerPath)) {
            $dh = opendir($controllerPath);
            while(($row = readdir($dh)) !== false) {
                if (!in_array($row, ['.', '..', '.svn'])) {
                    $controllers[] = basename($row, '.php');
                }
            }
        }

        $this->assign('controllers', $controllers);
        $this->assign('group', $group);
        $this->assign('modules', $modules);

        return $this->fetch();
    }

    public function ajax_get_action() {
        $controller = input('controller');
        $type = input('type', 0);
        $module = (new ModuleLogic1())->getModule($type);
        //dump($module);
        if ($module) {
            $className = "app\\" . $module['name'] . "\\controller\\" . $controller;
            $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);
            $selectClass = [];
            foreach($methods as $method) {
                if ($method->class == $className) {
                    if ($method->name != '__construct' && $method->name != 'initialize') {
                        $selectClass[] = strtolower($method->name);
                    }
                }
            }

            $html = '';
            foreach($selectClass as $key=>$val) {
                $html .= "<li><label><input type='checkbox' class='checkbox' name='act_list' value=".$val." />".$val."</label></li>";
                if ($val && strlen($val) > 18) {
                    $html .= "<li></li>";
                }
            }

            exit($html);
        } else {
            exit('模块不存在, 或不可见');
        }
    }


    public function insertRight()
    {
        if ($this->request->isPost()) {
            // 验证数据
            //$validate = Validate::make(['name' => 'require|max:60','right' => 'require'], ['name.require'=>'权限名称不能为空','right.require'=>'请选择权限码']);
            //$res = $this->validate(input('post.'), ['name' => 'require|max:60',/*'right' => 'require'*/], ['name.require'=>'权限名称不能为空','right.require'=>'请选择权限码'], true);
            //$res = $validate->batch()->check(input('post.'));
            $validate = new RightValidate();
            $res = $validate->batch()->check(input('post.'));

            if(!$res) {
                $err_msg = $validate->getError();
                //$errStr = implode(',', $err_msg);
                //ajaxReturn(['status'=>0, 'msg'=>$error[0], 'data'=>$res]);
                $this->error(implode(',', $err_msg));
            }

            $data = input('post.');
            $data['right'] = implode(',', $data['right']);

            $right = db('system_menu')->where('name', $data['name'])->find();
            //dump($right); exit;
            if (is_null($right)) {
                $res = model('Right')->save($data);
                if ($res){
                    $this->success('权限资源添加成功!', url('Right/rightList'));
                } else {
                    $this->error('权限资源添加失败!');
                }
            } else {
                $this->error('已存在相同名称的权限资源!');
            }

        } else {
            ajaxReturn('非法请求');
        }
    }

    public function editRight() {
        $moduleLogic = new ModuleLogic1();
        $modules = $moduleLogic->getModules();
        $privilege = $moduleLogic->getPrivilege(input('type', 0));

        // 获取当前模块的类名
        $controllerPath = app()->getAppPath() . $modules[input('type', 0)]['name'] . '/controller';
        $controllers = [];
        if (is_dir($controllerPath)) {
            $dh = opendir($controllerPath);
            while(($row = readdir($dh)) !== false) {
                if (!in_array($row, ['.', '..', '.svn'])) {
                    $controllers[] = basename($row, '.php');
                }
            }
        }

        $right_info = db('system_menu')->find(input('id'));         // 当前权限的信息
        $right_info['right'] = explode(',', $right_info['right']);

        $this->assign('controllers', $controllers);
        $this->assign('modules', $modules);
        $this->assign('group', $privilege);
        $this->assign('right_info', $right_info);
        return $this->fetch();
    }

    public function updateRight() {
        if ($this->request->isPost()) {
            // 验证数据
            $validate = new RightValidate();
            $res = $validate->batch()->check(input('post.'));
            if ($res === false) {
                $err_msg = $validate->getError();
                $this->error(implode(',', $err_msg));
            } else {
                $data = input('post.');
                $data['right'] = implode(',', $data['right']);

                $right = db('system_menu')->where('name',$data['name'])->where( 'id','<>', $data['id'])->find();
                if ($right == null) {
                    $res = model('Right')->isUpdate()->save($data);
                    if ($res) {
                        $this->success('权限资源修改成功', url('Right/rightList'));
                    } else {
                        $this->error('权限资源修改失败');
                    }
                } else {
                    $this->error('已存在相同名称的权限资源');
                }
            }

        }
    }

    public function delRight() {
        $id = input('post.del_id');
        if (!empty($id)) {
            $res = db('system_menu')->where('id', $id)->delete();
            if ($res) {
                ajaxReturn(1);
            } else {
                ajaxReturn('删除失败');
            }
        } else {
            ajaxReturn('参数有误');
        }
    }
}