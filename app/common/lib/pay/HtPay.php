<?php


namespace app\common\lib\pay;


class HtPay extends Pay
{
//    private $public_key  = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC3ecgxe70CG7dlAXKFlpQf2Pe3jbTZdE/HPQezF4F3NHbG1bqcBC3Gacfclip7JzCEaaFIfi86Z3/ONskIVvNDm2iV4pXfp6dVJH9OMiVqSHmGgIxn9QP+x56o2u035oOOWHlcPi0CoaygwMmKiA2z+Shf/+J0fAMZ7i3OTn2CGwIDAQAB";
//    private $mchPrivateKey = "MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBALd5yDF7vQIbt2UBcoWWlB/Y97eNtNl0T8c9B7MXgXc0dsbVupwELcZpx9yWKnsnMIRpoUh+Lzpnf842yQhW80ObaJXild+np1Ukf04yJWpIeYaAjGf1A/7Hnqja7Tfmg45YeVw+LQKhrKDAyYqIDbP5KF//4nR8AxnuLc5OfYIbAgMBAAECgYA5a1Vplw35wO7OH3vVruBAb0hnG2QDwdDNy53DQJH12mdppq218eDZfXVc5Wn6DLO0XNJqu4LQRl/LC34yq/OJJxkPQODFhDcIFZLNAPyf8COBUxWvRP6t6JAuwOSwz6K4DRXTYOvfvTGDdVSjOKiMLxMu9wp2hSh6jI4JmLtcwQJBAOTl9jyfZfEv/uwBNIblSyT7vIHOIrCsuY2j+x+BJzuPe+B98RdgY5pqRYdesTDJ/VLyi3vgphtcBa6OmeJyjJECQQDNMwkrZFwLPDHXZhQHpQ/tJyXNoKjWbXOeNM1SiGCYvGhYZ6d6lf4zWpzXtZUmSgTKHZcT7aqI5/zYSnocP2nrAkBTNKC40ryM2wSQp7N/YbRaIkQY72S+0lq0+Snc/ubTCMpgBYfxFnG+fOj/V1WTxakXUOGRS002XtIV7PJVVYxxAkBzb3aFFpxBJcfUKPSzqEPhYdh+aRcSKdiU85deCqJsyfDZatZou+CY+yyonNofBBzVpvfKmjUJd5hiBOPU6EepAkAYSIbHOQY6XLMLZWODd00DNjNM63QPq4DMyWYOAWPRA8yZrxyXXn9hxvAfzz2stZoOf2TdUL016dKIrlS5pKAs";

