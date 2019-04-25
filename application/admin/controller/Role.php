<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/8
 * Time: 20:49
 */

namespace app\admin\controller;
use app\common\logic\ModuleLogic;
use app\admin\validate\Role as RoleValidate;
use think\Paginator;

class Role extends Base
{
    public function roleList()
    {
        $roles = db('role')->order('role_id desc')->select();
        $this->assign('roles', $roles);

        return $this->fetch();
    }

    public function addRole() {
        $_rights = db('system_menu')->order('id')->select();
        foreach($_rights as $right) {
            $rights[$right['group']][] = $right;  //将同组放在一起
        }

        $group = (new ModuleLogic())->getPrivilege(0);
        $this->assign('rights', $rights);
        $this->assign('group', $group);

        return $this->fetch();
    }

    public function insertRole() {
        if ($this->request->isPost()) {
            // 数据验证
            $success = $this->validateData('Role');
            if ($success !== true) {
                $this->error(implode(',',$success));
            } else {
                $data = input('post.');
                $data['act_list'] = isset($data['right'])? implode(',', $data['right']) : '';
                if (empty($data['act_list'])) {
                    $this->error('请选择权限');
                }
            //dump($data);exit;
                $role = db('role')->where('role_name', $data['role_name'])->find();
                if (is_null($role)) {
                    $res = model('Role')->save($data);
                    if ($res) {
                        $this->success('角色添加成功', url('/admin/Role/roleList'));
                    } else {
                        $this->error('角色添加失败!');
                    }
                } else {
                    $this->error('角色已存在!');
                }
            }
        } else {
            $this->error('非法请求');
        }
    }

    public function editRole()
    {
        $role_info = db('Role')->where('role_id', input('role_id'))->find();
        if ($role_info != null) {
            $_rights = db('system_menu')->select();
            foreach ($_rights as $right) {
                if ($role_info['act_list'] != '') {
                    $right['enable'] = in_array($right['id'], explode(',', $role_info['act_list']));
                }
                $rights[$right['group']][] = $right;
            }
            //dump($rights);
            $group = (new ModuleLogic())->getPrivilege(0);

            $this->assign('group', $group);
            $this->assign('role_info', $role_info);
            $this->assign('rights', $rights);
        } else {
            $this->error('参数有误');
        }


        return $this->fetch();
    }

    public function updateRole() {
        if ($this->request->isPost()) {
            // 验证数据
            $success = $this->validateData('Role');
            if ($success !== true) {
                $this->error(implode(',',$success));
            }else {
                $data = input('post.');
                $data['act_list'] = isset($data['right'])? implode(',', $data['right']) : '';
                //dump($data);
                if (empty($data['act_list'])) {
                    $this->error('请选择权限');
                } else {
                    $role = db('Role')->where('role_id', '<>', $data['role_id'])->where('role_name',$data['role_name'])->find();
                    if ($role === null) {
                        $res = model('Role')->isUpdate()->save($data);
                        if ($res) {
                            $this->success('角色修改成功', url('/admin/Role/roleList'));
                        } else {
                            $this->error('角色修改失败');
                        }
                    } else {
                        $this->error('角色已存在!');
                    }
                }
            }
        } else {
            $this->error('非法请求');
        }
    }

    public function delRole()
    {
        $role_id = input('role_id', '');
        if ($role_id == '') {
            ajaxReturn('参数有误');
        } else {
            $admin = db('Admin')->where('role_id', $role_id)->find();
            if ($admin != null) {
                //$this->error('请先清除此角色下的管理员');
                ajaxReturn('请先清除此角色下的管理员');
            } else {
                $res = db('Role')->where('role_id', $role_id)->delete();
                if ($res) {
                    //$this->success('删除角色成功', url('/admin/Role/roleList'));
                    ajaxReturn(1);
                } else {
                    //$this->error('删除角色失败');
                    ajaxReturn('删除角色失败');
                }
            }
        }
    }
}