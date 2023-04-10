<?php


namespace app\common\lib\pay;


class NicePay extends Pay
{
    private  $notifyUrl = "/api/notify.nicepay/callback";
    private  $app = "MCH9350";
    private static $key = "f0b094e9ae89d299d67e5203c37795ae";
    private  $api_server = "http://merchant.nicepay.pro";
    public  function run($type, $params)
    {
        $domain =  request()->domain();
        $param = array(
            'app_key'=>$this->app,
            'balance'=>$params['trade_amount'],//支付金额，元
            'notify_url'=>$domain.$this->notifyUrl."?currency_type=".$this->currency_type,//回调地址
            'ord_id'=>$params['mch_order_no'],//商户自己的订单号
        );
        $param["sign"] = self::sign($param,self::$key);
        $ret_code = self::fetch_page_json($this->api_server."/api/recharge",$param);
        $ret = json_decode($ret_code,true);
        return ["orderNo"=>$params['mch_order_no'],"oriAmount"=>$params['trade_amount'],"payInfo"=>$ret['url']];
    }
    private static function fetch_page_json ($url, $params = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:application/json;charset=UTF-8"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_URL, $url);

        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        if ($errno != 0)
        {
            throw new Exception($errmsg, $errno);
        }
        curl_close($ch);
        return $result;
    }

    private static function sign($param,$salt){
        $data = $param;
        ksort($data);

        $str="";
        foreach ($data as $key => $value)
        {
            $str=$str.$value;
        }
        $str = $str.$salt;
        return md5($str);
    }

    public static function check_sign($param){
        $data = $param;
        $sign = $data['sign'];
        if(!$sign){
            return false;
        }
        unset($data['sign']);
        ksort($data);

        $str="";
        foreach ($data as $key => $value)
        {
            $str=$str.$value;
        }
        $str = $str."".self::$key;
        $check_sign = md5($str);

        if($sign != $check_sign){
            return false;
        }else{
            return true;
        }
    }

}