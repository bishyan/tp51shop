<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/14
 * Time: 22:35
 */

namespace app\admin\controller;
use think\Db;

class Index extends Base
{
    public function index()
    {
        $this->assign('menus', getMenus());

        return $this->fetch();
    }

    public function welcome()
    {
        $this->assign('sys_info', $this->getSysInfo());

        return $this->fetch();
    }

    protected function getSysInfo()
    {
        $sys_info['os']     = PHP_OS;
        $sys_info['domain'] = $_SERVER['HTTP_HOST'];
        $sys_info['ip'] = $this->request->ip();
        $sys_info['zlib'] = function_exists('gzclose')? 'YES' : 'NO';
        $sys_info['safe_mode'] = ini_get('safe_mode')? 'YES' : 'NO';
        $sys_info['timezone'] = function_exists('date_default_timezone_get')? date_default_timezone_get() : '未知';
        $sys_info['web_server'] = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['php_version'] = PHP_VERSION;
        $sys_info['file_upload_limit'] = ini_get('file_uploads')? ini_get('upload_max_filesize') : '未知';
        $sys_info['memory_limit'] = ini_get('memory_limit');
        $mysqlinfo = Db::query('SELECT VERSION() as version');
        $sys_info['mysql_version'] = $mysqlinfo[0]['version'];
        $sys_info['max_execute_time'] = ini_get('max_execution_time') . 's';
        $sys_info['curl'] = function_exists('curl_init')? 'YES' : 'NO';
        if (function_exists('gd_info')) {
            $gd = gd_info();
            $sys_info['gd_info'] = $gd['GD Version'];
        } else {
            $sys_info['gd_info'] = '未知';
        }

        //dump($sys_info);
        return $sys_info;
    }
}