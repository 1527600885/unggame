<?php


namespace app\common\lib\pay;


class OePay extends Pay
{
    protected $apiUrl = 'https://openapi.oepay.co.in';
    protected $key = "6434e27ae4b0be2e3485f2dd";
    private $secret = "MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALrfy3aigYGuwjlktlKkjH3Pl78f7CzyAK1OFnzF/2hrl9b5uawT8MMRILDm/LIyuMpWo6aHaCzwVN4af2rxaoNI+kB6a796q8LZbkMSB3Kq4kJvoMChTK4SHC0F5vwJtC5/kgaCCBOU9ZpaEp/3T2HsjRb2z1vg/nJ04tOg5Fb/AgMBAAECgYBsx3sDiuMCHz1V9WcgQkK5tY6qpaVwIEr+ltcGOKdNHFxdui43mb/rfNvfvgXYoSfqOHa4qFee2SM9yoTjNrZ9yLdeR7QA9XM/HBfA5RQt0gWZyYtspQlbRYim0zNEsd77JOjNnzF+Y35amS5xhpmABQTqP4cqT2Y8/fYorUiBAQJBAONcUMe0ylKj9tGiKT4TH72WTZwNNRM47Iq9vlf9nDY9FQkyHiBFN4KuzB/H+7qWqx/g7GYk8APaaJWox7qEuYECQQDSaeqT1m6q55AEEuddqtncZZJcpOrcjosNrj62peoAah8Gssd67MUMMtWcxmfjnLvUM8HnmsRKmWjpcGyhFFB/AkEAoSapyyOF1JWLOINsICeF8+c5E0b5O6q5Xo2nAM8tjfQ1mNMBL3ZgJiynWk9xSYvJt0rBxJSh2tlQD+QVzUqOAQJARMforWDoFifR1PMU/HJv+vKc8HncaDKUU+mEiJIdtvr5n2fre0xQcVdgqnnU1fuTDp/In9vglH4nZD+i0tjgIwJAO1As6QN90IPl+qzivmLv3sfvEItMveWVFifwmTnNeE0PvOEwbIU0qtIM0QGuXu5A3ajiTI+b9/8x/768pgzoSg==";
    public function run($type, $params)
    {
        $data = [
            'orderNo' => $params['mch_order_no'],
            'amount' => $params['trade_amount'],
            'firstname' => input("param.realname"),
            'mobile' => input("param.mobile"),
            'email' => input("param.email"),
            'surl' => "https://www.unggame.com",
            'furl' => 'https://www.unggame.com',
            'remark' => 'test',
        ];
        $data_str = $this->getUrlStr($data);

        $sign = $this->sign($data_str, $this->secret);

        $header = [

            'Content-Type: application/json; charset=utf-8',

            'X-SIGN: ' . $sign,

            'X-SERVICE-CODE: ' . $this->key

        ];
        $result_json = $this->curl_post_content($this->apiUrl."/gold-pay/portal/createH5PayLink", $data, $header);
        echo $result_json;
        $result = json_decode($result_json, true);
    }

    public function sign($data, $extra)
    {

// 私钥

        $privateKeyBase64 = "-----BEGIN RSA PRIVATE KEY-----\n";

        $privateKeyBase64 .= wordwrap($extra, 64, "\n", true);

        $privateKeyBase64 .= "\n-----END RSA PRIVATE KEY-----\n";

// 签名

        openssl_sign($data, $signature, $privateKeyBase64, OPENSSL_ALGO_SHA512);

        return base64_encode($signature);

    }

   private function getUrlStr($data)
    {

        ksort($data);

        $urlStr = [];

        foreach ($data as $k => $v) {

            if (!empty($v) && $k != 'sign') {

                $urlStr[] = $k . '=' . rawurlencode($v);

            }

        }

        return join('&', $urlStr);

    }

    private function curl_post_content($url, $data = null, $header = [])
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $output = curl_exec($ch);

        curl_close($ch);

        return json_decode($output, 1);

    }


    /**
     * 检验RSA签名
     *
     * @param array $transData 待签名数据
     * @param string $sign 待验证签名
     * @return bool 检验结果
     */

    public static function rsaVerify($transData, $sign)

    {

        $signStr = self::checkGetUrlStr($transData);

// 私钥

        $publicKeyBase64 = "-----BEGIN PUBLIC KEY-----\n";

        $publicKeyBase64 .= wordwrap(self::$pay_public_key, 64, "\n", true);

        $publicKeyBase64 .= "\n-----END PUBLIC KEY-----\n";

        $pay_public_key = openssl_get_publickey($publicKeyBase64);

        $result = openssl_verify($signStr, base64_decode($sign), $pay_public_key, OPENSSL_ALGO_SHA512);

        return ($result == 1); // -1:错误；0：签名错误；1：签名正确

    }

    public static function checkGetUrlStr($data)

    {


        ksort($data);

        $urlStr = [];

        foreach ($data as $k => $v) {


            if ($k != 'sign' && strlen($v) > 0) {

                $urlStr[] = $k . '=' . rawurlencode($v);

            }

            if ($k == 'paymentType') {

                $urlStr[] = $k . '=' . rawurlencode($v);

            }

        }

        return join('&', $urlStr);

    }
}