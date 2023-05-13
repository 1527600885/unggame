<?php


namespace app\common\lib\pay;


class SurePay extends Pay
{
    protected $merchant = "Surepay88";
    protected $post_url = "/api/notify.surepay/callback";
    protected $payback_url = "/api/notify.surepay/transferback";
    protected $apikey = "4449a5c22d99b4635748df69409eaaebd4099c02";
    public $callbackkey = "555b6d33e00c5746859eg70510fbbfce5100d13";
    protected $apiUrl = "https://sandbox.paymentgt.com";
    protected $bankcode = "10002493";
    public  function run($type, $params)
    {
        $domain =  request()->domain();
        $customer = "cust".time().rand(100,999);

        $data = [
            "merchant"=>$this->merchant,
            "amount"=>$params['trade_amount'],
            "refid"=>$params['mch_order_no'],
            "customer"=>$customer,
            "currency"=>$this->currency_type,
            "bankcode"=>$this->bankcode,
            "clientip"=>"52.55.100.240",
            "post_url"=>$domain.$this->post_url,//回调地址,
            "failed_return_url"=>$domain,
            "return_url"=>$domain
        ];
        $token = md5($data['merchant'].$data['amount'].$data['refid'].$data['customer'].$this->apikey.$this->currency_type."52.55.100.240");
        $data['token'] = $token;
        $url = $this->apiUrl."/fundtransfer";
        $form = "<body><form id='subform' style='display: none' action='{$url}' method='POST'>";
        foreach ($data as $k=>$v){
            $form.="<input name='{$k}' value='$v'/>";
        }
        $form.="<button type='submit'>提交</form></body><script>window.onload = function(){document.getElementById('subform').submit()}</script>";
        return ["orderNo"=>$params['mch_order_no'],"oriAmount"=>$params['trade_amount'],"payInfo"=>$form];
    }
    public function transfer($param)
    {
        $domain =  request()->domain();
        $data = [
            "merchant"=>$this->merchant,
            "customer"=>$param['game_account'],
            "currency"=>$this->currency_type,
            "bankcode"=>$this->bankcode,
            "destbankaccname"=>$param['bank_name'],
            "destbankcode"=>$param['bank'],
            "destbankaccno"=>$param['receive_account'],
            "refid"=>$param['mch_transferId'],
            "amount"=>$param['receive_account'],
            "post_url"=>$domain.$this->payback_url,//回调地址,
            "clientip"=>"52.55.100.240",
        ];

        $token = md5($data['merchant'].$data['amount'].$data['refid'].$data['customer'].$this->apikey.$this->currency_type."52.55.100.240");

        $data['token'] = $token;
        $result_json = curlNoIpSet($this->apiUrl."/v1/payout",$data);
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