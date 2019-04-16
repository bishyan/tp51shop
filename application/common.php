<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function encrypt($str) {
    return md5(config('MD5_CODE') . $str);
}

function delFile($path) {

    if(!is_dir($path)) {
        return false;
    }

    $handle = opendir($path);

    while(false !== ($row = readdir($handle))) {
        if ($row != '.' && $row != '..') {
            if (is_dir($path . '/' . $row)) {
                delFile($path .'/'.$row);
            } else {
                unlink($path . '/' . $row);
            }
        }
    }

    //$res = rmdir($path);
    closedir($handle);

    return $res;
}
