<?php
// +----------------------------------------------------------------------
// | OneKeyAdmin [ Believe that you can do better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2023 http://onekeyadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MUKE <513038996@qq.com>
// +----------------------------------------------------------------------
namespace plugins\statistics\addons;

use app\admin\model\Config as ConfigModel;
/**
 * 使用腾讯位置服务https://lbs.qq.com/service/webService/webServiceGuide/webServiceIp
 */
class TencentLocationService 
{
    /**
     * 请求域名
     */
    private $domain = "https://apis.map.qq.com";

    /**
     * 获取ip归属地
     */
    public function ipLocation() 
    {
        $location  = ['nation'=>'中国','province'=>'','city'=>''];
        $config    = ConfigModel::getVal('app_statistics');
        if (! empty($config)) {
            if ($config['open'] == 1) {
                $key    = $config['tencent_location_key'];
                $result = file_get_contents('https://apis.map.qq.com/ws/location/v1/ip?ip='.request()->ip().'&key='.$key);
                $result = json_decode($result,true);
                if ($result['status'] == 0) {
                    $location = $result['result']['ad_info'];
                }
            }
        }
        return $location;
    }
}