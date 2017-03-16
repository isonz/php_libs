<?php
set_time_limit(30);  //30 second
session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
if(!$user){header("Location: /");exit;}

//------------------------------------- start config
$php_path = dirname(__FILE__) . '/';
//文件保存目录路径
$save_path = $php_path . '../../upload/'.date('Y').'/'.date('m').'/'.date('d').'/';
//文件保存目录URL
$save_url = '/misc/upload/'.date('Y').'/'.date('m').'/'.date('d').'/';

//定义允许上传的文件扩展名
$ext_arr = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
	'flash' => array('swf', 'flv'),
	'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
	'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
);
//最大文件大小
$max_size = 10000000;
//------------------------------------ end config


require_once 'JSON.php';
if (!@file_exists($save_path)) {
    mkdir($save_path,0755, true);
}
$save_path = realpath($save_path) . '/';

//-----------------------------------------------------
$pics = $_POST['pic'];
$pics = explode('%7C', $pics);
$result = array();

foreach ($pics as $pic)
{
    if (false !== stripos($pic, "//".$_SERVER['HTTP_HOST'])) continue;
    $rs = download($pic, $save_path);
    if($rs) $result[] = $save_url . $rs;
}
$urls = implode('|', $result);
header('Content-type: text/html; charset=UTF-8');
$json = new Services_JSON();
echo $json->encode(array('error' => 0, 'url' => $urls));
exit;


function download($imgurl, $save_path, $max_size=1000000, $ext_arr=array())
{
    $filetype = getFiletype($imgurl);
    $tmp_name = @file_get_contents($imgurl, null, null, null, $max_size);
    if (@is_dir($save_path) === false) exit("上传目录不存在。");
    if (@is_writable($save_path) === false) exit("上传目录没有写权限。");

    if ($tmp_name) {
        $new_file_name = microtime(true) . rand(100, 999) . '.' . $filetype;
        $file_path = $save_path . $new_file_name;

        $fp = @fopen($file_path, 'w');
        @fwrite($fp, $tmp_name);
        @fclose($fp);
        @chmod($file_path, 0644);
        return $new_file_name;
    }
}

function alert($msg)
{
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}

function getFiletype($filename)
{
    $tempArray = explode(".",$filename);//分割字符串
    if (count($tempArray)>1) {
        $fileType = $tempArray[count($tempArray)-1];//得到文件扩展名
        return strtolower($fileType);
    }
    return "";
}