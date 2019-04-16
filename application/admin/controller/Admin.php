<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25
 * Time: 11:19
 */

namespace app\admin\controller;
use think\Db;
use think\captcha\Captcha;

class Admin extends Base
{
    // 管理员后台登陆
    public function login()
    {
        if (session('?admin_id') && session('admin_id') > 0) {
            $this->error('您已登陆!', url('/admin/Index/index'));
        }

        if ($this->request->isPost()) {
            $verify = new Captcha();
            if (!$verify->check(input('verify'), 'guoguo')) {
                ajaxReturn(['status=>0', 'msg'=>'验证码不正确']);
            }

            $admin_name = input('post.admin_name');
            $admin = db('Admin')->where('admin_name', $admin_name)->find();
            if ($admin !== null) {
                //验证密码
                if ($admin['password'] == encrypt(input('post.password'))) {
                    session('last_login', date('Y-m-d H:i:s', $admin['last_login']));
                    session('last_ip', $admin['last_ip']);
                    db('Admin')->where('admin_id', $admin['admin_id'])->update([
                        'last_ip' => $this->request->ip(),
                        'last_login' => time()
                    ]);
                    $right = db('Role')->where('role_id', $admin['role_id'])->find();
                    session('act_list', $right['act_list']);
                    session('admin_id', $admin['admin_id']);
                    session('admin_name', $admin['admin_name']);
                    //session();
                    //$this->redirect('/admin/Index/index');
                    ajaxReturn(['status'=>1, 'url'=>url('/admin/Index/index')]);
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>'密码错误!']);
                }
            } else {
                ajaxReturn(['status'=>0, 'msg'=>'用户不存在!']);
            }
            exit;
        }
        return $this->fetch();
    }

    public function verify() {
        $config = [
            'length' => 4,
            'fontSize' => 32,
            'useNoise' => false,
            'useCurve' => false,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry('guoguo');
    }

    public function logout() {
        session(null);
        session_destroy();

        $this->redirect(url('/admin/admin/login'));
    }

    public function adminList()
    {
        $admins = db('Admin')->select();
        $roles = db('Role')->column('role_id, role_name');
        //dump($roles);
        $this->assign('roles', $roles);
        $this->assign('admins', $admins);

        return $this->fetch();
    }

    public function addAdmin()
    {
        $roles = db('Role')->select();
        $this->assign('roles', $roles);

        return $this->fetch();
    }

    public function insertAdmin() {
        if ($this->request->isPost()) {
            if (($success = $this->validateData('Admin.insert')) !== true) {
                $msg = array_values($success);
                ajaxReturn(['status'=>0, 'msg'=>$msg[0], 'result'=>$success]);
            } else {
                $data = input('post.');
                if ($data['password'] != '') {
                    $data['password'] = encrypt($data['password']);
                }
                //dump($data); exit;
                $admin = db('Admin')->where('admin_name', $data['admin_name'])->find();
                if (is_null($admin)) {
                    $res = model('Admin')->save($data);
                    if ($res) {
                        ajaxReturn(['status'=>1, 'msg'=>'管理员添加成功', 'url'=>url('/admin/Admin/adminList')]);
                    } else {
                        ajaxReturn(['status'=>0, 'msg'=>'管理员添加失败']);
                    }
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>'用户名已存在']);
                }

                //ajaxReturn();
            }
        } else {
            $this->error('非法访问');
        }
    }

    public function editAdmin()
    {
        $admin_id = input('admin_id');
        if ($admin_id != '') {
            $admin_info = db('admin')->where('admin_id', $admin_id)->find();
            $roles = db('role')->field('role_id, role_name')->select();

            $this->assign('admin_info', $admin_info);
            $this->assign('roles', $roles);
        } else {
            ajaxReturn(['status'=>0, 'msg'=>'参数有误']);
        }

        return $this->fetch();
    }

    public function updateAdmin()
    {
        //dump(input('post.')); exit;
        if ($this->request->isPost()) {
            //dump(input('post.'));exit;
            // 验证数据
            $success = $this->validateData('Admin.update');
            if ($success !== true) {
                $msg = array_values($success);
                ajaxReturn(['status'=>0, 'msg'=>$msg[0], 'result'=>$success]);
            } else {
                $data = input('post.');
                if (empty($data['password'])) {
                    unset($data['password']);
                } else {
                    $data['password'] = encrypt($data['password']);
                }

                $admin = db('Admin')->where('admin_name', $data['admin_name'])->where('admin_id', '<>', $data['admin_id'])->find();
                if ($admin === null) {
                    $res = model('admin')->isUpdate()->save($data);
                    if ($res) {
                        ajaxReturn(['status'=>1, 'msg'=>'管理员修改成功', 'url'=>url('/admin/Admin/adminList')]);
                    } else {
                        ajaxReturn(['status'=>0, 'msg'=>'管理员修改失败']);
                    }
                } else {
                    ajaxReturn(['status'=>0, 'msg'=>'用户名已存在']);
                }
            }
        }
    }

    public function delAdmin() {
        $admin_id = input('post.admin_id', '');
        //dump($admin_id);exit;
        if (empty($admin_id)) {
            ajaxReturn(['status'=>0, 'msg'=>'非法参数']);
        } else {
            $res = db('Admin')->where('admin_id', $admin_id)->delete();

            if ($res) {
                ajaxReturn(['status'=>1, 'msg'=>'管理员删除成功']);
            } else {
                ajaxReturn(['status'=>0, 'msg'=>'管理员删除失败']);
            }
        }
    }

    public function modify_pwd() {
        if ($this->request->isPost())
        {
            $data = input('post.');
            $adminObj = Db::name('Admin');
            $password = $adminObj->where('admin_id', session('admin_id'))->value('password');
            if (encrypt($data['old_pw']) != $password) {
                ajaxReturn(['status'=>0, 'msg'=>'原密码错误, 请重新输入!']);
            }

            if ($data['new_pw'] != $data['new_pw2']) {
                ajaxReturn(['status'=>0, 'msg'=>'两次密码输入不一致!']);
            }

            $res = $adminObj->setField('password', encrypt($data['new_pw']));
            if ($res) {
                ajaxReturn(['status'=>1]);
            } else {
                ajaxReturn(['status'=>0, 'msg'=>'密码修改失败!']);
            }
            exit;
        }


        return $this->fetch();
    }
}