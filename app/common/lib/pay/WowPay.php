<?php


namespace app\common\lib\pay;


use think\Exception;
use think\facade\Log;
class WowPay extends Pay
{
    protected $payConfig = [
        "debug"=>true,
        "testconfig"=>[
            "MYR"=>[
                "requestUrl"=>"https://gx83ixk6srer.wowhescqct.com",
                "mch_id"=>"111887001",
            ]
        ],
        "config"=>[
            "MYR"=>[
                "requestUrl"=>"https://gx83ixk6srer.wowhescqct.com",
                "mch_id"=>"111887001",
            ]
        ],
        "gateWay"=>[
            "createOrder"=>"/pay/web"
        ],
        "version"=>"1.0",
        "notifyGateWay"=>"/api/notify.wowpay/callback",
        "pay_type"=>"423",
        "sign_type"=>"MD5",
        "key"=>"2A0QHL5ZQ0LLNYUCZGPFQ1TPOJELOGG3"
    ];
    public function run($type,$param)
    {
        $domain =  request()->domain();
        $config = $this->payConfig['debug'] ? $this->payConfig['testconfig'] : $this->payConfig['config'];
        $param['mch_id'] = $config[$this->currency_type]['mch_id'];
        $param['pay_type'] = $this->payConfig['pay_type'];
        $param['notify_url'] = $domain.$this->payConfig['notifyGateWay'];
        $param['version'] = $this->payConfig['version'];
        $param['sign'] = $this->getSign($param);
        $param['sign_type'] = $this->payConfig['sign_type'];
        $reuslt_json = curl($config[$this->currency_type]["requestUrl"].$this->payConfig['gateWay'][$type],$param);
        $result = json_decode($reuslt_json,true);
        if($result['respCode'] == "SUCCESS")
        {
            return $result;
        }else{
            throw new Exception($result['tradeMsg']);
        }
    }
    public function getSign($param)
    {
        ksort($param);
        $str = http_build_query($param);
        $str.="&key=".$this->payConfig["key"];
        $str = urldecode($str);
        return md5($str);
    }
    public function getBankCode($bankName)
    {
        $list = [
            "Bank of america" =>"AAAA",
            "Affin Bank" =>"AFFIN",
            "AGRO" =>"AGRO",
            "Alliance Bank Malaysia Berhad" =>"ALLIANCE",
            "AmBank" =>"AM",
            "Bangkok Bank Malaysia" =>"BAKO",
            "Bank Rakyat" =>"BKRM",
            "Bank Muamalate" =>"BMMB",
            "BNP PARIBAS MALAYSIA" =>"BNPB",
            "BSN" =>"BSN",
            "Bank of china" =>"CCCC",
            "CIMB Bank" =>"CIMB",
            "Citibank Malaysia" =>"CITI",
            "DEUTSCHE BANK" =>"DEUT",
            "EON Bank" =>"EON",
            "Hong Leong Bank" =>"HONGLEONG",
            "HSBC" =>"HSBC",
            "INDUSTRIAL & COMMERCIAL BANK OF CHINA" =>"ICBC",
            "Bank Islam Malaysia" =>"ISLAM",
            "J.P. MORGAN CHASE BANK" =>"JPMB",
            "KUWAIT FINANCE HOUSE" =>"KFHB",
            "Maybank" =>"MAY",
            "MBSB Bank Berhad" =>"MBSB",
            "CHINA CONST BK (M) BHD" =>"MCCB",
            "MIZUHO BANK" =>"MIZU",
            "MUFG BANK" =>"MUFG",
            "OCBC" =>"OCBC",
            "Public Bank Berhad" =>"PUBLIC",
            "RHB Bank" =>"RHB",
            "Standard Chartered Bank Malaysia" =>"SCBM",
            "BANK SIMPANAN NASIONAL" =>"SINA",
            "SUMITOMO MITSUI BANKING" =>"SUMB",
            "UOB" =>"UOB"
        ];
        return isset($list[$bankName]) ? $list[$bankName] : "";
    }
}