<?php


namespace app\common\lib\pay;


use think\Exception;
use think\facade\Log;

/**
 * Class WowPay
 * @package app\common\lib\pay
 */
class WowPay extends Pay
{
    /**
     * @var array
     */
    public $payConfig = [
        "debug"=>false,

        "testconfig"=>[
            "MYR"=>[
                "requestUrl"=>"https://gx83ixk6srer.wowhescqct.com",
                "mch_id"=>"111887001",
                "key"=>"8ba4b3d14415441aa9fc1eca23093c7c",
                "dfkey"=>"2A0QHL5ZQ0LLNYUCZGPFQ1TPOJELOGG3"
            ]
        ],
        "config"=>[
            "MYR"=>[
                "requestUrl"=>"https://gx83ixk6srer.wowhescqct.com",
                "mch_id"=>"300777778",
                "key"=>"07c0ac428b434ff39aa6bc97012271e7",
                "dfkey"=>"MNKXYOWGVOMKNELNYBYICAVQDUBC5DM7"
            ],
            "NGN"=>[
                "requestUrl"=>"https://gx83ixk6srer.wowhescqct.com",
                "mch_id"=>"900776827",
                "key"=>"767701826b6042a39cda3575655b2c13",
                "dfkey"=>"2A0QHL5ZQ0LLNYUCZGPFQ1TPOJELOGG3"
            ],
            "PHP"=>[
                "requestUrl"=>"https://gx83ixk6srer.wowhescqct.com",
                "mch_id"=>"777999857",
                "key"=>"d1d0562ede0f41179e529b2d10321969",
                "dfkey"=>"2A0QHL5ZQ0LLNYUCZGPFQ1TPOJELOGG3"
            ]
        ],
        "gateWay"=>[
            "createOrder"=>"/pay/web",
            "transfer"=>"/pay/transfer"
        ],
        "version"=>"1.0",
        "notifyGateWay"=>"/api/notify.wowpay/callback",
        "backurl"=>"/api/notify.wowpay/transferback",
        "pay_type"=>"423",
        "sign_type"=>"MD5",
        "page_url"=>"https://www.unggame.com/#/pages/center/wallet"
    ];

    /**
     * 支付
     * @param $type
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function run($type, $param)
    {
        $domain =  request()->domain();
        $config = $this->payConfig['debug'] ? $this->payConfig['testconfig'] : $this->payConfig['config'];
        $param['mch_id'] = $config[$this->currency_type]['mch_id'];
        $param['pay_type'] = $this->payConfig['pay_type'];
        $param['notify_url'] = $domain.$this->payConfig['notifyGateWay'];
        $param['version'] = $this->payConfig['version'];
        $param['page_url'] = $this->payConfig['page_url'];
        $param['mch_return_msg'] = $this->currency_type;
        $key = $config[$this->currency_type]["key"];
        $param['sign'] = $this->getSign($param,$key);
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



    /**
     * @param $bankName
     * @return string
     */
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
    public function transfer($param)
    {
        $config = $this->payConfig['debug'] ? $this->payConfig['testconfig'] : $this->payConfig['config'];
        $domain =  request()->domain();
        $data = [
            "mch_transferId"=>$param['mch_transferId'],
            "mch_id"=> $config[$this->currency_type]['mch_id'],
            "transfer_amount"=>$param['transfer_amount'],
            "apply_date"=>date("Y-m-d H:i:s"),
            "bank_code"=>$param['bank_code'],
            "receive_name"=>$param['receive_name'],
            "receive_account"=>$param['receive_account'],
            "back_url"=> $domain.$this->payConfig['backurl']
        ];
        $key = $config[$this->currency_type]["dfkey"];
        $data['sign'] = $this->getSign($data,$key);
        $data['sign_type'] = $this->payConfig['sign_type'];
        $reuslt_json = curl($config[$this->currency_type]["requestUrl"].$this->payConfig['gateWay']["transfer"],$data);
        $result = json_decode($reuslt_json,true);
        if($result['respCode'] == "SUCCESS")
        {
            return $result;
        }else{
            throw new Exception($result['errorMsg']);
        }
    }
}