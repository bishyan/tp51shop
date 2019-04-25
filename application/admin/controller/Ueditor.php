<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/23
 * Time: 21:00
 */

namespace app\admin\controller;


class Ueditor extends Base
{
    private $savePath = 'temp/';
    public function initialize()
    {
        $this->savePath = input('savepath', '')? input('savepath').'/' : 'temp/';
    }

    public function imageUp()
    {
        $title = input('pictitle');
        $path = input('dir');

        $file = $this->request->file('file');
        $upload_max_filesize = @ini_get('file_uploads')? ini_get('upload_max_filesize') : 'unknown';
        $success = $this->validate(
            ['file'=>$file],
            ['file'=>'image|fileSize:' . $upload_max_filesize * 1024 *1024 .'|fileExt:jpg,jpeg,gif,png'],
            ['file.image'=>'上传文件必须为图片', 'file.fileSize'=>'上传文件不能超过'.$upload_max_filesize .'M', 'file.fileExt'=>'上传文件的后缀必须为jpg,png,jpeg,gif']
        );

        if (true !== $success) {
            $state = implode(',', $success);
        } else {
            $res = save_upload_image($file, $this->savePath);
            
            $state = $res['state'];
            $url = $res['url'];
        }

        $return_data['title'] = $title;
        $return_data['path'] = $path;
        $return_data['state'] = $state;
        $return_data['url'] = $url;

        ajaxReturn($return_data);
    }


    public function vedioUp()
    {

    }
}