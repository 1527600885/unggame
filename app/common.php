<?php
// +----------------------------------------------------------------------
// | OneKeyAdmin [ Believe that you can do better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2023 http://onekeyadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MUKE <513038996@qq.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

use app\common\lib\Redis;
use think\Image;

use app\api\model\User as UserModel;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use GeoIp2\Database\Reader;
use app\api\model\CapitalFlow as CapitalFlowmodel;
use think\facade\Db;


function upimage($filePath, $savePath, $istrumb = false, $nopubpath = '')
{
    require root_path() . 'extend/Aws/aws-autoloader.php';
    $bucket = env('aws.bucket'); // 容器名称[调整填写自己的容器名称]
    $key = $filePath; // 要上传的文件
    $region = env('aws.region');//地区
    // $endpoint = 'https://obs-hazz.cucloud.cn';//
    $ak = env('aws.ak');// ak
    $sk = env('aws.sk');// sk
    $s3 = new \Aws\S3\S3Client([
        'version' => 'latest',
        's3ForcePathStyle' => true,
        'region' => $region,
        // 'endpoint' => $endpoint,
        'credentials' => [
            'key' => $ak,
            'secret' => $sk,
        ],
        // 'scheme' => 'http',
        // 'debug' => true,
    ]);

    $s3->putObject([
        'Bucket' => $bucket,
        'Key' => $savePath,
        'Body' => fopen($key, "r"),
    ]);

    // 生成缩略图
    if ($istrumb) {
        $fullthumbpath = createThumbImg($nopubpath, 100, 100);
        $fileName = pathinfo($nopubpath, PATHINFO_FILENAME);
        $thumbpath = str_replace($fileName, $fileName . '100x100', $nopubpath);
        $s3->putObject([
            'Bucket' => $bucket,
            'Key' => $thumbpath,
            'Body' => fopen($fullthumbpath, "r"),
        ]);

    }

}

function cacheRate()
{
    $key = "cashRateList";
    $redis = (new Redis(['select' => 2]))->getRedis();
    $rateList = $redis->get($key);
    if (!$rateList) {
        $req_url = 'https://api.exchangerate.host/latest?base=USD';
        $response_json = file_get_contents($req_url);
        if (false !== $response_json) {
            try {
                $response = json_decode($response_json, true);
                if ($response['success'] === true) {
                    $rateList = $response['rates'];
                    $redis->set($key, json_encode($rateList), 3600);
                } else {
                    throw new Exception("System error");
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    } else {
        $rateList = json_decode($rateList, true);
    }
    return $rateList;

}

function getCoinMarketCap($type, $change = "USD")
{
    $key = "virtualRate_{$type}_{$change}";
    $redis = (new Redis(['select' => 2]))->getRedis();
    $rate = $redis->get($key);
    if (!$rate) {
        $url = 'https://pro-api.coinmarketcap.com/v2/tools/price-conversion';
        $parameters = [
            'amount' => '1',
            "symbol" => $change,
            'convert' => $type
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: 9511b77d-1c7b-4875-b8a4-f8531580f328'
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL


        $curl = curl_init(); // Get cURL resource
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        $data = json_decode($response, true); // print json decoded response
        //echo $response;die();
        curl_close($curl); // Close request
        if ($data['status']['error_code'] == 0) {
            $rate = $data['data'][0]['quote'][$type]['price'];
            $redis->set($key, $rate, 300);
        } else {
            throw new Exception($data['status']['error_message']);
        }
    }
    return $rate;
}

/**
 * 生成缩略图
 * $imgSrc     图片源路径
 * $thumbWidth   缩略图宽度
 * $thumbHeight  缩略图高度
 * $thumbSrc    缩略图路径
 * $isCut     是否剪切图片
 */
function createThumbImg($imgSrc, $thumbWidth, $thumbHeight, $isCut = false)
{

    $imgSrc = str_replace('\/', '/', public_path() . $imgSrc);
    $fileName = pathinfo($imgSrc, PATHINFO_FILENAME);
    $thumbSrc = str_replace($fileName, $fileName . $thumbWidth . 'x' . $thumbHeight, $imgSrc);
    //1.获取图片的类型
    $type = substr(strrchr($imgSrc, "."), 1);
    //2.初始化图象
    if ($type == "jpg" || $type == "jpeg") {
        //创建一块画布，并从JPEG文件或URL地址载入一副图像
        $sourceImg = imagecreatefromjpeg($imgSrc);
    } elseif ($type == "gif") {
        //创建一块画布，并从GIF文件或URL地址载入一副图像
        $sourceImg = imagecreatefromgif($imgSrc);
    } elseif ($type == "png") {
        //创建一块画布，并从PNG文件或URL地址载入一副图像
        $sourceImg = imagecreatefrompng($imgSrc);
    } elseif ($type == "wbmp") {
        //创建一块画布，并从WBMP文件或URL地址载入一副图像
        $sourceImg = imagecreatefromwbmp($imgSrc);
    }
    //取得图像宽度
    $width = imagesx($sourceImg);
    //取得图像高度
    $height = imagesy($sourceImg);

    //3.生成图象
    //缩略图的图象比例
    $scale = intval(($thumbWidth) / ($thumbHeight));

    //源图片的图象比例
    $ratio = round(($width) / ($height));
    if (($isCut) == 1) {
        //高度优先
        if ($ratio >= $scale) {
            //创建真彩图像资源（imagecreatetruecolor()函数使用GDLibrary创建新的真彩色图像）

            $newimg = imagecreatetruecolor($thumbWidth, $thumbHeight);

            //图像处理
            imagecopyresampled($newimg, $sourceImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, (($height) * $scale), $height);
            //以JPEG格式将图像输出到浏览器或文件
            ImageJpeg($newimg, $thumbSrc);
        }
        //宽度优先
        if ($ratio < $scale) {
            $newimg = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagecopyresampled($newimg, $sourceImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, (($width) / $scale));
            ImageJpeg($newimg, $thumbSrc);
        }
    } else {
        if ($ratio >= $scale) {
            $newimg = imagecreatetruecolor($thumbWidth, intval(round(($thumbWidth) / $ratio)));
            imagecopyresampled($newimg, $sourceImg, 0, 0, 0, 0, $thumbWidth, intval(round(($thumbWidth) / $ratio)), $width, $height);
            ImageJpeg($newimg, $thumbSrc);
        }
        if ($ratio < $scale) {
            $newimg = imagecreatetruecolor(intval(round(($thumbHeight) * $ratio)), $thumbHeight);
            imagecopyresampled($newimg, $sourceImg, 0, 0, 0, 0, intval(round(($thumbHeight) * $ratio)), $thumbHeight, $width, $height);
            ImageJpeg($newimg, $thumbSrc);
        }
    }
    //销毁图像
    ImageDestroy($sourceImg);
    return $thumbSrc;
}

/**
 * 生成缩略图
 * @param 图片连接
 * @param 生成宽度
 * @param 生成高度
 * @param 是否裁剪
 */
function thumbnail(string $url, $width = 100, $height = 100, $crop = false)
{
    $file = str_replace('\/', '/', public_path() . $url);
    // $file = substr($url,1);
    $fileName = pathinfo($url, PATHINFO_FILENAME);
    $thumbName = str_replace($fileName, $fileName . $width . 'x' . $height, $file);
    // var_dump($thumbName);
    if (!is_file($thumbName)) {
        $image = \think\Image::open($file);

        $aa = $image->thumb(150, 150)->save($thumbName);
        var_dump($aa);
        die;
        // $image = Image::open($file);
        // var_dump(123155);
        // var_dump($image);
        // if ($crop) {
        //     $image->crop($width, $height,100,30)->save($thumbName);
        // } else {
        //     $image->thumb($width, $height)->save($thumbName);
        // }
    }

    // $thumbpath = str_replace($fileName, $fileName.$width.'x'.$height, $url);
    // upimage($file,$thumbpath);
    var_dump($thumbName);
    return $thumbName;
}

/**
 * 过滤掉（空格、全角空格、换行等）
 * @param 字符串
 */
function ctrim(string $str)
{
    $search = array(" ", "　", "\n", "\r", "\t");
    $replace = array("", "", "", "", "");
    return str_replace($search, $replace, $str);
}

/**
 * 字符串裁剪(针对编辑器编码问题)
 * @param 字符串
 * @param 起始位置
 * @param 结束位置
 */
function csubstr($string = "", $start = 0, $length = 255): string
{
    $string = str_replace("&nbsp;", '', $string);
    return mb_substr(trim(strip_tags(htmlspecialchars_decode($string, ENT_QUOTES))), $start, $length, 'UTF-8');
}

/**
 * 日期时间友好显示
 */
function friend_time(string $data)
{
    $time = strtotime($data);
    $fdate = '';
    $d = time() - intval($time);
    $sy = intval(date('Y'));
    $sm = intval(date('m'));
    $sd = intval(date('d'));
    $ld = $time - mktime(0, 0, 0, 0, 0, $sy); //得出年
    $md = $time - mktime(0, 0, 0, $sm, 0, $sy); //得出月
    $byd = $time - mktime(0, 0, 0, $sm, $sd - 2, $sy); //前天
    $yd = $time - mktime(0, 0, 0, $sm, $sd - 1, $sy); //昨天
    $dd = $time - mktime(0, 0, 0, $sm, $sd, $sy); //今天
    $td = $time - mktime(0, 0, 0, $sm, $sd + 1, $sy); //明天
    $atd = $time - mktime(0, 0, 0, $sm, $sd + 2, $sy); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y年m月d日', $time);
                break;
            case $d < $td:
                $fdate = '后天' . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = '明天' . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . '秒前';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '分钟前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '小时前';
                break;
            case $d < $yd:
                $fdate = '昨天' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前天' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m月d日 H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m月d日', $time);
                break;
            default:
                $fdate = date('Y年m月d日', $time);
                break;
        }
    }
    return $fdate;
}


/**
 * 二维数组根据某个字段排序
 * @param 要排序的数组
 * @param 要排序的键字段
 * @param 排序类型SORT_ASC/SORT_DESC
 */
function array_sort(array $array, string $keys, $sort = "desc"): array
{
    $order = $sort === 'asc' ? SORT_ASC : SORT_DESC;
    $keysValue = [];
    foreach ($array as $k => $v) {
        $keysValue[$k] = $v[$keys];
    }
    array_multisort($keysValue, $order, $array);
    return $array;
}

/**
 * 生成不重复的字符串
 * @param 需要的长度
 */
function rand_id(int $length): string
{
    $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
    $str = '';
    $arr_len = count($arr);
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $arr_len - 1);
        $str .= $arr[$rand];
    }
    return $str;
}

