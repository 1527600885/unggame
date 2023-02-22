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
namespace app\api\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'id'            => 'require',
        'email'         => 'require|email',
		// 'email'         => 'email',
        'mobile'        => 'require',
        'account'       => 'require',
        'captcha'       => 'require|captcha',
        'code'          => 'require',
        'nickname'      => 'require|max:40',
        'describe'      => 'max:255',
        'password'      => ['alphaNum', 'min' => 6, 'max' => 12],
        'phone'         =>['alphaNum', 'min' => 5, 'max' => 20],
		'sex'           => 'require',
		'pay_paasword'  => ['number','length'=>6],
        'whatsApp' =>'require',
    ];
    protected $message = [
        // 'id.require'             => '参数错误',
        // // 'email.require'          => '请填写邮箱号码',
        // 'email.email'            => '邮箱号码格式不正确',
        // 'mobile.require'         => '请填写手机号',
        // 'account.require'        => '请填写账号',
        // 'captcha.require'        => '请填写验证码',
        // 'captcha.captcha'        => '验证码不正确',
        // 'nickname.require'       => '昵称不能为空',
        // 'nickname.max'           => '昵称不能超过40个',
        // 'describe.max'           => '签名不能超过255个',
        // 'password.require'       => '请填写密码',
        // 'password.min'           => '密码不能小于6个',
        // 'password.max'           => '密码不能大于40个',
		'id.require'             => 'system.id',
		'email.email'            => 'user.email',
        'phone.require'         => 'user.mobileEmpty',
		'mobile.require'         => 'user.mobileEmpty',
		'account.require'        => 'user.accountEmpty',
		'captcha.require'        => 'user.captchaEmpty',
		'captcha.captcha'        => 'user.captchaError',
		'nickname.require'       => 'user.nicknameEmpty',
		'nickname.max'           => 'user.nicknameError',
		'describe.max'           => 'user.describeError',
		'password.require'       => 'user.passwordEmpty',
		'password.alphaNum'      => 'user.passwordAlphaNum',
		'password.min'           => 'user.passwordMin',
		'password.max'           => 'user.passwordMax',
        'confirmpassword.require'       => 'user.passwordEmpty',
        'confirmpassword.alphaNum'      => 'user.passwordAlphaNum',
        'confirmpassword.min'           => 'user.passwordMin',
        'confirmpassword.max'           => 'user.passwordMax',
		'sex.require'            => 'user.sex',
		'pay_paasword.require'   => 'user.pay_paasword_require',
		'pay_paasword.length'    => 'user.pay_paasword_length',
        'whatsApp.require'       => 'user.whatsAppEmpty'
    ];
    protected $scene = [
        'login'          => ['email','password'],
        'loginphone'          => ['phone','password'],
        'passwordEmail'  => ['email','password','code'],
        'passwordMobile' => ['mobile','password','code'],
        'registerphone'  => ['phone','password','code','confirmpassword'],
        'registerEmail'  => ['email','password','code','confirmpassword'],
        'registerMobile' => ['mobile','password','code'],
        'bindEmail'      => ['email','code'],
        // 'bindMobile'     => ['mobile','code'],
        'codeEmail'      => ['email'],
        'codeMobile'     => ['mobile'],
        'info'           => ['id'],
        'set'            => ['nickname','sex','mobile'],
		'set_pay_paasword'=> ['pay_paasword'],
    ];
}