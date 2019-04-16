<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25
 * Time: 21:13
 */

namespace app\admin\model;
use think\Model;

class Admin extends Model
{
    protected $pk = 'admin_id';
    protected $autoWriteTimestamp = true;
}