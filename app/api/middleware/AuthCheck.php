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
        if (! $request->userInfo) {
            $this->error(lang('user.accountBlocked'));
            // return json(['status'=>'login', 'message'=> lang('user.accountBlocked')]);
        }
        // 下一步
        return $next($request);
    }
}