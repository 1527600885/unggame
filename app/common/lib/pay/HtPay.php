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
        $data['sign'] = $this->sign($data);
        $result_json = curl_json($this->apiUrl."/ty/orderPay",$data);
        $result = json_decode($result_json,true);
        if($result['Code'] == 0)
        {
            return ["orderNo"=>$param['mch_order_no'],"oriAmount"=>$param['trade_amount'],"payInfo"=>$result['Data']['PayeeInfo']['CashUrl']];
        }else{
            throw new \Exception($result['Message']);
        }
    }
    private function pivate_key_encrypt($data, $pivate_key)
    {
        $pivate_key = '-----BEGIN PRIVATE KEY-----'."\n".$pivate_key."\n".'-----END PRIVATE KEY-----';
        $pi_key = openssl_pkey_get_private($pivate_key);
        $crypto = '';
        foreach (str_split($data, 117) as $chunk) {
            openssl_private_encrypt($chunk, $encryptData, $pi_key);
            $crypto .= $encryptData;
        }

        return base64_encode($crypto);
    }
    private function sign($data,$priKey) {
        $priKey = '-----BEGIN PRIVATE KEY-----'."\n".$priKey."\n".'-----END PRIVATE KEY-----';
        $res = openssl_get_privatekey($priKey);
        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res);
        //释放资源
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }
}