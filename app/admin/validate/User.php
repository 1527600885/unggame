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
namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'nickname' => 'require|min:2|max:40',
        'email'    => 'require|email',
        'mobile'   => 'mobile',
		'whatsapp'   => 'max:150',
		'telegram'   => 'max:150',
		'line'   => 'max:150',
        'cover'    => 'max:255',
        'describe' => 'max:255',
        'password' => ['min' => 6,'max' => 40],
    ];
    protected $message  =   [
        'nickname.require' => '用户昵称不能为空',
        'nickname.min'     => '用户昵称不能少于2个字符',
        'nickname.max'     => '用户昵称不能超过40个字符',
        'email'            => '用户邮箱格式不正确',
        'mobile'           => '用户手机格式不正确',
		'whatsapp.max'     => 'WhatsApp不能超过150个字符',
		'telegram.max'     => 'Telegram不能超过150个字符',
		'line.max'         => 'Line不能超过150个字符',
        'cover.max'        => '用户头像不能超过255个字符',
        'describe.max'     => '用户签名不能超过255个字符',
        'password.min'     => '用户密码不能少于6个字符',
        'password.max'     => '用户密码不能超过40个字符',
    ];
}