/**
 * 时间秒转换为 00:00:00 格式
 * @param 秒
 */
function secto_time(int $times): string
{
    $result = '00:00:00';
    if ($times > 0) {
        $hour = floor($times / 3600);
        if ($hour < 10) {
            $hour = "0" . $hour;
        }
        $minute = floor(($times - 3600 * $hour) / 60);
        if ($minute < 10) {
            $minute = "0" . $minute;
        }
        $second = floor((($times - 3600 * $hour) - 60 * $minute) % 60);
        if ($second < 10) {
            $second = "0" . $second;
        }
        $result = $hour . ':' . $minute . ':' . $second;
    }
    return $result;
}

/**
 * 获取admin应用url
 * @param 链接
 * @param 参数
 */
function admin_url($url = "", $vars = []): string
{
    $url = empty($url) || $url == 'index/index' ? '' : $url;
    if (count(explode('/', $url)) > 2) {
        $vars['path'] = $url;
        $url = 'plugins';
    }
    $param = "";
    $index = 0;
    foreach ($vars as $key => $val) {
        $str = $index === 0 ? '?' : '&';
        $param .= $str . $key . '=' . $val;
        $index++;
    }
    return request()->domain() . '/' . env('map_admin') . '/' . $url . $param;
}

/**
 * 获取index应用url
 * @param 链接
 * @param 参数
 */
function index_url($url = '', array $vars = [], $theme = true): string
{
    $url = empty($url) || $url == 'index' ? '' : '/' . $url . '.html';
    if ($theme && !empty(input('theme'))) {
        $vars['theme'] = theme();
    }
    $param = "";
    $index = 0;
    foreach ($vars as $key => $val) {
        $str = $index === 0 ? '?' : '&';
        $param .= $str . $key . '=' . $val;
        $index++;
    }
    return request()->domain() . $url . $param;
}

/**
 * CURL请求函数:支持POST及基本header头信息定义
 * @param 请求远程链接
 * @param 请求远程数据
 * @param 头信息数组
 * @param 来源url
 */
function curl(string $api_url, $post_data = [], $header = [], $referer_url = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    $header[] = "CLIENT-IP:" . request()->ip();
    $header[] = "X-FORWARDED-FOR:" . request()->ip();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Baiduspider/2.0; +" . request()->domain() . ")");
    curl_setopt($ch, CURLOPT_REFERER, request()->domain());
    if ($post_data && is_array($post_data)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['status' => 'error', 'message' => curl_error($ch)];
    } else {
        curl_close($ch);
        return $data;
    }
}

/**
 * CURL请求函数:支持POST及基本header头信息定义
 * @param 请求远程链接
 * @param 请求远程数据
 * @param 头信息数组
 * @param 来源url
 */
function curlNoIpSet(string $api_url, $post_data = [], $header = [], $referer_url = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Baiduspider/2.0; +" . request()->domain() . ")");
    curl_setopt($ch, CURLOPT_REFERER, request()->domain());
    if ($post_data && is_array($post_data)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['status' => 'error', 'message' => curl_error($ch)];
    } else {
        curl_close($ch);
        return $data;
    }
}

