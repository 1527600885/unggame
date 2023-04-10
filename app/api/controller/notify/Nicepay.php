<?php


namespace app\api\controller\notify;


class Nicepay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        if(\app\common\lib\pay\NicePay::check_sign($result)){
            $this->updateOrder($result['amount'],$result['order'],$result['currency_type'],$result['sign']);
        }else{
            echo "fail";
        }
    }
}