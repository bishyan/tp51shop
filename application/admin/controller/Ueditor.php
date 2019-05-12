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
        $savePath = input('savepath')?: input('savePath');
        $this->savePath = $savePath? $savePath .'/' : 'temp/';
        $sub_dir = input('dir', '');
        if ($sub_dir != '') {
            $this->savePath .= $sub_dir . '/';
        }
        date_default_timezone_set("Asia/Chongqing");
        error_reporting(E_ERROR | E_WARNING);
        header("Content-Type: text/html; charset=utf8");
    }


    public function index()
    {
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($this->app->getRootPath() . "public/plugins/Ueditor/php/config.json")), true);
        $action = $_GET['action'];

        switch($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;
            /* 上传图片 */
            case 'uploadimage':
                $fieldName = $CONFIG['imageFieldName'];
                $result = $this->imageUp($fieldName);
                break;
                /* 上传涂鸦 */
            case 'uploadscrawl':
                $config = array(
                    "pathFormat" => $CONFIG['scrawlPathFormat'],
                    "maxSize" => $CONFIG['scrawlMaxSize'],
                    "allowFiles" => $CONFIG['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $CONFIG['scrawlFieldName'];
                $result = $this->upBase64($config, $fieldName);
                break;
                /* 上传视频 */
            case 'uploadvideo':
                $config = array(
                    "pathFormat" => $CONFIG['videoPathFormat'],
                    "maxSize" => $CONFIG['videoMaxSize'],
                    "allowFiles" => $CONFIG['videoAllowFiles']
                );
                $fieldName = $CONFIG['videoFieldName'];
                $result = $this->upFile($fieldName);
                break;
                /* 上传文件 */
            case 'uploadfile':
                $fieldName = $CONFIG['fileFieldName'];
                $result = $this->upFile($fieldName);
                break;

            /* 列出图片 */
            case 'listimage':
                $allowFiles = $CONFIG['imageManagerAllowFiles'];
                $listSize = $CONFIG['imageManagerListSize'];
                $path = $CONFIG['imageManagerListPath'];

                $result =$this->fileList($allowFiles,$listSize,$path);
                break;
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $CONFIG['fileManagerAllowFiles'];
                $listSize = $CONFIG['fileManagerListSize'];
                $path = $CONFIG['fileManagerListPath'];
                $result =$this->fileList($allowFiles,$listSize, $path);
                break;
            /* 抓取远程文件 */
            case 'catchimage':
                $config = array(
                    "pathFormat" => $CONFIG['catcherPathFormat'],
                    "maxSize" => $CONFIG['catcherMaxSize'],
                    "allowFiles" => $CONFIG['catcherAllowFiles'],
                    "oriName" => "remote.png"
                );
                $fieldName = $CONFIG['catcherFieldName'];
                $result = $this->saveRemote($config, $fieldName);
                break;

            default:
                ajaxReturn(['state'=> '请求地址出错']);
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }


    private function fileList($allowFiles,$listSize,$path)
    {
        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;

        /* 获取文件列表 */
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        $path = $this->app->getRootPath() . 'public/' . (substr($path, 0, 1) == "/" ? substr($path, 1) : $path);
        //dump($path);
        $files = $this->getFiles($path, $allowFiles);
        if (!count($files)) {
            ajaxReturn([
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            ]);
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }

        /* 返回数据 */
        ajaxReturn([
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ]);
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private function getFiles($path, $allowFiles)
    {
        static $files = [];

        if (!is_dir($path)) {
            return null;
        }
        if(substr($path, strlen($path) - 1) != '/') {
            $path .= '/';
        }

        $handle = opendir($path);
        while(false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getFiles($path2,$allowFiles);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = [
                            'url' => substr($path2, strlen($this->app->getRootPath() . 'public')),
                            'mtime' => filemtime($path2)
                        ];
                    }
                }
            }
        }

        return $files;
    }

    private function saveRemote($config, $fieldName) {
        $imgUrl = htmlspecialchars($fieldName);
        $imgUrl = str_replace("&amp;", "&", $imgUrl);

        // http开头验证
        if (strpos($imgUrl, 'http') !== 0) {
            ajaxReturn(['state'=>'链接不是http链接']);
        }

        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            ajaxReturn(['state'=>'非法 URL']);
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            ajaxReturn(['state'=>'非法IP']);
        }

        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            ajaxReturn(['state'=>'链接不可用']);
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            ajaxReturn(['state'=>'链接contentType不正确']);
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);

        $oriName = $m? $m[1] : '';
        $fileSize = strlen($img);
        $ext = strtolower(strrchr($config['oriName'], '.'));
        $fileName = uniqid() . $ext;
        $upload_path = config('upload_path') . 'remote/';
        $fullName = $upload_path . $fileName;

        if ($fileSize >= $config['maxSize']) {
            ajaxReturn(['state'=>'文件大小超出网站限制']);
        }

        //创建目录失败
        if (!file_exists($upload_path) && !mkdir($upload_path, 0777, true)) {
            ajaxReturn(['state'=>'目录创建失败']);
        } else if (!is_writeable($upload_path)) {
            ajaxReturn(['state'=>'目录没有写权限']);
        }

        //移动文件
        if (!(file_put_contents($fullName, $img) && file_exists($fullName))) {
            ajaxReturn(['state'=>'写入文件内容错误']);
        } else {
            ajaxReturn([
                'state' => 'SUCCESS',
                'url'   => '/upload/remote/'. $fileName,
                'title' => $fileName,
                'original' => $oriName,
                'type' => $ext,
                'size' => $fileSize,
            ]);
        }
    }

    private function upBase64($config, $fieldName)
    {
        $base64Data = $this->request->post($fieldName);
        $img = base64_decode($base64Data);

        $upload_path = config('upload_path') . 'scrawl/';
        $ext = strtolower(strrchr($config['oriName'], '.'));
        $file_name = uniqid() . $ext;
        $full_name = $upload_path . $file_name;

        //检查文件大小是否超出限制
        $file_size = strlen($img);
        if ($file_size >= $config['maxSize']) {
            ajaxReturn(['state'=>'文件大小超出网站限制']);
        }

        //创建目录失败
        if (!file_exists($upload_path) && !mkdir($upload_path, 0777, true)) {
            ajaxReturn(['state'=>'目录创建失败']);
        } else if (!is_writeable($upload_path)) {
            ajaxReturn(['state'=>'目录没有写权限']);
        }

        //移动文件
        if (!(file_put_contents($full_name, $img) && file_exists($full_name))) {
            ajaxReturn(['state'=>'写入文件内容错误']);
        } else {
            ajaxReturn([
                'state' => 'SUCCESS',
                'url'   => '/upload/scrawl/'. $file_name,
                'title' => $file_name,
                'original' => $config['oriName'],
                'type' => $ext,
                'size' => $file_size,
            ]);
        }
    }

    private function upFile($fieldName) {
        $file = $this->request->file($fieldName);
        if (empty($file)) {
            $state = "ERROR";
            ajaxReturn(['state'=>$state]);
        } elseif(strtolower(pathinfo($file->getInfo('name'), PATHINFO_EXTENSION)) == 'php') {
            ajaxReturn(['state' => 'ERROR后缀不允许']);
        }

        // 移动文件到指定目录
        $save_path = $this->savePath . date('Y') . '/' . date('m-d');
        $info = $file->rule(function($file){
            return md5(uniqid(microtime(), true));
        })->move(config('upload_path') . $save_path);
        if($info) {
            $data = [
                'state' => 'SUCCESS',
                'url'   => '/upload/'. $save_path .$info->getSaveName(),
                'title' => $info->getFilename(),
                'original' => $info->getFilename(),
                'type' => '.' .$info->getExtension(),
                'size' => $info->getSize()
            ];
        } else {
            $data = ['state' => $file->getError()];
        }
        ajaxReturn($data);
    }

    public function videoUp($filename='file')
    {
        $title = input('pictitle');

        $file = $this->request->file($filename);
        $upload_max_filesize = @ini_get('file_uploads')? ini_get('upload_max_filesize') : 'unknown';
        $success = $this->validate(
            ['file'=>$file],
            ['file'=>'fileSize:' . $upload_max_filesize * 1024 *1024 .'|fileExt:mp4,3gp,flv,avi,wmv'],
            ['file.fileSize'=>'上传文件不能超过'.$upload_max_filesize .'M', 'file.fileExt'=>'上传的视频文件的后缀必须为mp4,3gp,flv,avi,wmv']
        );

        if (true !== $success) {
            $state = implode(',', $success);
        } else {
            $res = save_upload_image($file, $this->savePath);
            $state = $res['state'];
            $url = $res['url'];
        }

        $return_data['title'] = $title;
        $return_data['state'] = $state;
        $return_data['url'] = $url;

        ajaxReturn($return_data);
    }

    public function imageUp($filename='file')
    {
        $title = input('pictitle');
        $path = input('dir');

        $file = $this->request->file($filename);
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

}