function curl_json(string $api_url, $post_data = [], $header = [], $referer_url = '')
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($post_data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

/**
 * 插件列表
 * @param 状态
 */
function plugin_list($status = 1): array
{
    $pluginList = [];
    $pluginPath = plugin_path();
    if (is_dir($pluginPath)) {
        $handle = opendir($pluginPath);
        if ($handle) {
            while (($path = readdir($handle)) !== false) {
                if ($path != '.' && $path != '..') {
                    $nowPluginPath = $pluginPath . $path;
                    $nowPluginInfo = is_file($nowPluginPath . '/info.php') ? include($nowPluginPath . '/info.php') : [];
                    if ($nowPluginInfo) {
                        if ($nowPluginInfo['status'] == $status || $status == '') {
                            $nowPluginInfo['route'] = is_file($nowPluginPath . '/route.php') ? include($nowPluginPath . '/route.php') : [];
                            array_push($pluginList, $nowPluginInfo);
                        }
                    }
                }
            }
        }
    }
    return array_sort($pluginList, 'sort');
}

/**
 * 插件位置
 */
function plugin_path(): string
{
    return public_path() . 'plugins/';
}

/**
 * 当前主题
 */
function theme(): string
{
    if (empty(input('theme'))) {
        return empty(env('app_theme')) ? config('app.theme') : env('app_theme');
    } else {
        return input('theme');
    }
}

/**
 * 主题路径
 */
function theme_path(): string
{
    return public_path() . 'themes/';
}

/**
 * 当前主题路径
 */
function theme_now_path(): string
{
    return theme_path() . theme() . '/';
}

/**
 * 当前主题视图
 */
function theme_now_view(): string
{
    $path = theme_now_path();
    // 手机端
    if (is_dir($path . 'wap/') && request()->isMobile() == 1) {
        $path = $path . 'wap/';
    }
    return $path;
}

/**
 * api发起POST请求
 * @param 请求api方法
 * @param 请求api数据
 */
function api_post(string $func, $data = []): array
{
    $data['token'] = env('app_token');
    $url = config('app.api') . '/api/onekey/' . $func;
    $output = curl($url, $data);
    if (is_array($output)) {
        return $output;
    }
    $result = json_decode($output, true);
    return is_array($result) ? $result : ['status' => 'error', 'message' => '连接错误'];
}

/* 生成证书 */
function exportOpenSSLFile()
{

    $config = array(

        "digest_alg" => "sha512",

        "private_key_bits" => 512,           //字节数  512 1024 2048  4096 等

        "private_key_type" => OPENSSL_KEYTYPE_RSA,   //加密类型

    );

    $res = openssl_pkey_new($config);

    if ($res == false) return false;

    openssl_pkey_export($res, $private_key);

    $public_key = openssl_pkey_get_details($res);

    $public_key = $public_key["key"];

    // file_put_contents("./cert/cert_public.key",$public_key);

    // file_put_contents("./cert/cert_private.pem",$private_key);

    // openssl_free_key($res);

    // 将证书以字符串的形式展现出来，方便把公钥下发给客户端
    $public_key = strtr($public_key, ['-----BEGIN PUBLIC KEY-----' => '', '-----END PUBLIC KEY-----' => '', "\n" => '']);
    $private_key = strtr($private_key, ['-----BEGIN PRIVATE KEY-----' => '', '-----END PRIVATE KEY-----' => '', "\n" => '']);

    return json_encode(['public' => $public_key, 'private' => $private_key]);
}

/*加密解密
**默认解密
*/

function authcode($string, $ssl_public, $ssl_private, $operation = 'D')
{
    // 以文件的方式加密和解密
    // $ssl_public = file_get_contents("./cert/cert_public.key");

    // $ssl_private = file_get_contents("./cert/cert_private.pem");

    // 以字符串的方式加密和解密
    $ssl_public = chunk_split($ssl_public, 64, "\n");

    $ssl_public = "-----BEGIN PUBLIC KEY-----\n" . $ssl_public . "-----END PUBLIC KEY-----\n";

    $ssl_private = chunk_split($ssl_private, 64, "\n");

    $ssl_private = "-----BEGIN PRIVATE KEY-----\n" . $ssl_private . "-----END PRIVATE KEY-----\n";

    $pi_key = openssl_pkey_get_private($ssl_private);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id

    $pu_key = openssl_pkey_get_public($ssl_public);//这个函数可用来判断公钥是否是可用的

    if (false == ($pi_key || $pu_key)) return false;//证书错误的情况

    $data = "";

    if ($operation == 'D') {

        openssl_private_decrypt(base64_decode($string), $data, $pi_key);//私钥解密

    } elseif ($operation == 'E') {

        openssl_public_encrypt($string, $data, $pu_key);//公钥加密

        $data = base64_encode($data);

    }

    return $data;

}

//随机生成安全码
function createsalt()
{
    // 生成字母和数字组成的6位字符串

    $str = range('A', 'Z');
    // 去除大写的O，以防止与0混淆 
    unset($str[array_search('O', $str)]);
    $arr = array_merge(range(0, 9), $str);
    shuffle($arr);
    $invitecode = '';
    $arr_len = count($arr);
    for ($i = 0; $i < 6; $i++) {
        $rand = mt_rand(0, $arr_len - 1);
        $invitecode .= $arr[$rand];
    }

    return $invitecode;
}

// 对字符串进行裁剪
function croppstring($str, $leng)
{
    $strlen = strlen($str);
    if ($strlen > $leng) {
        $str = mb_substr($str, 0, $leng) . "...";
    }
    return $str;
}

/**
 * @description：随机生成邮箱
 * @date: 2020/5/14 0014
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function randStr($len = 6, $format = 'default')
{
    switch ($format) {
        case 'ALL':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
            break;
        case 'CHAR':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
            break;
        case 'NUMBER':
            $chars = '0123456789';
            break;
        default :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
    }
    // mt_srand((double)microtime() * 1000000 * getmypid());
    $password = "";
    while (strlen($password) < $len)
        $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
    return $password;
}

/**
 * @description：根据IP获取国家
 * @date: 2020/5/14 0014
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getipcountry($ip)
{
//    return $address=["country"=>"未知","province"=>"未知","city"=>"未知"];
    $redis = isset($GLOBALS['SPREDIS']) ? $GLOBALS['SPREDIS'] : (new \app\common\lib\Redis())->getRedis();
    $address = $redis->get("ip_address_detail_{$ip}") ?: '';
    try {
        if (!$address) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://ip-api.com/json/{$ip}?lang=zh-CN",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36'
                ),
            ));

            $response = curl_exec($curl);
            if ($response) {
                curl_close($curl);
                $contents = json_decode($response, true);
                if ($contents['status'] == 'success') {
//      if($contents['country']=='香港' || $contents['country']=='澳门'){
//          $contents['country']='中国';
//      }
                    $address = ["country" => $contents['country'], "province" => $contents['regionName'], "city" => $contents['city']];
                } else {
                    $address = ["country" => "未知", "province" => "未知", "city" => "未知"];
                }
                $redis->set("ip_address_detail_{$ip}", json_encode($address), 10 * 24 * 3600);
            }

        } else {
            $address = json_decode($address, true);
        }
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }


//  $reader = new Reader(root_path().'GeoIp2_data/GeoLite2-Country.mmdb');
//  // HK=>香港，TW=>台湾，MO=>澳门
//  $record = $reader->country($ip);
//  $country = $record->country->isoCode;
//  if($country=='HK' || $country=='TW' || $country=='MO'){
//      $country='中';
//  }
    return $address;
}

/**
 * @description：生成二维码
 * @date: 2020/5/14 0014
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function create_qrcode($data, $userInfo)
{
    require root_path() . 'extend/Aws/aws-autoloader.php';
    $qrcodefile = public_path() . 'upload/qrcode/' . date('Y') . date('m') . date('d');
    if (!is_dir($qrcodefile)) {
        mkdir($qrcodefile);
    }
    $writer = new PngWriter();

    // Create QR code
    $qrCode = QrCode::create($data)
        ->setEncoding(new Encoding('UTF-8'))
        ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
        ->setSize(300)
        ->setMargin(10)
        ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->setForegroundColor(new Color(0, 0, 0))
        ->setBackgroundColor(new Color(255, 255, 255));

    // Create generic logo
    // $logo = Logo::create($userInfo['cover'])
    //     ->setResizeToWidth(50)
    //  ->setResizeToHeight(50);

    // Create generic label
    // $label = Label::create('Label')
    //     ->setTextColor(new Color(255, 0, 0));

    $result = $writer->write($qrCode);
    // header('Content-Type: '.$result->getMimeType());

    $result->saveToFile($qrcodefile . '/' . $userInfo['id'] . $userInfo['game_account'] . '.png');
    $filename = 'upload/qrcode/' . date('Y') . date('m') . date('d') . '/' . $userInfo['id'] . $userInfo['game_account'] . '.png';
    $bucket = env('aws.bucket'); // 容器名称[调整填写自己的容器名称]
    $key = root_path() . 'public/' . $filename; // 要上传的文件
    $region = env('aws.region');//地区
// $endpoint = 'https://obs-hazz.cucloud.cn';//
    $ak = env('aws.ak');// ak
    $sk = env('aws.sk');// sk
    $s3 = new \Aws\S3\S3Client([
        'version' => 'latest',
        's3ForcePathStyle' => true,
        'region' => $region,
        // 'endpoint' => $endpoint,
        'credentials' => [
            'key' => $ak,
            'secret' => $sk,
        ],
        // 'scheme' => 'http',
        // 'debug' => true,
    ]);

    $s3->putObject([
        'Bucket' => $bucket,
        'Key' => $filename,
        'Body' => fopen($key, "r"),
    ]);
    unlink($key);
    UserModel::where('id', $userInfo['id'])->update(['QR_code' => $filename]);
    return $filename;
}

/**
 * @description：资金流水
 * @date: 2020/5/14 0014
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function capital_flow($uid, $other_id, $type, $money_type, $amount, $balance, $content, $admin_content, $game_log_id = 0)
{
    if ($amount > 0) {
        $data = [
            'uid' => $uid,
            'other_id' => $other_id,
            'type' => $type,
            'money_type' => $money_type,
            'amount' => $amount,
            'balance' => $balance,
            'content' => $content,
            'admin_content' => $admin_content,
            'add_time' => time(),
            'add_ip' => request()->ip(),
            'game_log_id' => $game_log_id
        ];
        $cId = CapitalFlowmodel::insertGetId($data);
        if ($cId) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * @description：二维数组去重
 * @date: 2020/5/14 0014
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */

function remove_duplicate($array)
{
    $result = array();
    for ($i = 0; $i < count($array); $i++) {
        $source = $array[$i];
        if (array_search($source, $array) == $i && $source <> "") {
            $result[] = $source;
        }
    }
    return $result;
}

/**
 * @description：处理内容语言包
 * @date: 2020/5/14 0014
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getlang($content)
{
    preg_match_all('/(?:\{)(.*?)(?:\})/i', $content, $match);
    $lang = [];
    foreach ($match[1] as $k => $v) {
        $lang[$k] = lang($v) . " ";
    }
    $langcontent = str_replace($match[1], $lang, $content);
    $langcontent = str_replace(['{', '}'], '', $langcontent);
    return $langcontent;
}

function getBankList($currency, $name)
{
    $list = [
        "SurePay_MYR"=>[
            ["name" => "Maybank","code"=>"MAYBANK"],
            ["name" => "CIMB Group Holdings","code"=>"CIMB"],
            ["name" => "Public Bank Berhad","code"=>"PBE"],
            ["name" => "RHB Bank","code"=>"RHB"],
            ["name" => "Hong Leong Bank","code"=>"HLB"],
            ["name" => "Bank Simpanan Nasional","code"=>"BSN"],
            ["name" => "Affin Bank","code"=>"AFFIN"],
            ["name" => "AmBank","code"=>"AMBANK"],
            ["name" => "Bank Rakyat Malaysia","code"=>"BKRM"],
            ["name" => "Alliance Bank Malaysia Berhad","code"=>"ALLIANCE"],
            ["name" => "Bank Islam Berhad","code"=>"BIMB"],
            ["name" => "Bank Muamalat","code"=>"BMMB"],
            ["name" => "Bank of Agro","code"=>"AGRO"],
            ["name" => "Hong Kong And Shanghai Bank","code"=>"HSBC"],
            ["name" => "United Overseas Bank","code"=>"UOBMY"],
            ["name" => "Oversea-Chinese Banking Corporation","code"=>"OCBC"],
        ],
        "TopPay_IDR" => [
            [
                "bankshort" => "ACEH",
                "name" => "Bank Aceh Syariah",
                "code" => "116"
            ],
            [
                "bankshort" => "ACEH_UUS",
                "name" => "Bank Agris UUS",
                "code" => "1160"
            ],
            [
                "bankshort" => "ACEH_SYR",
                "name" => "BPD ISTIMEWA ACEH SYARIAH",
                "code" => "1161"
            ],
            [
                "bankshort" => "AGRIS",
                "name" => "Bank IBK Indonesia",
                "code" => "945"
            ],
            [
                "bankshort" => "AMAR",
                "name" => "BANK AMAR INDONESIA",
                "code" => "1162"
            ],
            [
                "bankshort" => "AGRONIAGA",
                "name" => "Bank Agroniaga",
                "code" => "494"
            ],
            [
                "bankshort" => "ANDARA",
                "name" => "Bank Andara",
                "code" => "466"
            ],
            [
                "bankshort" => "ANGLOMAS",
                "name" => "Anglomas International Bank",
                "code" => "531"
            ],
            [
                "bankshort" => "ANTAR_DAERAH",
                "name" => "BANK ANTAR DAERAH",
                "code" => "1163"
            ],
            [
                "bankshort" => "ANZ",
                "name" => "Bank ANZ Indonesia",
                "code" => "061"
            ],
            [
                "bankshort" => "ANZ_PANIN",
                "name" => "Bank ANZ PANIN",
                "code" => "0610"
            ],
            [
                "bankshort" => "ARTAJASA",
                "name" => "ARTAJASA PEMBAYARAN ELEKTRONIK",
                "code" => "987"
            ],
            [
                "bankshort" => "ARTA_NIAGA_KENCANA",
                "name" => "Bank Arta Niaga Kencana",
                "code" => "020"
            ],
            [
                "bankshort" => "ARTHA",
                "name" => "Bank Artha Graha Internasional",
                "code" => "037"
            ],
            [
                "bankshort" => "ARTOS",
                "name" => "Bank ARTOS/ Bank Jago",
                "code" => "542"
            ],
            [
                "bankshort" => "BALI",
                "name" => "BPD Bali",
                "code" => "129"
            ],
            [
                "bankshort" => "BISNIS_INTERNASIONAL",
                "name" => "Bank Bisnis Internasional",
                "code" => "459"
            ],
            [
                "bankshort" => "BANGKOK",
                "name" => "Bangkok Bank",
                "code" => "040"
            ],
            [
                "bankshort" => "BANTEN",
                "name" => "BPD Banten",
                "code" => "558"
            ],
            [
                "bankshort" => "BARCLAYS",
                "name" => "BANK BARCLAYS INDONESIA",
                "code" => "525"
            ],
            [
                "bankshort" => "BCA",
                "name" => "Bank Central Asia",
                "code" => "014"
            ],
            [
                "bankshort" => "BCA_SYR",
                "name" => "Bank Central Asia (BCA) Syariah",
                "code" => "536"
            ],
            [
                "bankshort" => "BENGKULU",
                "name" => "Bank Bengkulu",
                "code" => "133"
            ],
            [
                "bankshort" => "BJB",
                "name" => "Bank Jawa Barat(BJB)",
                "code" => "110"
            ],
            [
                "bankshort" => "BJB_SYR",
                "name" => "Bank BJB Syariah",
                "code" => "425"
            ],
            [
                "bankshort" => "BNI",
                "name" => "Bank Negara Indonesia(BNI)",
                "code" => "009"
            ],
            [
                "bankshort" => "BNI_SYR",
                "name" => "Bank BNI Syariah",
                "code" => "427"
            ],
            [
                "bankshort" => "BOC",
                "name" => "BANK OF CHINA LIMITED",
                "code" => "069"
            ],
            [
                "bankshort" => "BRI",
                "name" => "Bank Rakyat Indonesia(BRI)",
                "code" => "002"
            ],
            [
                "bankshort" => "BRI_SYR",
                "name" => "Bank BRI Syariah",
                "code" => "422"
            ],
            [
                "bankshort" => "BNP_PARIBAS",
                "name" => "Bank BNP Paribas",
                "code" => "1450"
            ],
            [
                "bankshort" => "BOA",
                "name" => "BANK OF AMERICA NA",
                "code" => "033"
            ],
            [
                "bankshort" => "BPRKS",
                "name" => "BPR KS",
                "code" => "688"
            ],
            [
                "bankshort" => "BSI",
                "name" => "Bank Syariah Indonesia(BSI)",
                "code" => "4510"
            ],
            [
                "bankshort" => "BTN",
                "name" => "Bank Tabungan Negara (BTN)",
                "code" => "200"
            ],
            [
                "bankshort" => "BTN_UUS",
                "name" => "Bank Tabungan Negara (BTN) UUS",
                "code" => "2000"
            ],
            [
                "bankshort" => "BTPN",
                "name" => "Bank BTPN",
                "code" => "213"
            ],
            [
                "bankshort" => "BTPN_SYARIAH",
                "name" => "BTPN Syariah",
                "code" => "5470"
            ],
            [
                "bankshort" => "BTPN_SYR",
                "name" => "Bank BTPN Syariah",
                "code" => "547"
            ],
            [
                "bankshort" => "BUKOPIN",
                "name" => "Wokee/Bukopin",
                "code" => "441"
            ],
            [
                "bankshort" => "BUKOPIN_SYR",
                "name" => "Bank Bukopin Syariah",
                "code" => "521"
            ],
            [
                "bankshort" => "BUMI_ARTA",
                "name" => "Bank Bumi Arta",
                "code" => "076"
            ],
            [
                "bankshort" => "BUMIPUTERA",
                "name" => "BANK BUMIPUTERA",
                "code" => "4850"
            ],
            [
                "bankshort" => "CAPITAL",
                "name" => "Bank Capital Indonesia",
                "code" => "054"
            ],
            [
                "bankshort" => "CENTRATAMA",
                "name" => "Centratama Nasional Bank",
                "code" => "5590"
            ],
            [
                "bankshort" => "CHINACONS",
                "name" => "BANK CHINA CONSTRUCTION",
                "code" => "9490"
            ],
            [
                "bankshort" => "CHINATRUST",
                "name" => "CTBC Indonesia",
                "code" => "949"
            ],
            [
                "bankshort" => "CNB",
                "name" => "Centratama Nasional Bank(CNB)",
                "code" => "559"
            ],
            [
                "bankshort" => "CIMB",
                "name" => "Bank CIMB Niaga",
                "code" => "022"
            ],
            [
                "bankshort" => "CIMB_UUS",
                "name" => "Bank CIMB Niaga UUS",
                "code" => "0220"
            ],
            [
                "bankshort" => "CIMB_REKENING_PONSEL",
                "name" => "Bank CIMB Niaga REKENING PONSEL",
                "code" => "0221"
            ],
            [
                "bankshort" => "CITIBANK",
                "name" => "Citibank",
                "code" => "031"
            ],
            [
                "bankshort" => "COMMONWEALTH",
                "name" => "Bank Commonwealth",
                "code" => "950"
            ],
            [
                "bankshort" => "BPD_DIY",
                "name" => "BPD DIY",
                "code" => "112"
            ],
            [
                "bankshort" => "BPD_DIY_SYR",
                "name" => "BANK PEMBANGUNAN DAERAH DIY UNIT USAHA SYARIAH",
                "code" => "1121"
            ],
            [
                "bankshort" => "DANAMON",
                "name" => "Bank Danamon",
                "code" => "011"
            ],
            [
                "bankshort" => "DANAMON_UUS",
                "name" => "Bank Danamon UUS",
                "code" => "0110"
            ],
            [
                "bankshort" => "DBS",
                "name" => "Bank DBS Indonesia",
                "code" => "046"
            ],
            [
                "bankshort" => "DEUTSCHE",
                "name" => "Deutsche Bank",
                "code" => "067"
            ],
            [
                "bankshort" => "DINAR_INDONESIA",
                "name" => "Bank Dinar Indonesia",
                "code" => "526"
            ],
            [
                "bankshort" => "DIPO",
                "name" => "BANK DIPO INTERNATIONAL",
                "code" => "5230"
            ],
            [
                "bankshort" => "DKI",
                "name" => "Bank DKI",
                "code" => "111"
            ],
            [
                "bankshort" => "DKI_UUS",
                "name" => "Bank DKI UUS",
                "code" => "778"
            ],
            [
                "bankshort" => "EKA",
                "name" => "Bank EKA",
                "code" => "699"
            ],
            [
                "bankshort" => "EKONOMI_RAHARJA",
                "name" => "BANK EKONOMI RAHARJA",
                "code" => "087"
            ],
            [
                "bankshort" => "FAMA",
                "name" => "Bank Fama International",
                "code" => "562"
            ],
            [
                "bankshort" => "GANESHA",
                "name" => "Bank Ganesha",
                "code" => "161"
            ],
            [
                "bankshort" => "HANA",
                "name" => "LINE Bank/KEB Hana",
                "code" => "484"
            ],
            [
                "bankshort" => "HARDA_INTERNASIONAL",
                "name" => "Allo Bank/Bank Harda Internasional",
                "code" => "567"
            ],
            [
                "bankshort" => "HIMPUNAN_SAUDARA",
                "name" => "Bank Himpunan Saudara 1906",
                "code" => "2120"
            ],
            [
                "bankshort" => "HSBC",
                "name" => "HSBC",
                "code" => "041"
            ],
            [
                "bankshort" => "IBK",
                "name" => "IBK",
                "code" => "9450"
            ],
            [
                "bankshort" => "ICBC",
                "name" => "Bank ICBC Indonesia",
                "code" => "164"
            ],
            [
                "bankshort" => "INA_PERDANA",
                "name" => "Bank Ina Perdana",
                "code" => "513"
            ],
            [
                "bankshort" => "INDEX_SELINDO",
                "name" => "Bank Index Selindo",
                "code" => "555"
            ],
            [
                "bankshort" => "INDIA",
                "name" => "Bank of India Indonesia",
                "code" => "146"
            ],
            [
                "bankshort" => "JAGO",
                "name" => "BANK JAGO TBK",
                "code" => "5421"
            ],
            [
                "bankshort" => "JAMBI",
                "name" => "Bank Jambi",
                "code" => "115"
            ],
            [
                "bankshort" => "JASA_JAKARTA",
                "name" => "Bank Jasa Jakarta",
                "code" => "472"
            ],
            [
                "bankshort" => "JAWA_TENGAH",
                "name" => "Bank Jateng",
                "code" => "113"
            ],
            [
                "bankshort" => "JAWA_TENGAH_UUS",
                "name" => "BPD JAWA TENGAH UNIT USAHA SYARIAH",
                "code" => "1130"
            ],
            [
                "bankshort" => "JATIM",
                "name" => "Bank Jatim",
                "code" => "114"
            ],
            [
                "bankshort" => "JAWA_TIMUR",
                "name" => "BPD Jawa Timur",
                "code" => "1140"
            ],
            [
                "bankshort" => "JATIM_UUS",
                "name" => "Bank Jatim UUS",
                "code" => "1141"
            ],
            [
                "bankshort" => "JPMORGAN",
                "name" => "JPMORGAN CHASE BANK",
                "code" => "032"
            ],
            [
                "bankshort" => "JTRUST",
                "name" => "Bank JTrust Indonesia",
                "code" => "095"
            ],
            [
                "bankshort" => "KALIMANTAN_BARAT",
                "name" => "BPD Kalimantan Barat/Kalbar",
                "code" => "123"
            ],
            [
                "bankshort" => "KALIMANTAN_BARAT_UUS",
                "name" => "BPD Kalimantan Barat UUS",
                "code" => "1230"
            ],
            [
                "bankshort" => "KALIMANTAN_SELATAN",
                "name" => "BPD Kalimantan Selatan/Kalsel",
                "code" => "122"
            ],
            [
                "bankshort" => "KALIMANTAN_SELATAN_UUS",
                "name" => "BPD Kalimantan Selatan UUS",
                "code" => "1220"
            ],
            [
                "bankshort" => "KALIMANTAN_TENGAH",
                "name" => "Bank_Kalteng",
                "code" => "125"
            ],
            [
                "bankshort" => "KALIMANTAN_TIMUR",
                "name" => "BPD Kalimantan Timur",
                "code" => "124"
            ],
            [
                "bankshort" => "KALIMANTAN_TIMUR_UUS",
                "name" => "BPD Kalimantan Timur UUS",
                "code" => "1240"
            ],
            [
                "bankshort" => "KESEJAHTERAAN_EKONOMI",
                "name" => "Seabank/Bank Kesejahteraan Ekonomi(BKE)",
                "code" => "535"
            ],
            [
                "bankshort" => "LAMPUNG",
                "name" => "BPD Lampung",
                "code" => "121"
            ],
            [
                "bankshort" => "MALUKU",
                "name" => "Bank Maluku",
                "code" => "131"
            ],
            [
                "bankshort" => "MANDIRI",
                "name" => "Bank Mandiri",
                "code" => "008"
            ],
            [
                "bankshort" => "MANDIRI_SYR",
                "name" => "Bank Syariah Mandiri",
                "code" => "451"
            ],
            [
                "bankshort" => "MANDIRI_TASPEN",
                "name" => "Bank Mandiri Taspen Pos",
                "code" => "5640"
            ],
            [
                "bankshort" => "MANTAP",
                "name" => "Bank MANTAP",
                "code" => "564"
            ],
            [
                "bankshort" => "MULTI_ARTA_SENTOSA",
                "name" => "Bank Multi Arta Sentosa(MAS)",
                "code" => "548"
            ],
            [
                "bankshort" => "MASPION",
                "name" => "Bank Maspion Indonesia",
                "code" => "157"
            ],
            [
                "bankshort" => "MAYAPADA",
                "name" => "Bank Mayapada",
                "code" => "097"
            ],
            [
                "bankshort" => "MAYBANK",
                "name" => "Bank Maybank",
                "code" => "016"
            ],
            [
                "bankshort" => "MAYBANK_SYR",
                "name" => "Bank Maybank Syariah Indonesia",
                "code" => "947"
            ],
            [
                "bankshort" => "MAYBANK_UUS",
                "name" => "Bank Maybank Syariah Indonesia UUS",
                "code" => "0160"
            ],
            [
                "bankshort" => "MAYORA",
                "name" => "Bank Mayora Indonesia",
                "code" => "553"
            ],
            [
                "bankshort" => "MEGA",
                "name" => "Bank Mega",
                "code" => "426"
            ],
            [
                "bankshort" => "MEGA_SYR",
                "name" => "Bank Mega Syariah",
                "code" => "506"
            ],
            [
                "bankshort" => "MESTIKA_DHARMA",
                "name" => "Bank Mestika Dharma",
                "code" => "151"
            ],
            [
                "bankshort" => "METRO_EXPRESS",
                "name" => "BANK METRO EXPRESS",
                "code" => "1520"
            ],
            [
                "bankshort" => "MNC_INTERNASIONAL",
                "name" => "Motion/Bank MNC Internasional",
                "code" => "485"
            ],
            [
                "bankshort" => "MUAMALAT",
                "name" => "Bank Muamalat Indonesia",
                "code" => "147"
            ],
            [
                "bankshort" => "MITRA_NIAGA",
                "name" => "Bank Mitra Niaga",
                "code" => "491"
            ],
            [
                "bankshort" => "MIZUHO",
                "name" => "Bank Mizuho Indonesia",
                "code" => "048"
            ],
            [
                "bankshort" => "MUTIARA",
                "name" => "Bank MUTIARA",
                "code" => "10010"
            ],
            [
                "bankshort" => "MULTICOR",
                "name" => "Bank MULTICOR",
                "code" => "10006"
            ],
            [
                "bankshort" => "NATIONALNOBU",
                "name" => "Bank National Nobu",
                "code" => "503"
            ],
            [
                "bankshort" => "NIAGA_SYR",
                "name" => "BANK NIAGA TBK. SYARIAH",
                "code" => "583"
            ],
            [
                "bankshort" => "NUSA_TENGGARA_BARAT",
                "name" => "BPD Nusa Tenggara Barat(NTB)",
                "code" => "128"
            ],
            [
                "bankshort" => "NUSA_TENGGARA_BARAT_UUS",
                "name" => "BPD Nusa Tenggara Barat (NTB) UUS",
                "code" => "1280"
            ],
            [
                "bankshort" => "NUSA_TENGGARA_TIMUR",
                "name" => "BPD Nusa Tenggara Timur(NTT)",
                "code" => "130"
            ],
            [
                "bankshort" => "NUSANTARA_PARAHYANGAN",
                "name" => "Bank Nusantara Parahyangan",
                "code" => "145"
            ],
            [
                "bankshort" => "OCBC",
                "name" => "Bank OCBC NISP",
                "code" => "028"
            ],
            [
                "bankshort" => "OCBC_UUS",
                "name" => "Bank OCBC NISP UUS",
                "code" => "0280"
            ],
            [
                "bankshort" => "PANIN",
                "name" => "Bank Panin",
                "code" => "019"
            ],
            [
                "bankshort" => "PANIN_SYR",
                "name" => "Panin Dubai Syariah",
                "code" => "517"
            ],
            [
                "bankshort" => "PAPUA",
                "name" => "Bank Papua",
                "code" => "132"
            ],
            [
                "bankshort" => "PERMATA",
                "name" => "Bank Permata",
                "code" => "013"
            ],
            [
                "bankshort" => "PERMATA_UUS",
                "name" => "Bank Permata UUS",
                "code" => "0130"
            ],
            [
                "bankshort" => "PRIMA_MASTER",
                "name" => "Bank Prima Master",
                "code" => "520"
            ],
            [
                "bankshort" => "PUNDI",
                "name" => "BANK PUNDI INDONESIA",
                "code" => "584"
            ],
            [
                "bankshort" => "QNB_KESAWAN",
                "name" => "QNB KESAWAN",
                "code" => "167"
            ],
            [
                "bankshort" => "QNB_INDONESIA",
                "name" => "QNB Indonesia",
                "code" => "1670"
            ],
            [
                "bankshort" => "OKE",
                "name" => "Bank Oke Indonesia",
                "code" => "5260"
            ],
            [
                "bankshort" => "RABOBANK",
                "name" => "Rabobank International Indonesia",
                "code" => "089"
            ],
            [
                "bankshort" => "RESONA",
                "name" => "Bank Resona Perdania",
                "code" => "047"
            ],
            [
                "bankshort" => "RIAU_DAN_KEPRI",
                "name" => "BPD Riau Dan Kepri",
                "code" => "119"
            ],
            [
                "bankshort" => "RIAU_DAN_KEPRI_UUS",
                "name" => "BPD Riau Dan Kepri UUS",
                "code" => "1190"
            ],
            [
                "bankshort" => "ROYAL",
                "name" => "Blu/BCA Digital",
                "code" => "5010"
            ],
            [
                "bankshort" => "SAHABAT_PURBA_DANARTA",
                "name" => "BANK PURBA DANARTA",
                "code" => "5471"
            ],
            [
                "bankshort" => "SAHABAT_SAMPOERNA",
                "name" => "Bank Sahabat Sampoerna",
                "code" => "523"
            ],
            [
                "bankshort" => "SBI_INDONESIA",
                "name" => "Bank SBI Indonesia",
                "code" => "498"
            ],
            [
                "bankshort" => "SHINHAN",
                "name" => "Bank Shinhan Indonesia",
                "code" => "152"
            ],
            [
                "bankshort" => "SINARMAS",
                "name" => "Bank Sinarmas",
                "code" => "153"
            ],
            [
                "bankshort" => "SINARMAS_UUS",
                "name" => "Bank Sinarmas UUS",
                "code" => "1530"
            ],
            [
                "bankshort" => "STANDARD_CHARTERED",
                "name" => "Standard Chartered Bank",
                "code" => "050"
            ],
            [
                "bankshort" => "SULAWESI",
                "name" => "Bank Sulteng",
                "code" => "134"
            ],
            [
                "bankshort" => "SULAWESI_TENGGARA",
                "name" => "Bank Sultra",
                "code" => "135"
            ],
            [
                "bankshort" => "SULSELBAR",
                "name" => "Bank Sulselbar",
                "code" => "126"
            ],
            [
                "bankshort" => "SULSELBAR_UUS",
                "name" => "Bank Sulselbar UUS",
                "code" => "1260"
            ],
            [
                "bankshort" => "SULUT",
                "name" => "BPD Sulawesi Utara(SulutGo)",
                "code" => "127"
            ],
            [
                "bankshort" => "SUMATERA_BARAT",
                "name" => "BPD Sumatera Barat",
                "code" => "118"
            ],
            [
                "bankshort" => "SUMATERA_BARAT_UUS",
                "name" => "BPD Sumatera Barat UUS",
                "code" => "1180"
            ],
            [
                "bankshort" => "NAGARI",
                "name" => "BANK NAGARI",
                "code" => "1181"
            ],
            [
                "bankshort" => "SUMSEL_BABEL",
                "name" => "BPD Sumsel Babel",
                "code" => "120"
            ],
            [
                "bankshort" => "SUMSEL_DAN_BABEL",
                "name" => "Bank Sumsel Babel",
                "code" => "1200"
            ],
            [
                "bankshort" => "SUMSEL_DAN_BABEL_UUS",
                "name" => "Bank Sumsel Babel UUS",
                "code" => "1201"
            ],
            [
                "bankshort" => "SUMUT",
                "name" => "Bank Sumut",
                "code" => "117"
            ],
            [
                "bankshort" => "SUMUT_UUS",
                "name" => "Bank Sumut UUS",
                "code" => "1170"
            ],
            [
                "bankshort" => "MITSUI",
                "name" => "Bank Sumitomo Mitsui Indonesia",
                "code" => "045"
            ],
            [
                "bankshort" => "TOKYO",
                "name" => "Bank of Tokyo",
                "code" => "042"
            ],
            [
                "bankshort" => "UOB",
                "name" => "TMRW/Bank UOB Indonesia",
                "code" => "023"
            ],
            [
                "bankshort" => "VICTORIA_INTERNASIONAL",
                "name" => "Bank Victoria International",
                "code" => "566"
            ],
            [
                "bankshort" => "VICTORIA_SYR",
                "name" => "Bank Victoria Syariah",
                "code" => "405"
            ],
            [
                "bankshort" => "WOORI",
                "name" => "Bank Woori Saudara",
                "code" => "212"
            ],
            [
                "bankshort" => "YUDHA_BHAKTI",
                "name" => "Neo Commerce/Bank Yudha Bhakti",
                "code" => "490"
            ],
            [
                "bankshort" => "DAERAH_ISTIMEWA_UUS",
                "name" => "BPD_Daerah_Istimewa_Yogyakarta_(DIY)",
                "code" => "1120"
            ],
            [
                "bankshort" => "CCB",
                "name" => "CCB Indonesia",
                "code" => "088"
            ],
            [
                "bankshort" => "RBS",
                "name" => "Royal Bank of Scotland (RBS)",
                "code" => "501"
            ],
            [
                "bankshort" => "OVO",
                "name" => "OVO",
                "code" => "10001"
            ],
            [
                "bankshort" => "DANA",
                "name" => "DANA",
                "code" => "10002"
            ],
            [
                "bankshort" => "GOPAY",
                "name" => "GOPAY",
                "code" => "10003"
            ],
            [
                "bankshort" => "SHOPEEPAY",
                "name" => "SHOPEEPAY",
                "code" => "10008"
            ],
            [
                "bankshort" => "LINKAJA",
                "name" => "LINKAJA",
                "code" => "10009"
            ]
        ],
        "WowPay_MYR" => [
            [
                "code" => "AAAA",
                "name" => "Bank of america"
            ],
            [
                "code" => "AFFIN",
                "name" => "Affin Bank"
            ],
            [
                "code" => "AGRO",
                "name" => "AGRO"
            ],
            [
                "code" => "ALLIANCE",
                "name" => "Alliance Bank Malaysia Berhad"
            ],
            [
                "code" => "AM",
                "name" => "AmBank"
            ],
            [
                "code" => "BAKO",
                "name" => "Bangkok Bank Malaysia"
            ],
            [
                "code" => "BKRM",
                "name" => "Bank Rakyat"
            ],
            [
                "code" => "BMMB",
                "name" => "Bank Muamalate"
            ],
            [
                "code" => "BNPB",
                "name" => "BNP PARIBAS MALAYSIA"
            ],
            [
                "code" => "BSN",
                "name" => "BSN"
            ],
            [
                "code" => "CCCC",
                "name" => "Bank of china"
            ],
            [
                "code" => "CIMB",
                "name" => "CIMB Bank"
            ],
            [
                "code" => "CITI",
                "name" => "Citibank Malaysia"
            ],
            [
                "code" => "DEUT",
                "name" => "DEUTSCHE BANK"
            ],
            [
                "code" => "EON",
                "name" => "EON Bank"
            ],
            [
                "code" => "HONGLEONG",
                "name" => "Hong Leong Bank"
            ],
            [
                "code" => "HSBC",
                "name" => "HSBC"
            ],
            [
                "code" => "ICBC",
                "name" => "INDUSTRIAL & COMMERCIAL BANK OF CHINA"
            ],
            [
                "code" => "ISLAM",
                "name" => "Bank Islam Malaysia"
            ],
            [
                "code" => "JPMB",
                "name" => "J.P. MORGAN CHASE BANK"
            ],
            [
                "code" => "KFHB",
                "name" => "KUWAIT FINANCE HOUSE"
            ],
            [
                "code" => "MAY",
                "name" => "Maybank"
            ],
            [
                "code" => "MBSB",
                "name" => "MBSB Bank Berhad"
            ],
            [
                "code" => "MCCB",
                "name" => "CHINA CONST BK (M) BHD"
            ],
            [
                "code" => "MIZU",
                "name" => "MIZUHO BANK"
            ],
            [
                "code" => "MUFG",
                "name" => "MUFG BANK"
            ],
            [
                "code" => "OCBC",
                "name" => "OCBC"
            ],
            [
                "code" => "PUBLIC",
                "name" => "Public Bank Berhad"
            ],
            [
                "code" => "RHB",
                "name" => "RHB Bank"
            ],
            [
                "code" => "SCBM",
                "name" => "Standard Chartered Bank Malaysia"
            ],
            [
                "code" => "SINA",
                "name" => "BANK SIMPANAN NASIONAL"
            ],
            [
                "code" => "SUMB",
                "name" => "SUMITOMO MITSUI BANKING"
            ],
            [
                "code" => "UOB",
                "name" => "UOB"
            ]
        ],
        "NicePay_PHP" => [
            ["name" => "The Bank of the Philippine Islands", "code" => "BPI"],
            ["name" => "UnionBank of the Philippines", "code" => "UNIONBANK"],
            ["name" => "BDO Bank", "code" => "BDO"],
            ["name" => " Asia United Bank", "code" => "AUB"],
            ["name" => "EastWestBank", "code" => "EAST_WEST"],
            ["name" => "Land Bank Of The Philippines", "code" => "LAND_BANK"],
            ["name" => "Malayan Banking Berhad", "code" => "MAYBANK"],
            ["name" => "Metrobank", "code" => "METRO_BANK"],
            ["name" => "Philippine National Bank", "code" => "PNB"],
            ["name" => "Philippine Bank of Communications", "code" => "PBC"],
            ["name" => "Philippine Savings Bank", "code" => "PSB"],
            ["name" => "UnionBank of the Philippines", "code" => "PB"],
            ["name" => "Philippine Veterans Bank", "code" => "PVB"],
            ["name" => "Philtrust Bank", "code" => "PTC"],
            ["name" => "Philippine Business Bank", "code" => "PBB"],
            ["name" => "Security Bank", "code" => "SECURITY_BANK"],
            ["name" => "United Coconut Planters Bank", "code" => "UCPB"],
            ["name" => "Rizal Commercial Banking Corp", "code" => "RCBC"],
            ["name" => "Rural Bank of Bayombong", "code" => "RB"],
            ["name" => "CTBC BANK", "code" => "CTBC"],
            ["name" => "China Bank Savings", "code" => "CBS"],
            ["name" => "China Banking Corp", "code" => "CBC"],
            ["name" => "UnionBank of the Philippines", "code" => "DBI"],
            ["name" => "Bank of Commerce", "code" => "BOC"],
            ["name" => "UnionBank of the Philippines", "code" => "DCPAY"],
            ["name" => "UnionBank of the Philippines", "code" => "CAMALIG_BANK"],
            ["name" => "UnionBank of the Philippines", "code" => "STARPAY"],
            ["name" => "Malayan Banking Berhad", "code" => "MALAYAN_BANK"],
            ["name" => "Emigrant Savings Bank", "code" => "ESB"],
            ["name" => "UnionBank of the Philippines", "code" => "SUN_BANK"],
            ["name" => "Sterling Bank", "code" => "STERLING_BANK"],
            ["name" => "UnionBank of the Philippines", "code" => "EASTWEST_RURAL"],
            ["name" => "UnionBank of the Philippines", "code" => "OMNIPAY"],
            ["name" => "Chinabank", "code" => "CHINABANK"],
            ["name" => "UnionBank of the Philippines", "code" => "ALL_BANK"],
            ["name" => "ING Bank", "code" => "ING_BANK"],
            ["name" => "UnionBank of the Philippines", "code" => "CEBUANA_BANK"],
            ["name" => "SeaBank", "code" => "SEA_BANK"],
        ],
        "HtPay_PHP" => [
            [
            "code" => "GCASH",
            "name" => "GCASH"
            ],
            [
                "code" => "AUB",
                "name" => "Asia United Bank"
            ],
            [
                "code" => "UnionBankEON",
                "name" => "UnionBank EON"
            ],
            [
                "code" => "Starpay",
                "name" => "Starpay"
            ],
            [
                "code" => "EB",
                "name" => "Eastwest Bank"
            ],
            [
                "code" => "ESB",
                "name" => "Equicom Savings Bank"
            ],
            [
                "code" => "MB",
                "name" => "Malayan Bank"
            ],
            [
                "code" => "ERB",
                "name" => "EastWest Rural Bank"
            ],
            [
                "code" => "PB",
                "name" => "Producers Bank"
            ],
            [
                "code" => "PBC",
                "name" => "Philippine Bank of Communications"
            ],
            [
                "code" => "PBB",
                "name" => "Philippine Business Bank"
            ],
            [
                "code" => "PNB",
                "name" => "Philippine National Bank"
            ],
            [
                "code" => "PSB",
                "name" => "Philippine Savings Bank"
            ],
            [
                "code" => "PTC",
                "name" => "Philippine Trust Company"
            ],
            [
                "code" => "PVB",
                "name" => "Philippine Veterans Bank"
            ],
            [
                "code" => "RBG",
                "name" => "Rural Bank of Guinobatan, Inc."
            ],
            [
                "code" => "RCBC",
                "name" => "Rizal Commercial Banking Corporation"
            ],
            [
                "code" => "RB",
                "name" => "Robinsons Bank"
            ],
            [
                "code" => "SBC",
                "name" => "Security Bank Corporation"
            ],
            [
                "code" => "SBA",
                "name" => "Sterling Bank Of Asia"
            ],
            [
                "code" => "SSB",
                "name" => "Sun Savings Bank"
            ],
            [
                "code" => "UCPBSAVINGSBANK",
                "name" => "UCPB SAVINGS BANK"
            ],
            [
                "code" => "QCDBI",
                "name" => "Queen City Development Bank, Inc."
            ],
            [
                "code" => "UCPB",
                "name" => "United Coconut Planters Bank"
            ],
            [
                "code" => "WDBI",
                "name" => "Wealth Development Bank, Inc."
            ],
            [
                "code" => "YSBI",
                "name" => "Yuanta Savings Bank, Inc."
            ],
            [
                "code" => "GrabPay",
                "name" => "GrabPay Philippines"
            ],
            [
                "code" => "BDOUI",
                "name" => "Banco De Oro Unibank, Inc."
            ],
            [
                "code" => "BMI",
                "name" => "Bangko Mabuhay (A Rural Bank), Inc."
            ],
            [
                "code" => "BOC",
                "name" => "Bank Of Commerce"
            ],
            [
                "code" => "CTBC",
                "name" => "CTBC Bank (Philippines), Inc."
            ],
            [
                "code" => "Chinabank",
                "name" => "Chinabank"
            ],
            [
                "code" => "CBS",
                "name" => "Chinabank Savings"
            ],
            [
                "code" => "CBC",
                "name" => "Chinatrust Banking Corp"
            ],
            [
                "code" => "ALLBANK",
                "name" => "ALLBANK (A Thrift Bank), Inc."
            ],
            [
                "code" => "BNBI",
                "name" => "BDO Network Bank, Inc."
            ],
            [
                "code" => "BRBI",
                "name" => "Binangonan Rural Bank Inc"
            ],
            [
                "code" => "Camalig",
                "name" => "Camalig Bank"
            ],
            [
                "code" => "DBI",
                "name" => "Dungganun Bank, Inc."
            ],
            [
                "code" => "GlobeGcash",
                "name" => "Globe Gcash"
            ],
            [
                "code" => "CLRBI",
                "name" => "Cebuana Lhuillier Rural Bank, Inc."
            ],
            [
                "code" => "ISLABANK",
                "name" => "ISLA Bank (A Thrift Bank), Inc."
            ],
            [
                "code" => "LOTP",
                "name" => "Landbank of the Philippines"
            ],
            [
                "code" => "MPI",
                "name" => "Maybank Philippines, Inc."
            ],
            [
                "code" => "MBATC",
                "name" => "Metropolitan Bank and Trust Co"
            ],
            [
                "code" => "Omnipay",
                "name" => "Omnipay"
            ],
            [
                "code" => "PRBI",
                "name" => "Partner Rural Bank (Cotabato), Inc."
            ],
            [
                "code" => "PPI",
                "name" => "Paymaya Philippines, Inc."
            ],
            [
                "code" => "AlliedBankingCorp",
                "name" => "Allied Banking Corp"
            ],
            [
                "code" => "ING",
                "name" => "ING Bank N.V."
            ],
            [
                "code" => "BDBIASB",
                "name" => "BPI Direct Banko, Inc., A Savings Bank"
            ],
            [
                "code" => "CSB",
                "name" => "Citystate Savings Bank Inc."
            ],
            [
                "code" => "BPI",
                "name" => "Bank Of The Philippine Islands"
            ]]
    ];
    return isset($list[$name . "_" . $currency]) ? $list[$name . "_" . $currency] : [];
}