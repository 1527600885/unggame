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

use onekey\Email;
use plugins\alisms\addons\AliSms;
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
        $title = lang('system.code');
        $code  = rand(1000,9999);
        $font_size1 = "style='font-size:12px'";
        $font_size2 = "style='font-size:12px'";
        $body  = "<p><img src='https://game.unicgm.com/upload/reg.png' /></p>
	<div>
	<p {$font_size1}>Your unicorn game registration verification code is <span style='font-size:16px;color:green'>{$code}<span> </p>
	<p {$font_size1}>Leaking the verification code will make the account unsafe. </p>
	<p {$font_size1}>Please keep the verification code safe.</p>
	<div style='margin-top:180px'>
		<p {$font_size2}>Unicorn is a Web3.0 game that provides global users with fun, interesting and profitable gameproducts. It currently has millions of gamers around the world. Our goal is to bring users to playgames to make money, a new way of consumption and entertainment.</p>
		<p style='margin-top:25px;font-size:12px'>Our official website:</p>
		<p><a href='//www.unicgm.com' {$font_size2}>www.unicgm.com</a></p>
		<p {$font_size2}>Our App download link:</p>
		<p><a href='//www.unicgm.com/download' {$font_size2}>www.unicgm.com/download</a></p>
		<p {$font_size2}>Business cooperation:<a href='//business@unicgm.com'> business@unicgm.com</a></p>
		<p {$font_size2}>Customer service:<a href='//service@unicgm.com'>service@unicgm.com</a></p>
	</div>";
        $result = Email::send($email, $title, $body);
        if ($result['status'] === 'success') {
            $salt  = rand_id(8);
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
}