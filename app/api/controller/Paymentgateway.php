<?php


namespace app\api\controller;


use app\api\BaseController;
use app\common\lib\pay\Pay;

class Paymentgateway extends BaseController
{
    public function index()
    {
        $pay = Pay::instance("WowPay","MYR");
        $param = [
            "mch_order_no"=>'order'."ywzldvzl".time(),
            "trade_amount"=>30,
            "order_date"=>date("Y-m-d H:i:s"),
            "goods_name"=>"Recharge",
        ];
        $result = $pay->run("createOrder",$param);
        var_dump($result);
    }
}