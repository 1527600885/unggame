<?php


namespace app\api\controller\v2;


use app\admin\model\Config as ConfigModel;
use app\api\BaseController;
use app\api\model\CapitalFlow as CapitalFlowmodel;
use app\api\model\CurrencyAll;
use app\api\model\GameBetLog;
use app\common\lib\Redis;
use think\Exception;

class Payment extends BaseController
{
    public function getWallet()
    {
        $userInfo = $this->request->userInfo;
        if ($userInfo) {
            $data['balance'] = $userInfo->balance;
            // 今日股息
            $data['dividend'] = CapitalFlowmodel::where(['uid' => $userInfo->id, 'type' => 4, 'money_type' => 1])->whereDay('add_time')->value("SUM(CAST(amount as DECIMAL (18,3))) as amount") ?? 0;
            // 总获得的股息
            $data['dividends'] = CapitalFlowmodel::where(['uid' => $userInfo->id, 'type' => 4, 'money_type' => 1])->value("SUM(CAST(amount as DECIMAL (18,3))) as amount") ?? 0;
            //最小提现金额
            $withdrawConfig = ConfigModel::getVal('withdraw');
            $data['miniwithrawal'] = $withdrawConfig['minprice'];
            // 今日流水
            $data['water'] = round(GameBetLog::where(['user_id' => $userInfo->id])->whereDay('betTime')->sum('betAmount'), 2);
            //要显示的货币
            $data['currency'] = CurrencyAll::where("is_show", 1)->cache("currency_all_show")->field("id,name,type,country,symbol,thumb_img,url_list")->select();
            $country = getipcountry($this->request->ip());
            $rateList = $this->cacheRate();

            foreach ($data['currency'] as $v) {
                if ($v['type'] == 2) {
                    $v['rate'] = $rateList[$v['name']];
                } else {
                    $v['rate'] = bcdiv('1', $this->getCoinMarketCap('USD', $v['name']), 8);
                }
                $v['amount'] = bcmul($v['rate'], $data['balance'], 8);
                if (!empty($v['country']) && $v['country'] == $country) {
                    $data['symbol'] = $v['symbol'];
                    $data['country_amount'] = $v['amount'];
                }
            }
            if (!isset($data['country_amount'])) {
                $data['symbol'] = "$";
                $data['country_amount'] = $data['balance'];
            }

        }
        $this->success(lang('system.operation_succeeded'), $data);

    }
    public function getPaymentInfo()
    {
        $param = input("param.");
        $currency = CurrencyAll::where("is_show", 1)->cache("currency_all_show")->field("id,name,type,country,symbol,thumb_img,url_list")->select();
        $lists = $currency->column("thumb_img,url_list","name");
        $data = $lists[$param['type']];
        $this->success(lang('system.operation_succeeded'), $data);
    }
    public function getRate($type = 2)
    {
        $data = [];
        if ($type == 2) {
            $list = \app\api\model\v2\Payment::where(['is_show' => 1, 'type' => $type])->order('id asc')->column("currency_name");
            $currency_list = [];
            foreach ($list as $v) {
                $currency_list = array_merge($currency_list, explode(",", $v));
            }

            $rateList = $this->cacheRate();
            foreach ($currency_list as $vv) {
                $data[] = ["name" => $vv, "rate" => $rateList[$vv]];
            }
        } else {
            $list = \app\api\model\v2\Payment::field("id,name,url,type")->where(['is_show' => 1, 'type' => $type])->order('id asc')->select();

            foreach ($list as $v) {
                $v['rate'] = $this->getCoinMarketCap($v['name']);
                $data[] = $v;
            }
        }
        $this->success(lang('system.success'), $data);
    }

    public function getCoinMarketCap($type, $change = "USD")
    {
        $key = "virtualRate_{$type}_{$change}";
        $redis = (new Redis(['select' => 2]))->getRedis();
        $rate = $redis->get($key);
        if (!$rate) {
            $url = 'https://pro-api.coinmarketcap.com/v2/tools/price-conversion';
            $parameters = [
                'amount' => '1',
                "symbol" => $change,
                'convert' => $type
            ];

            $headers = [
                'Accepts: application/json',
                'X-CMC_PRO_API_KEY: 9511b77d-1c7b-4875-b8a4-f8531580f328'
            ];
            $qs = http_build_query($parameters); // query string encode the parameters
            $request = "{$url}?{$qs}"; // create the request URL


            $curl = curl_init(); // Get cURL resource
            curl_setopt_array($curl, array(
                CURLOPT_URL => $request,            // set the request URL
                CURLOPT_HTTPHEADER => $headers,     // set the headers
                CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
            ));

            $response = curl_exec($curl); // Send the request, save the response
            $data = json_decode($response, true); // print json decoded response
            //echo $response;die();
            curl_close($curl); // Close request
            if ($data['status']['error_code'] == 0) {
                $rate = $data['data'][0]['quote'][$type]['price'];
                $redis->set($key, $rate, 300);
            } else {
                throw new Exception($data['status']['error_message']);
            }
        }
        return $rate;
    }

    public function cacheRate()
    {
        $key = "cashRateList";
        $redis = (new Redis(['select' => 2]))->getRedis();
        $rateList = $redis->get($key);
        if (!$rateList) {
            $req_url = 'https://api.exchangerate.host/latest?base=USD';
            $response_json = file_get_contents($req_url);
            if (false !== $response_json) {
                try {
                    $response = json_decode($response_json, true);
                    if ($response['success'] === true) {
                        $rateList = $response['rates'];
                        $redis->set($key, json_encode($rateList), 3600);
                    } else {
                        throw new Exception("System error");
                    }
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        } else {
            $rateList = json_decode($rateList, true);
        }
        return $rateList;

    }
}