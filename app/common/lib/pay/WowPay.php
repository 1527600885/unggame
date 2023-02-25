<?php


namespace app\common\lib\pay;


use think\Exception;

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
            "createOrder"=>"pay/web"
        ],
        "version"=>"1.0",
        "notifyGateWay"=>"/api/notify.wowpay/callback",
        "pay_type"=>"423",
        "sign_type"=>"MD5",
    ];
    public function run($type,$param)
    {
        $domain =  request()->domain();
        $config = $this->payConfig['debug'] ? $this->payConfig['testconfig'] : $this->payConfig['config'];
        $param['mch_id'] = $config[$this->currency_type]['mch_id'];
        $param['pay_type'] = $config['pay_type'];
        $param['notify_url'] = $domain.$config['notifyGateWay'];
        sort($param);
        $param['sign'] = http_build_query($param);
        $param['sign_type'] = $config['sign_type'];
        $reuslt_json = curl($domain.$config['gateWay'][$type],$param);
        $reuslt = json_decode($reuslt_json,true);
        if($reuslt['respCode'] == "SUCCESS")
        {
            return $reuslt;
        }else{
            throw new Exception($reuslt['tradeMsg']);
        }
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