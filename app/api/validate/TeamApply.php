<?php


namespace app\api\validate;
use think\Validate;

class TeamApply extends Validate
{
    protected $rule = [
        "mobile"=>"require",
        "code"=>"require",
        "email"=>"require|email",
        "name"=>"require",
        "type"=>"require|in:1,2",
        'surname'=>"require"
    ];
    protected $message = [
        "mobile.require"=>"Please fill in your mobile number",
        "email.require"=>"Please fill in your email",
        "email.email"=>"Email number format is incorrect",
        "name.require"=>"Please fill in your name",
        "surname.require"=>"Please fill in your surname"
    ];

}