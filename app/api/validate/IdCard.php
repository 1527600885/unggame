<?php


namespace app\api\validate;


use think\Validate;

class IdCard extends Validate
{
    protected $rule=[
        "idCard_image"=>"require",
        "idCard_image_with_hand"=>"require",
        "surname"=>"length:1,40",
        "name"=>"require|length:1,40"
    ];
    protected $message = [
        "idCard_image.require"=>"idCard.imagerequire",
        "idCard_image.url"=>"idCard.imageInvalid",
        "idCard_image_with_hand.require"=>"idCard.imageWithHand",
        "idCard_image_with_hand.url"=>"idCard.imageInvalid",
        "surname.require"=>"idCard.require",
        "surname.length"=>"idCard.surnamelengtherror",
        "name.require"=>"idCard.name",
        "name.length"=>"idCard.namelengtherror"
    ];
}