<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/25
 * Time: 11:23
 */

namespace app\common\model;
use think\Model;
use think\Db;

class User extends Model
{
    protected $table = 'users';
    protected $pk = 'user_id';
    protected $autoWriteTimestamp = 'true';
    protected $createTime = 'reg_time';
    protected $updateTime = false;

    public function checkUser($username, $password) {
        if (!$username || !$password) {
            return ['status'=>0, 'msg'=>'账号或密码为空!'];
        }
        $user = Db::name('users')->where('mobile', $username)->whereOr('email', $username)->find();
        if (is_null($user)) {
            $result = ['status'=>-1, 'msg'=>'账号不存在!'];
        } elseif (encrypt($password) != $user['password']) {
            $result = ['status'=>-2, 'msg'=>'密码错误!'];
        } elseif ($user['is_lock'] == 1) {
            $result = ['status'=>-3, 'msg'=>'账号已被锁定!'];
        } else {
            //判断是否清空积分
            $this->isEmptyingIntegral($user);
            //
            $level_name = Db::name('user_level')->where('level_id', $user['level'])->value('level_name');
            $user['level_name'] = is_null($level_name)? '' : $level_name;

            Db::name('users')->where('user_id', $user['user_id'])->update([
                'last_login'=>time(),
                'last_ip' => request()->ip(),
            ]);

            session('user', $user);
            setcookie('user_id', $user['user_id'], null, '/');
            setcookie('is_distribute', $user['is_distribute'], null, '/');
            $nickname = empty($user['nickname'])? $username : $user['nickname'];
            setcookie('uname', $nickname, null, '/');
            setcookie('cn', 0);

            $result = ['status'=> 1, 'msg'=>'登陆成功!', 'result'=>$user];
        }

        return $result;
    }


    public function isEmptyingIntegral($user)
    {

    }
}