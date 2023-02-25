<?php


namespace app\common\lib\pay;


class Pay
{
    protected $currency_type;
    public function __construct($currency_type)
    {
        $this->currency_type = $currency_type;

    }
    public static function instance($pay_type,$currency_type)
    {
        $static = new $pay_type($currency_type);
        return $static;
    }
}