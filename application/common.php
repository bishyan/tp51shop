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

// md5加密
function encrypt($str) {
    return md5(config('MD5_CODE') . $str);
}


//function saveDataToTable($data, $table_name, $id_name) {
//    if (isset($data[$id_name])) {
//        # 修改
//        Db::name('spec')->update($data);
//        foreach($data['item'] as $item) {
//            if ($item['item'] != '') {
//                if (isset($item['id'])) {
//                    # 原有的
//                    Db::name('spec_item')->update([
//                        'item' => $item['item'],
//                        'id' => $item['id'],
//                        'spec_id' => $data['spec_id']
//                    ]);
//                } else {
//                    # 添加新的规格项
//                    Db::name('spec_item')->insert([
//                        'item'=>$item['item'],
//                        'spec_id'=>$data['spec_id']
//                    ]);
//                }
//            }
//        }
//    } else {
//        # 增加
//        $spec_id = Db::name('spec')->insertGetId();
//        if ($spec_id) {
//            foreach ($data['item'] as $key => $item) {
//                dump($item[$key]);
//                if ($item['item'] != '') {
//                    Db::name('spec_item')->insert([
//                        'item' => $item['item'],
//                        'spec_id' => $spec_id
//                    ]);
//                }
//            }
//        }
//    }
//}


/**
 * 递归删除文件
 * @param $path
 * @return bool
 */
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

    return true;
}

/**
 * 上传文件到指定目录
 * @param $file         文件对象
 * @param $save_path    存储地址
 * @return array
 */
function save_upload_image($file, $save_path)
{
    $return_url = '';
    $state = "SUCCESS";
    $upload_path = app()->getRootPath() . 'public/upload/';
    $save_path = $save_path . date('Y') . '/' . date('m-d') .'/';

    //移动文件到指定目录
    $info = $file->rule(function(){
        return md5(uniqid(microtime(),true));
    })->move($upload_path . $save_path);
    if (!$info) {
        $state = 'ERROR' . $file->getError();
    } else {
        $return_url = '';
        $file_name = $info->getSaveName();
        $save_name = $save_path . $file_name;
        $ext = strstr($file_name, '.');

        // 给商品图片添加水印
        if ($save_path == 'goods/' && $ext != '.gif') {
            waterImage($upload_path . $save_name);
        }
        $return_url = '/upload/' . $save_name;
    }

    return [
        'state' => $state,
        'url'   => $return_url
    ];
}

/**
 * 添加水印
 * @param $img_path
 */
function waterImage($img_path) {
    $image = \think\Image::open($img_path);
    $water = config('water');
    //dump($water);
    if ($water['is_water'] == 1 && $image->width() > $water['water_width'] && $image->height() > $water['water_height']) {
        if ($water['water_type'] == 'text') {
            $font = './hgzb.ttf';
            if (file_exists($font)) {
                $size = $water['water_txt_size'] ?: 30;
                $color = $water['water_txt_color'] ?: '#000000';
                if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                    $color = '#000000';
                }
                $alpha = intval($water['water_alpha'] * (127/100));
                $color .= dechex($alpha);
                dump($color);
                $image->text($water['water_text'], $font, $size, $color, $water['water_locate'])->save($img_path);
            }
        } else {
            # 图片水印
            $waterPath = app()->getRootPath(). 'public/upload/' .  $water['water_img'];
            $quality = $water['water_quality']?: 80;
            $image->water($waterPath, $water['water_locate'], $water['water_alpha'])->save($img_path);
        }
    }
}