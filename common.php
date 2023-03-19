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
declare (strict_types = 1);

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
    $thumbName = str_replace($fileName, $fileName.$width.'x'.$height, $file);
	if (! is_file($thumbName)) {
		$image = Image::open($file);
        if ($crop) {
            $image->crop($width, $height,100,30)->save($thumbName);
        } else {
            $image->thumb($width, $height)->save($thumbName);
        }
    }
    return $thumbName;
}

/**
 * 过滤掉（空格、全角空格、换行等）
 * @param 字符串
 */
function ctrim(string $str)
{
    $search = array(" ","　","\n","\r","\t");
    $replace = array("","","","","");
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
    $string = str_replace("&nbsp;",'',$string);
    return mb_substr(trim(strip_tags(htmlspecialchars_decode($string,ENT_QUOTES))), $start, $length, 'UTF-8');
}

/**
* 日期时间友好显示
*/
function friend_time(string $data){
    $time  = strtotime($data);
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
    $order     = $sort === 'asc' ? SORT_ASC : SORT_DESC;
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
    for ($i = 0; $i < $length; $i++){
        $rand = mt_rand(0, $arr_len-1);
        $str.=$arr[$rand];
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
    if ($times>0) {  
            $hour = floor($times/3600); 
            if($hour<10){
                $hour = "0".$hour;
            } 
            $minute = floor(($times-3600 * $hour)/60); 
            if($minute<10){
                $minute = "0".$minute;
            } 
            $second = floor((($times-3600 * $hour) - 60 * $minute) % 60); 
             if($second<10){
                $second = "0".$second;
            } 
            $result = $hour.':'.$minute.':'.$second;  
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
    if ($theme && ! empty(input('theme'))) {
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
    curl_setopt( $ch, CURLOPT_URL, $api_url);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt( $ch, CURLOPT_TIMEOUT, 60);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt( $ch, CURLOPT_AUTOREFERER, 1);
    $header[] = "CLIENT-IP:".request()->ip();
    $header[] = "X-FORWARDED-FOR:".request()->ip();
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt( $ch, CURLOPT_ENCODING, "");
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Baiduspider/2.0; +" . request()->domain() . ")" );
    curl_setopt( $ch, CURLOPT_REFERER, request()->domain());
    if($post_data && is_array($post_data)) {
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec( $ch );
    if (curl_errno($ch)) {
        return ['status' => 'error', 'message' => curl_error($ch)];
    } else {
        curl_close($ch);    
        return $data;
    }
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
                            $nowPluginInfo['route'] = is_file($nowPluginPath.'/route.php') ? include($nowPluginPath.'/route.php') : [];
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
    $url    = config('app.api').'/api/onekey/' . $func;
    $output = curl($url, $data);
    if (is_array($output)) {
        return $output;
    }
    $result = json_decode($output, true);
    return is_array($result) ? $result : ['status' => 'error', 'message' => '连接错误'];
}

/* 生成证书 */
function exportOpenSSLFile(){

	$config = array(

	"digest_alg"    => "sha512",

	"private_key_bits" => 512,           //字节数  512 1024 2048  4096 等

	"private_key_type" => OPENSSL_KEYTYPE_RSA,   //加密类型

	);

	$res = openssl_pkey_new($config);

	if($res == false) return false;

	openssl_pkey_export($res, $private_key);

	$public_key = openssl_pkey_get_details($res);

	$public_key = $public_key["key"];

	// file_put_contents("./cert/cert_public.key",$public_key);

	// file_put_contents("./cert/cert_private.pem",$private_key);
	
	// openssl_free_key($res);
	
	// 将证书以字符串的形式展现出来，方便把公钥下发给客户端
	$public_key=strtr($public_key,['-----BEGIN PUBLIC KEY-----'=>'','-----END PUBLIC KEY-----'=>'',"\n"=>'']);
	$private_key=strtr($private_key,['-----BEGIN PRIVATE KEY-----'=>'','-----END PRIVATE KEY-----'=>'',"\n"=>'']);
	
	return json_encode(['public'=>$public_key,'private'=>$private_key]);
}

/*加密解密
**默认解密
*/

function authcode($string,$ssl_public,$ssl_private, $operation = 'D') {
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

	if(false == ($pi_key || $pu_key)) return false;//证书错误的情况

	$data = "";

	if($operation=='D'){

	openssl_private_decrypt(base64_decode($string),$data,$pi_key);//私钥解密

	}elseif($operation=='E'){

	openssl_public_encrypt($string,$data,$pu_key);//公钥加密

	$data = base64_encode($data);

	}

	return $data;

}

//随机生成安全码
function createsalt(){
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
function croppstring($str,$leng){
	$strlen=strlen($str);
	if($strlen>$leng){
		$str=mb_substr($str,0,$leng)."...";
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
function getipcountry($ip){
	$country;
	// $handle = fopen("http://ip-api.com/json/".$ip."?lang=zh-CN", "rb");
	// $contents = stream_get_contents($handle);
	// $contents=json_decode($contents,true);
	// if($contents['status']=='success'){
	// 	if($contents['country']=='香港' || $contents['country']=='澳门'){
	// 		$contents['country']='中国';
	// 	}
	// 	$country=$contents['country'];
	// }else{
	// 	$country='未知';
	// }
	// fclose($handle);
	$reader = new Reader(public_path().'GeoIp2_data/GeoLite2-Country.mmdb');
	// HK=>香港，TW=>台湾，MO=>澳门
	$record = $reader->country($ip);
	$country = $record->country->isoCode;
	if($country=='HK' || $country=='TW' || $country=='MO'){
		$country='CN';
	}
	return $country;
}
/**
 * @description：生成二维码
 * @date: 2020/5/14 0014
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function create_qrcode($data,$userInfo){
	$qrcodefile=public_path().'upload/qrcode/'.date('Y').date('m').date('d');
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
	// 	->setResizeToHeight(50);
	
	// Create generic label
	// $label = Label::create('Label')
	//     ->setTextColor(new Color(255, 0, 0));
	
	$result = $writer->write($qrCode);
	// header('Content-Type: '.$result->getMimeType());
	
	$aaa=$result->saveToFile($qrcodefile.'/'.$userInfo['game_account'].'.png');
	$filename='/upload/qrcode/'.date('Y').date('m').date('d').'/'.$userInfo['game_account'].'.png';
	UserModel::where('id',$userInfo['id'])->update(['QR_code'=>$filename]);
	return $filename;
}
 //生成二维码跳转
     function qrcode($url,$userInfo){
        $qrcodefile=public_path().'upload/qrcode/'.date('Y').date('m').date('d');
        // 扫描二维码后跳转的地址
        // 扫描二维码后跳转的地址
        $qrCode = new QrCode($url);
        // 内容区域宽高,默认为300
        $qrCode->setSize(300);
        // 外边距大小,默认为10
        $qrCode->setMargin(10);
        // 设置编码
        $qrCode->setEncoding('UTF-8');
        // 设置容错等级
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        // 设置二维码颜色,默认为黑色
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        // 设置二维码背景色,默认为白色
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        // var_dump(LabelAlignment::CENTER());
        // die;
        // 设置二维码下方的文字
        // $qrCode->setLabel('个人技术博客网站', 11, null, LabelAlignment::CENTER());
        
        ##### 二维码中的logo #####
        // $qrCode->setLogoPath('logo.jpg');
        $qrCode->setLogoSize(100, 90);
        // $qrCode->setLogoWidth(100);
        // $qrCode->setLogoHeight(90);
        ##### 二维码中的logo / #####
        // 启用内置的验证读取器(默认情况下禁用)
        $qrCode->setValidateResult(false);
        
        ########## 二维码三种显示方式 ##########
        
        // 二维码输出在浏览器上
        // header('Content-Type: ' . $qrCode->getContentType());
        // echo $qrCode->writeString();
        
        // 二维码存在本地
        $qrCode->writeFile($qrcodefile.'/'.$userInfo['game_account'].'.png');
        
        // 返回数据URI
        // data:image/png;base64,iVBORwxxx
        // $dataUri = $qrCode->writeDataUri();
        // echo '<img src="' . $dataUri . '">';
        var_dump($qrCode);
    }