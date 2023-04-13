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
    /**
     * 签名
     * @param $param
     * @param $key
     * @return string
     */
    public function getSign($param, $key,$keyName = "key")
    {
        ksort($param);
        $str = http_build_query($param);
        $str.="&{$keyName}=".$key;
        $str = urldecode($str);
        return md5($str);
    }
}