    private $public_key = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCN9VDPnoJwDpK2HXY2ieXED+uUIJTgUKhsYERMubHYY4pozVQkxjmTBeKQ3EadveEO9NVbPCb9zuiHGmlmKh3gyNx285KJcA/HN4JmY1ivBMMtp2U96oKHztqLPYEI0gUlyBf0IbPQ6gFNmiUdXco1Q47nWiXqOEr+2mfd8s8VWwIDAQAB';
    private $mchPrivateKey = 'MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAKezOAss6WPbKsy1VZTl89pMd//xZANrW01MH3GmiDv4rhKid1Gp7p/T7DKCpRbARECr86LtkjecsRcnFa7u4+1eL5z1GsgMp7U0JL5L5zr8EsOWFs/32FII29ZQXLnwNenAZtxGD2dHmhHkfv52t6f1OXG6sy93VtlJZvMLveD/AgMBAAECgYAf+GzAxKkh3lCEgjV0k3ovrdBavNxCQp8/VznPYt4qALi+2LZCnVDeq3omDv4GHlVktuNVtlDfxUGFlm/tz6En+abWvE+Pn2AgGPTun+b2E5nzHphcfvo3+eEn/Es9qbZsSErngmIpLZ/y/bnJ6dNJitc6FlvLp8/KDqa0NAVJkQJBAPAMMcMVuqNak2Dyrq5T3pDQu7f1aAzgs8CPTyK904CIU8QFGan8yCndBL+GQjtQbSLCU99N/6HlZDd/QBxkuBsCQQCy2DaNzuDt4C2QYKnYjcnIPYIZUNaJF9mJeXHMT1GJnc8OToD5jds83CRtctyyI0Hos5VnOGVR/eW2yqr4VVDtAkAqPxsq6FIWmcRCVbOkfqI2/mVrNMeBLLK1+wLEbIAiqNuFLhicMB7SL1G8m1ZgtgDfEzBLpqCMz6BZnA2ecaNtAkAFHS2iLHI+GxTydfElYhiNA0U/GBKqZOYxiil44CPCvaJ4FEKX4DiOqvTXtFsfNObjko8JHpG3IH17FpyA8V+ZAkA5MjQXUksAPT4Mier36raFUKy9csc8xfPu/AvCQ+H047dwWgzHx1B38BKN10n6Uz68gHL2ZNFfhCOl+Hjkwe7G';
    private $mch_no = '861100000033451';
    protected $CallbackUrl = "/api/notify.htpay/callback";
    protected $busi_code = "101202";
    protected $apiUrl = "https://cksax.hntwq.com";
    public $md5Key = "F94464C64353F068C3E970B3CC41FA58";
    public function run($type,$param)
    {
        $domain =  request()->domain();
        $data = [
            "mer_no" =>$this->mch_no,
            "phone" =>input("param.phone"),
            "pname" =>input("param.name"),
            "order_amount" =>$param['trade_amount'],
            "goods" =>"UNGGame",
            "notifyUrl" => $domain.$this->CallbackUrl,
            "pageUrl" =>"https://unggame.com",
            "ccy_no" =>$this->currency_type,
            "pemail" =>input("param.email"),
            "busi_code" =>$this->busi_code,
            "mer_order_no" =>$param["mch_order_no"]
        ];
        $data = $this->encrypt($data,$this->mchPrivateKey);
        $data=json_encode($data, JSON_UNESCAPED_UNICODE);
        $result_json = $this->globalpay_http_post_res_json($this->apiUrl."/ty/orderPay",$data);
        $result = json_decode($result_json,true);
        if(isset($result['status']) && $result['status'] == 'SUCCESS')
        {
            return ["orderNo"=>$param['mch_order_no'],"oriAmount"=>$param['trade_amount'],"payInfo"=>$result['order_data']];
        }else{
            throw new \Exception($result['err_msg']);
        }
    }
    //支付加密
    private function encrypt($data){
        $mch_private_key = $this->mchPrivateKey;
        ksort($data);
        $str = '';
        foreach ($data as $k => $v){
            if(!empty($v)){
                $str .=(string) $k.'='.$v.'&';
            }
        }
        $str = rtrim($str,'&');
        $encrypted = '';
        //替换成自己的私钥
        $pem = chunk_split($mch_private_key, 64, "\n");
        $pem = "-----BEGIN PRIVATE KEY-----\n" . $pem . "-----END PRIVATE KEY-----\n";
        $private_key = openssl_pkey_get_private($pem);
        $crypto = '';
        foreach (str_split($str, 117) as $chunk) {
            openssl_private_encrypt($chunk, $encryptData, $private_key);
            $crypto .= $encryptData;
        }
        $encrypted = base64_encode($crypto);
        $encrypted = str_replace(array('+','/','='),array('-','_',''),$encrypted);

        $data['sign']=$encrypted;
        return $data;
    }
    //请求
    private function globalpay_http_post_res_json($url, $postData)
    {
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/json',
                'content' => $postData,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
    public function transfer($param)
    {
        $domain =  request()->domain();
        $data = [
            "summary" =>"summary",
            "bank_code" =>$param['bank_code'],
            "acc_name" =>$param['acc_name'],
            "mer_no" =>$this->mch_no,
            "order_amount" =>$param['transfer_amount'],
            "mobile_no" =>$param['mobile_no'],
            "acc_no" =>$param['acc_no'],
            "notifyUrl" =>$domain.$this->CallbackUrl,
            "ccy_no" =>$this->currency_type,
            "mer_order_no" =>$param['mch_transferId']
        ];
        $data = $this->encrypt($data,$this->mchPrivateKey);
        $data=json_encode($data, JSON_UNESCAPED_UNICODE);
        $result_json = $this->globalpay_http_post_res_json($this->apiUrl."/ty/orderPay",$data);
        $result = json_decode($result_json,true);
        if(isset($result['status']) && $result['status'] == 'SUCCESS')
        {
            return ["orderNo"=>$param['mch_order_no'],"oriAmount"=>$param['trade_amount'],"payInfo"=>$result['order_data']];
        }else{
            throw new \Exception($result['err_msg']);
        }
    }
}