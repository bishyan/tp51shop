<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/23
 * Time: 21:03
 */

namespace app\index\controller;
use think\Controller;
use think\captcha\Captcha;

class User extends Controller
{
    public function pop_login()
    {
        $referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : url('/index/User/index');
        $this->assign('referer', $referer);
        $this->request->action();
        return $this->fetch();
    }

    public function checkLogin()
    {
        // 验证验证码
        $verify_code = input('verify_code');
        $verify = new Captcha();
        if (!$verify->check($verify_code, 'ggshop_login')) {
            ajaxReturn(['status'=>0, 'msg'=>'验证码错误!']);
        }

        $userModel = $this->app->model('user');
        $res = $userModel->checkUser(input('username', '', 'trim'), input('password', '', 'trim'));
        if ($res['status'] == 1) {
            $res['url'] = input('post.referurl');
        }
        ajaxReturn($res);
    }

    public function logout()
    {
        session('user', null);

        setcookie('user_id', '', -1, '/');
        setcookie('uname', '', -1, '/');
        setcookie('cn', '', -1, '/');
        setcookie('PHPSESSIN', '', -1, '/');
        $this->redirect('/');
    }


    public function verify()
    {
        $config = [
            'useCurve' => false,
            'useNoise' => false,
            'fontSize' => 24,
            'length' =>4
        ];

        $verify = new Captcha($config);

        return $verify->entry('ggshop_login');
    }
}