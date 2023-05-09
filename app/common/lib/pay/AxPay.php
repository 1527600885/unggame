<?php


namespace app\common\lib\pay;


class AxPay extends Pay
{
    protected $AccessKey = "MX8ZY9JBeoFn9GVapP46C2lp5a5";
    protected $PayChannelId = "103";
    protected $CallbackUrl = "/api/notify.axpay/callback";
    protected $payBackUrl = "/api/notify.axpay/transferback";
    public $SecretKey = "JkKEA3eNZ2TlAeBbKpeJHR52znkJ5XhqkJBw2mKP";
    protected $apiUrl = "https://merchant.axpay.vip";
    public  function run($type, $params)
    {
        $domain =  request()->domain();
        $data = [
            "Timestamp"=>time(),
            "AccessKey"=>$this->AccessKey,
            "PayChannelId"=>$this->PayChannelId,
            "OrderNo"=>$params["mch_order_no"],
            "Amount"=>$params['trade_amount'],
            "Ext"=>$this->currency_type,
            "CallbackUrl"=>$domain.$this->CallbackUrl,//回调地址,
        ];
        $sign = $this->getSign($data,$this->SecretKey,"SecretKey");
        $data['sign'] = $sign;
        $result_json = curl_json($this->apiUrl."/api/PayV2/submit",$data);
        $result = json_decode($result_json,true);
        if($result['Code'] == 0)
        {
            return ["orderNo"=>$params['mch_order_no'],"oriAmount"=>$params['trade_amount'],"payInfo"=>$result['Data']['PayeeInfo']['CashUrl']];
        }else{
            throw new \Exception($result['Message']);
        }
    }
    public function transfer($param)
    {
        $domain =  request()->domain();
        $data = [
            "Timestamp"=>time(),
            "AccessKey"=>$this->AccessKey,
            "PayChannelId"=>$this->PayChannelId,
            "OrderNo"=>$param['mch_transferId'],
            "Amount"=>$param['transfer_amount'],
            "Ext"=>$this->currency_type,
            "CallbackUrl"=>$domain.$this->payBackUrl,//回调地址,
            "Payload"=>"IndiaBank_AccountName={$param['receive_name']}&IndiaBank_AccountNo={$param['receive_account']}&IndiaBank_BankName=SBI&IndiaBank_Ifsc={$param['ifsc']}"
        ];
        $sign = $this->getSign($data,$this->SecretKey,"SecretKey");
        $data['sign'] = $sign;
        $reuslt_json = curl_json($this->apiUrl."/api/WithdrawalV2/submit",$data);
        $result = json_decode($reuslt_json,true);
        if($result['Code'] == 0)
        {
            return $result;
        }else{
            throw new \Exception($result['Message']);
        }
    }
}