<?php


namespace app\common\lib\pay;


use think\Exception;

class NicePay extends Pay
{
    private  $notifyUrl = "/api/notify.nicepay/callback";
    private  $app = "MCH9350";
    private static $key = "f0b094e9ae89d299d67e5203c37795ae";
    private  $api_server = "http://merchant.nicepay.pro";
    protected $transferback = "/api/notify.nicepay/transferback";
    public  function run($type, $params)
    {
        $domain =  request()->domain();
        $param = array(
            'app_key'=>$this->app,
            'balance'=>$params['trade_amount'],//支付金额，元
            'notify_url'=>$domain.$this->notifyUrl."?currency_type=".$this->currency_type,//回调地址
            'ord_id'=>$params['mch_order_no'],//商户自己的订单号
            'p_method'=>'gcash'
        );
        $param["sign"] = self::sign($param,self::$key);
        $ret_code = self::fetch_page_json($this->api_server."/api/recharge",$param);
        $ret = json_decode($ret_code,true);
        if($ret['err'] != 0)
        {
            throw new Exception($ret['err_msg']);
        }
        return ["orderNo"=>$params['mch_order_no'],"oriAmount"=>$params['trade_amount'],"payInfo"=>$ret['url']];
    }
    public function transfer($data)
    {
        $domain =  request()->domain();
        $param = array(
            'app_key'=>$this->app,
            'balance'=>$data['transfer_amount'],//请求代付的金额
            'card'=>$data['receive_account'],
            'name'=>$data['receive_name'],
            'bank'=>$data['bank_code'],
            'ord_id'=>$data['mch_transferId'],//代付订单号，用于查询
            'notify_url'=>$domain.$this->transferback
        );

        $param["sign"] = self::sign($param,self::$key);
        $ret_code = self::fetch_page_json($this->api_server."/api/withdraw",$param);
        $ret = json_decode($ret_code,true);
        if($ret['err'] == 0){
            return $ret;
        }
        throw new \Exception($ret['err_msg']);
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
    public function getBankList()
    {
        $lists  = [
            ["bankName"=>"The Bank of the Philippine Islands","short"=>"BPI"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"UNIONBANK"],
            ["bankNmae"=>"BDO Bank","short"=>"BDO"],
            ["bankNmae"=>" Asia United Bank","short"=>"AUB"],
            ["bankNmae"=>"EastWestBank","short"=>"EAST_WEST"],
            ["bankNmae"=>"Land Bank Of The Philippines","short"=>"LAND_BANK"],
            ["bankNmae"=>"Malayan Banking Berhad","short"=>"MAYBANK"],
            ["bankNmae"=>"Metrobank","short"=>"METRO_BANK"],
            ["bankNmae"=>"Philippine National Bank","short"=>"PNB"],
            ["bankNmae"=>"Philippine Bank of Communications","short"=>"PBC"],
            ["bankNmae"=>"Philippine Savings Bank","short"=>"PSB"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"PB"],
            ["bankNmae"=>"Philippine Veterans Bank","short"=>"PVB"],
            ["bankNmae"=>"Philtrust Bank","short"=>"PTC"],
            ["bankNmae"=>"Philippine Business Bank","short"=>"PBB"],
            ["bankNmae"=>"Security Bank","short"=>"SECURITY_BANK"],
            ["bankNmae"=>"United Coconut Planters Bank","short"=>"UCPB"],
            ["bankNmae"=>"Rizal Commercial Banking Corp","short"=>"RCBC"],
            ["bankNmae"=>"Rural Bank of Bayombong","short"=>"RB"],
            ["bankNmae"=>"CTBC BANK","short"=>"CTBC"],
            ["bankNmae"=>"China Bank Savings","short"=>"CBS"],
            ["bankNmae"=>"China Banking Corp","short"=>"CBC"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"DBI"],
            ["bankNmae"=>"Bank of Commerce","short"=>"BOC"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"DCPAY"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"CAMALIG_BANK"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"STARPAY"],
            ["bankNmae"=>"Malayan Banking Berhad","short"=>"MALAYAN_BANK"],
            ["bankNmae"=>"Emigrant Savings Bank","short"=>"ESB"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"SUN_BANK"],
            ["bankNmae"=>"Sterling Bank","short"=>"STERLING_BANK"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"EASTWEST_RURAL"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"OMNIPAY"],
            ["bankNmae"=>"Chinabank","short"=>"CHINABANK"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"ALL_BANK"],
            ["bankNmae"=>"ING Bank","short"=>"ING_BANK"],
            ["bankNmae"=>"UnionBank of the Philippines","short"=>"CEBUANA_BANK"],
            ["bankNmae"=>"SeaBank","short"=>"SEA_BANK"],
        ];
    }

}