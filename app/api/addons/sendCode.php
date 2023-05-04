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

namespace app\api\addons;

use app\api\model\EmailCode;
use onekey\Email;
use plugins\alisms\addons\AliSms;
use think\facade\Cache;
/**
 * 发送验证码
 */
class sendCode
{
    /**
     * 发送邮箱验证码
     * @param 邮箱号
     * @param 标识
     * @param 操作名
     */
    public static function email(string $email, string $name, string $operation): array
    {
        $code  = rand(1000,9999);
        $title = $code.lang('system.code');
        $operation ="Your";
        // $body  = $operation . lang('system.code')."<br/>".lang('system.hi') . $email . "!<br/>" . $title . "，".lang('system.code') . $operation . lang('system.page')."。<br/>".lang('system.code')."：" . $code . "";
        $font_size1 = "style='font-size:12px;font-weight: bolder'";
        $font_size2 = "style='font-size:12px'";
        $body  = "<p><img src='https://game.unicgm.com/upload/reg.png' style='width:300px;height:300px'/></p>
	<div>
	<p {$font_size1}>{$operation}".lang('system.code')." is <span style='font-size:16px;color:green'>{$code}</span> </p>
	<p {$font_size1}>Leaking the verification code will make the account unsafe. </p>
	<p {$font_size1}>Please keep the verification code safe.</p>
	<div style='margin-top:50px'>
		<p {$font_size2}>UNG GAME is a Web3.0 game platform, that provides global users with fun, interesting and profitable game products. It currently has millions of gamers around the world. Our goal is to bring happiness to every customer while playing games to make money, explore and practice new ways of entertainment and money.</p>
		<p style='margin-top:25px;font-size:12px'>UNG GAME official website:</p>
		<p><a href='//www.unggame.com' {$font_size2}>www.unggame.com</a></p>
	
		<p {$font_size2}>Business cooperation:<a href='//business@unicgm.com'> business@unicgm.com</a></p>
		<p {$font_size2}>Customer service:<a href='//service@unicgm.com'>service@unicgm.com</a></p>
	</div>";
        $email = trim($email);
        $result = Email::send($email, $title, $body);
        if ($result['status'] === 'success') {
            cache($email,$code,300);
            $salt  = rand_id(8);
            EmailCode::create(["email"=>$email,"code"=>$code,"create_time"=>date("Y-m-d H:i:s")]);
            $code  = password_hash($code.$name.$email.$salt.request()->ip(), PASSWORD_BCRYPT, ['cost' => 12]);

            $result=['status' => 'success','message' => lang('system.success'), 'code' => $code, 'salt' => $salt];
        }
        return $result;
    }

    /**
     * 发送短信验证码
     * @param 手机号
     * @param 标识
     * @param 操作名
     */
    public static function sms(string $mobile, string $name, string $operation): array
    {
        $code = rand(1000,9999);
        // operation模板ID
        $aliSms = AliSms::send($operation, $mobile, ['code' => $code]);
        if ($aliSms['status'] === 'success') {
            $salt  = rand_id(8);
            $code  = password_hash($code.$name.$mobile.$salt.request()->ip(), PASSWORD_BCRYPT, ['cost' => 12]);
            return ['status' => 'success','message' => '获取成功', 'code' => $code, 'salt' => $salt];
        }
        return $result;
    }
    /// 云片发送短信验证码
    public static function singleSend($mobile) {
        $code = rand(1000,9999);
        $text = '[UNGGAME] The verification code is '. $code;
        if(!self::niuxinyunCode($mobile,$text)){
            return false;
        }
        cache::set($mobile,$code,300);
        $result=['status' => 'success','message' => lang('system.success'), 'code' => 0];
        return $result;
    }
    public static function yunpianCode($mobile,$text)
    {

        $data = [
            'apikey' => "34ff173cb17ee45bb0f6444a933e2233",
            'mobile' => $mobile,
            'text' => $text,
        ];
        $header = array("Content-Type:application/x-www-form-urlencoded;charset=utf-8;", "Accept:application/json;charset=utf-8;");
        $result = self::curlPost("https://us.yunpian.com/v2/sms/single_send.json", $data,5,$header);
        $data = json_decode($result,true);
        if($data["code"]!=0){
            return false;
        }
        return true;

    }
    public static function niuxinyunCode($mobile,$text)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api2.nxcloud.com/api/sms/mtsend',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "appkey=iXZqD8M6&secretkey=UOm4pvTX&phone={$mobile}&content={$text}",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response,true);
        if($data['code'] !=0){
            return false;
        }
        return true;
    }
   public static function curlPost($url, $post_data = array(), $timeout = 5, $header = "", $data_type = "") {
            $header = empty($header) ? '' : $header;
            //支持json数据数据提交
            if($data_type == 'json'){
                $post_string = json_encode($post_data);
            }elseif($data_type == 'array') {
                $post_string = $post_data;
            }elseif(is_array($post_data)){
                $post_string = http_build_query($post_data, '', '&');
            }
            
            $ch = curl_init();    // 启动一个CURL会话
            curl_setopt($ch, CURLOPT_URL, $url);     // 要访问的地址
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // 对认证证书来源的检查   // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
            //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
            //curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
            curl_setopt($ch, CURLOPT_POST, true); // 发送一个常规的Post请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);     // Post提交的数据包
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);     // 设置超时限制防止死循环
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            //curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     // 获取的信息以文件流的形式返回 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //模拟的header头
            $result = curl_exec($ch);
         
            // 打印请求的header信息
            //$a = curl_getinfo($ch);
            //var_dump($a);
         
            curl_close($ch);
            return $result;
        }
}