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
use think\Db;
// 应用公共文件

// md5加密
function encrypt($str) {
    return md5(config('MD5_CODE') . $str);
}

function getSubstr($string, $start, $length)
{
    if (mb_strlen($string, 'utf-8') > $length) {
        return mb_substr($string, $start, $length) . '...';
    } else {
        return $string;
    }
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

//php获取中文字符拼音首字母
function getFirstCharter($str){
    if(empty($str))
    {
        return '';
    }
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gb2312//TRANSLIT//IGNORE',$str);
    $s2=iconv('gb2312','UTF-8//TRANSLIT//IGNORE',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return null;
}


function ajaxReturn($value) {
    exit(json_encode($value));
}

/**
 * 获取商品的缩略图
 * @param $goods_id
 * @param $width
 * @param $height
 * @param int $item_id
 * @return mixed|string
 */
function goods_thumb_image($goods_id, $width, $height, $item_id=0)
{
    if (empty($goods_id)) {
        return '';
    }

    $path = getcwd() . "/upload/goods/thumb/{$goods_id}/";
    //dump($path);
    $thumb_name = "goods_thumb_{$goods_id}_{$item_id}_{$width}_{$height}";
    $exts = ['.jpg', '.jpeg', '.gif', '.png'];
    foreach($exts as $ext) {
        if (is_file($path . $thumb_name . $ext)) {
            return  "/upload/goods/thumb/{$goods_id}/" . $thumb_name . $ext;
        }
    }

    $original_img = '';
    if ($item_id) {
        //$original_img = Db::name('spec_goods_price')->where(['goods_id'=>$goods_id, 'item_id'=>$item_id])->cache(true, 30, 'original_img_cache')->value('spec_img');
        $original_img = Db::name('spec_goods_price')->where(['goods_id'=>$goods_id, 'item_id'=>$item_id])->value('spec_img');
    } else {
        //$original_img = Db::name('goods')->where('goods_id', $goods_id)->cache(true, 30, 'original_img_cache')->value('original_img');
        $original_img = Db::name('goods')->where('goods_id', $goods_id)->value('original_img');
    }

    if (empty($original_img)) {
        return '/images/icon_goods_thumb_empty_300.png';
    } else {
        if (!is_file(getcwd() . $original_img)) {
            return '/images/icon_goods_thumb_empty_300.png';
        }

        try{
            $image = \think\Image::open(getcwd() . $original_img);
            $thumb_name = $thumb_name . '.' . $image->type();
            // 生成缩略图
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $image->thumb($width, $height, 2)->save($path . $thumb_name, null, 100);

            return "/upload/goods/thumb/{$goods_id}/" . $thumb_name;
        } catch (think\Exception $e) {

            return $original_img;
        }
    }
}

/**
 *  获取商品相册缩略图
 */
function get_pic_thumb_images($pic_image, $goods_id, $width, $height)
{
    $path = getcwd() . "/upload/goods/thumb/{$goods_id}/";
    //dump($path);
    $thumb_name = "goods_pic_thumb_{$pic_image['img_id']}_{$width}_{$height}";
    $exts = ['.jpg', '.jpeg', '.gif', '.png'];
    foreach($exts as $ext) {
        if (is_file($path . $thumb_name . $ext)) {
            return  "/upload/goods/thumb/{$goods_id}/" . $thumb_name . $ext;
        }
    }

    $original_img = getcwd() . $pic_image['image_url'];
    if (!is_file($original_img)) {
        return '/images/icon_goods_thumb_empty_300.png';
    } else {
        try {
            $image = \think\Image::open($original_img);
            $thumb_name = $thumb_name . '.' . $image->type();
            $image->thumb($width, $height, 2)->save($path.$thumb_name, null, 100);

            return "/upload/goods/thumb/{$goods_id}/" . $thumb_name;
        }catch(think\Exception $e) {
            return $original_img;
        }

    }
}


/**
 * 递归删除文件
 * @param $path
 * @return bool
 */
function delFile($path, $del_dir = false) {

    if(!is_dir($path)) {
        return false;
    }

    $handle = opendir($path);

    while(false !== ($row = readdir($handle))) {

        if ($row != '.' && $row != '..') {
            is_dir($path . '/' . $row)? delFile($path .'/'.$row, true) : unlink($path . '/' . $row);
        }
    }
    closedir($handle);

    if ($del_dir) {
        return rmdir($path);
    }

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

/**
 * 更新商品的库存
 * @param $goods_id
 * @return bool
 */
function refresh_stock($goods_id)
{
    //查询goods_id下有没有规格项
    $count = Db::name('spec_goods_price')->where('goods_id', $goods_id)->count();
    if ($count == 0) {
        return false;
    }

    // 更新商品的库存
    $store_count= Db::name('spec_goods_price')->where('goods_id', $goods_id)->sum('store_count');
    Db::name('goods')->where('goods_id', $goods_id)->setField('store_count', $store_count);
}

function array_sort($arr, $key, $type='desc') {
    $key_value = [];
    $new_arr = [];
    foreach($arr as $k=>$v) {
        $key_value[$k] = $v[$key];
    }
    if ($type == 'asc') {
        asort($key_value);
    } else {
        arsort($key_value);
    }
    reset($key_value);
    foreach($key_value as $key=>$val) {
        $new_arr[$key] = $arr[$key];
    }

    return $new_arr;
}