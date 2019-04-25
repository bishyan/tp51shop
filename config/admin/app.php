<?php

return [
    'MD5_CODE'  => 'GG_shop',

    // 上传设置
    'image_upload_limit_size' => 3,  // 图片上传大小限制, 单位 M

    //水印配置
    'water'     => [
        'is_water'           => 1,               // 0: 不加水印, 1: 加水印
        'water_type'         => 'image',          // 水印类型, text/image
        'water_txt_font'     => 'hgzb.ttf',      // 文字水印的字体
        'water_text'         => 'guoguoshop',    // 文字水印内容
        'water_txt_size'     => 30,              //文字水印的大小
        'water_txt_color'    => '#000000',       //文字水印的颜色
        'water_width'        => 100,             // 水印宽度
        'water_height'       => 100,             // 水印高度
        'water_locate'       => 9,               // 水印的位置 1左上角, 2上居中, 3右上角, 4左居中, 5居中, 6右居中,7左下角,8下居中, 9右下角
        'water_quality'      => 100,             // 图片水印的质量
        'water_alpha'        => 60,              // 水印透明度
        'water_img'          => 'water/guo.png',  //水印图片
    ],
];