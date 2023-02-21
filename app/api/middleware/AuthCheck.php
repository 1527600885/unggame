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
declare (strict_types = 1);

namespace app\api\middleware;

use app\api\model\User;
use app\api\model\UserToken;
use app\api\BaseController;
use think\facade\Cache;

/**
 * 用户鉴权（按需引入中间件）
 */
class AuthCheck extends BaseController
{
    protected $freeze_method = [
       1=>["User/getwallet","User/userinfo","Withdrawal/currencylist"],
       2=>["Withdrawal/setwithdrawal_log",],
    ];
    public function handle($request, \Closure $next)
    {
        $time  = 14*24; // 后台控制token过期时间
        $input = input('post.');
        $input['token']=$this->request->header('Accept-Token');
        if (empty($input['token'])) {
            $this->error(lang('user.tokenEmpty'));
            // return json(['status'=>'login', 'message'=> lang('user.tokenEmpty')]);
        }
        $id = UserToken::where("token", $input['token'])->whereTime("create_time","-$time hours")->value('user_id');
        if (! $id) {
            $this->error(lang('user.tokenExpired'));
            // return json(['status'=>'login', 'message'=> lang('user.tokenExpired')]);
        }
        $password = User::where('id', $id)->value('password');
        $ip  = Cache::get("user_ip_{$id}");
        if(!$ip){
            $ip = $request->ip();
            Cache::set("user_ip_{$id}",$request->ip(),14*24*3600);
        }
        if($ip!= $request->ip()){
            Cache::set("user_ip_{$id}","");
            $this->error(lang('user.tokenError'));
        }
//        if (! password_verify($id . $request->ip() . $password, $input['token'])) {
//            $this->error(lang('user.tokenError'));
//            // return json(['status'=>'login', 'message'=> lang('user.tokenError')]);
//        }
        $request->userInfo = User::with(['group'])->where('id', $id)->where('status', 1)->find();
        $action = strtolower($this->request->action());
        if(!in_array($action,$this->noNeedCheckIp) || in_array("*",$this->noNeedCheckIp))
        {
            //获取访问的目标地区
            $country=getipcountry($this->request->ip());
            if(in_array($country['country'],["中国","香港","澳门"])){
                $this->error(lang('system.iperror'),$country,407);
            }
        }
        if (! $request->userInfo) {
            $this->error(lang('user.accountBlocked'));
            // return json(['status'=>'login', 'message'=> lang('user.accountBlocked')]);
        }
        if($request->userInfo['balance_status'] == 0)
        {
            $request_method = $request->controller().'/'.$request->action();
            if(!in_array($request_method,$this->freeze_method[1])){
                if(in_array($request_method,$this->freeze_method[2])){
                    $this->error(lang("user.balanceFreeze"));
                }else{
                    $request->userInfo['balance'] = 0;
                }
            }
        }
        // 下一步
        return $next($request);
    }
}