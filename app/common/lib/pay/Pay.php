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
        $className = "app\common\lib\pay\\".$pay_type;
        if (class_exists($className)) {
            $static = new $className($currency_type);
            return $static;
        }
        return self::class;
    }
}