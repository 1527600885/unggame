<?php


namespace app\common\lib\pay;


class TopPay extends Pay
{
    private $mchPrivateKey = "MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAN1QQO3J00Q4r6GEZFjb7D+/YShP/DRj33JQxO3XJ7zz7oD4qQavJDTRwCFsckdJsRcfwt+nxbPV3jkua6mmL6gBVx/jk2lxh48m1xQ6YPdKIhCnPZCIfTRVOw3WU1Lyi+PZVzBoykgQy2kf7t4I1Yl3SGW+ccb5qxmBhyFMLaUbAgMBAAECgYBdCMWisHstbJ74SQ1eBWV1DuCq76TX6TwfdDC0wwOjfO/AK8fyVWHlCl+4LTyFF0doryNencqQZNF8PDVqJcBWGbSzMRY4Y6jmnpiMPa+F4CV6Vfus+BlMGx2brLAB/qZjs1LzmoFwBiGYvCSq7JgAWID+aL6sh9oP7o4k2oXuUQJBAPUfu+jx93qCJc3OZJ7fKplmzmS41v4i6CkWOcDRg1tlfe26L2t2UeLKYou3dJoBnHiUXgJfrqNsWjEtOMkJFPUCQQDnIhDfIZbkqpZFXF0b8io1yHyG4+x7e5SOB8+Vdqwpl2gULU2UtO4ZeMaFX4HNzdcGA1cEofas+ROR3EiDVwfPAkBsDjMtuwyXSqwTj3o3trT2rqUpLXpIyWaCRjPrVfCL56+djke9HYl3ajQK1zJleXRaizzt2vQHQop3xzGTHZfJAkBZ6/X2aWIEOp3WBFYxHijv3b0c2aXScMTd8QoA0zetwrr6RpnNRgrwG/3YO80LXY7PRxNeuQh4STsk3zfS6VQfAkBALyrR7begKox9T9Gj6hkfbAk4m1AAQmglz7tTvGBJqQYiRGfTOvDwlXM2Q/A08EUcyec4Z167nSAyUgoHTCVx";
    private $public_key = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDdUEDtydNEOK+hhGRY2+w/v2EoT/w0Y99yUMTt1ye88+6A+KkGryQ00cAhbHJHSbEXH8Lfp8Wz1d45Lmuppi+oAVcf45NpcYePJtcUOmD3SiIQpz2QiH00VTsN1lNS8ovj2VcwaMpIEMtpH+7eCNWJd0hlvnHG+asZgYchTC2lGwIDAQAB";
    private $merchantCode = "S820230411102156000005";
    private $CallbackUrl = "/api/notify.toppay/callback";
    private $apiUrl = "https://id-openapi.toppay.asia";
    public function run($type,$params)
    {
        $data = array(
            'merchantCode' => $this->merchantCode,
            'orderType' => "0",
            'orderNum' => $params["mch_order_no"],
            'payMoney' => $params['trade_amount'],
            'name' => input("param.name"),
            'email' => input("param.email"),
            'phone' => input("param.phone"),
            'notifyUrl' => $this->CallbackUrl,
            'dateTime' => date("Y-m-d H:i:s"),
            'expiryPeriod' => 1200,
            'productDetail' => "UNGGame Recharge"
        );
       $sign = $this->getSigns($data);
       $data['sign'] = $sign;
       $result_json = curl_json($this->apiUrl."/gateway/pay",$data);
       echo json_encode($result_json);die();
    }
    public function getSigns($params)
    {
        ksort($params);
        $params_str = '';
        foreach ($params as $key => $val) {
            $params_str = $params_str . $val;
        }
        $sign = $this->pivate_key_encrypt($params_str, $this->mchPrivateKey);
        return $sign;
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
    public function checkSign($params)
    {
        $platSign = $params['platSign'];
        unset($params['platSign']);
        $decryptSign = $this->public_key_decrypt($platSign,$this->public_key);
        ksort($params);
        $params_str = '';
        foreach ($params as $key => $val) {
            $params_str = $params_str . $val;
        }
        if($params_str == $decryptSign) {
            return true;
        }else{
            return false;
        }
    }
    private function public_key_decrypt($data, $public_key)
    {
        $public_key = '-----BEGIN PUBLIC KEY-----'."\n".$public_key."\n".'-----END PUBLIC KEY-----';
        $data = base64_decode($data);
        $pu_key =  openssl_pkey_get_public($public_key);
        $crypto = '';
        foreach (str_split($data, 128) as $chunk) {
            openssl_public_decrypt($chunk, $decryptData, $pu_key);
            $crypto .= $decryptData;
        }

        return $crypto;
    }
}