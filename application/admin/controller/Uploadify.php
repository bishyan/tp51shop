<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/23
 * Time: 16:34
 */

namespace app\admin\controller;


class Uploadify extends Base
{
    public function upload()
    {
        $func = input('func');
        $path = input('path', 'temp');
        $image_upload_limit_size = config('image_upload_limit_size');
        $fileType = input('fileType', 'Images');
        if ($fileType == 'Flash') {
            $upload =  url('/Admin/Ueditor/videoUp', ['savepath'=>$path, 'pictitle'=>'banner', 'dir'=>'video'], false);
            $type = 'mp4, 3gp, flv, avi, wmv';
        } else {
            $upload =  url('/Admin/Ueditor/imageUp', ['savepath'=>$path, 'pictitle'=>'banner', 'dir'=>'images'], false);
            $type = 'jpg,png,gif,jpeg';
        }
        $info = [
            'num'       => input('num/d'),
            'fileType'  => $fileType,
            'title'     => '',
            'upload'    => $upload,
            'fileList'  => url('/Admin/Uploadify/fileList', ['path'=>$path], false),
            'size'      => $image_upload_limit_size . 'M',
            'type'      => $type,
            'input'     => input('input'),
            'func'      => empty($func)? 'undefined' : $func,
        ];

        $this->assign('info', $info);
        return $this->fetch();
    }

    public function fileList()
    {
        $type = input('type', 'Images');
        switch ($type) {
            case 'Images':  //图片
                $allowfiles = 'png|jpg|jpeg|gif|bmp';
                break;
            case 'Flash':
                $allowfiles = 'mp4|3gp|fvl|wmv|flash|swf';
                break;
            default:  //文件
                $allowfiles = '.+';
        }

        $key = empty(input('key')) ? '' : input('key');
        $path = 'upload/' . input('path', 'temp');
        // 获取文件列表
        $files = $this->getfiles($path, $allowfiles, $key);
        if (count($files) == 0) {
            ajaxReturn([
                'state' => '没有相关文件',
                'list'  => [],
                'start' => 0,
                'total' => 0
            ]);
        } else {
            // 获取指定范围的列表
            $listSize = 100000;
            $size =
            $size = input('size', $listSize);
            $start = input('start',0);
            $end = $start + $size;

            $len = min($end, count($files));
            for ($i = 0, $list = []; $i < $len; $i++) {
                $list[] = $files[$i];
            }

            ajaxReturn([
                'state' => 'SUCCESS',
                'list' => $list,
                'start' => $start,
                'total' => count($files)
            ]);
        }
    }

    private function getFiles($path, $allowFiles, $key, $ignore=[], &$files=[])
    {
        $upload_path = $this->app->getRootPath() . 'public/';
        //dump($upload_path . $path);
        if (!is_dir($upload_path . $path)){
            return null;
        }

        if(substr($path, strlen($path) - 1) != '/') {
            $path .= '/';
        }

        $handle = opendir($path);
        while(false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $upload_path . $path . $file;
                if (is_dir($path2) && !in_array($file, $ignore)) {
                    $this->getFiles($path . $file, $allowFiles, $key, $ignore, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file) && preg_match("/.*".$key.".*/i", $file)) {
                        $files[] = [
                            'url' => '/' .$path . $file,
                            'name' => $file,
                            'mtime' => filemtime($path2)
                        ];
                    }
                }
            }
        }

        return $files;
    }

    public function delUpload()
    {
        $file = $this->app->getRootPath() . 'public' .input('url', '');
        if (is_file($file)) {
            unlink($file);
            ajaxReturn(1);
        } else {
            ajaxReturn(0);
        }

    }
}