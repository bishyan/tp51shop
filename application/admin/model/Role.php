<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/8
 * Time: 22:02
 */

namespace app\admin\model;
use think\Model;

class Role extends Model
{
    protected $pk = 'role_id';
    protected $autoWriteTimestamp = true;
}