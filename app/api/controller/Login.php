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
namespace app\api\controller;

use think\exception\ValidateException;
use app\api\BaseController;
use app\api\addons\sendCode;
use app\api\model\UserGroup;
use app\api\model\UserToken;
use app\api\model\User as UserModel;
use app\api\validate\User as UserValidate;
use app\common\game\ApiGame as apigame;
use Hashids\Hashids;
use think\facade\Cache;
use think\facade\Db;
/**
 * 登录模块
 */
class Login extends BaseController
{
    protected $noNeedLogin = ['*'];
    /**
     * 登录
     */
    public function index()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('loginaccount')->check($input);
                // if($input['checklable']==1){
                //     validate(UserValidate::class)->scene('loginphone')->check($input);
                // }else if($input['checklable']==2){
                //     validate(UserValidate::class)->scene('login')->check($input);
                // }
                
            } catch ( ValidateException $e ) {
                $this->error($e->getError());
                // return json(['status' => 'error', 'message' => $e->getError()]);
            }
             $userInfo = UserModel::with(['group'])->append(['url'])->where('mobile|email',$input['account'])->find();
            // if($input['checklable']==1){
            //     $userInfo = UserModel::with(['group'])->append(['url'])->where('mobile',$input['phone'])->where('uncode',$input['uncode'])->find();
            // }else{
            //     $userInfo = UserModel::with(['group'])->append(['url'])->where('email',$input['email'])->find();
            // }
            if (! $userInfo) {
                $this->error(lang('user.accountnot'));
                // return json(['status' => 'error', 'message' => '账号不存在']);
            }
            $password = UserModel::where('id', $userInfo->id)->value('password');
            if (! $password) {
                $this->error(lang('user.wrong'));
                // return json(['status' => 'error', 'message' => '密码或账号错误']);
            }
            if (! password_verify($input['password'], $password)) {
                $this->error(lang('user.wrong'));
                // return json(['status' => 'error', 'message' => '密码或账号错误']);
            }
            // dump($userInfo->status);exit;
            if ($userInfo->status != 1) { 
                $this->error(lang('user.shield'));
                // return json(['status' => 'error', 'message' => '您的账号被屏蔽']);
            }
            // 生成令牌
            $token = password_hash($userInfo->id . $this->request->ip() . $password, PASSWORD_BCRYPT, ['cost' => 12]);
            UserToken::create([
                'user_id'     => $userInfo->id,
                'token'       => $token,
                'create_time' => date('Y-m-d H:i:s')
            ]);
            if(!$userInfo->game_account){
                $hashids = new Hashids(env('hashids'), 8,env('hashids_write'));
                $game_account=$hashids->encode($userInfo->id);
                $apigame=new ApiGame();
                $result=$apigame->create_user($game_account,$input['password']);
                $ret  = json_decode($result,true);
                if($ret['status']==0){
                    $userInfo->game_account  = $game_account;
                }
            }
            // 修改信息
            $userInfo->login_count = $userInfo->login_count + 1;
            $userInfo->login_ip    = $this->request->ip();
            $userInfo->login_time  = date('Y-m-d H:i:s');
            $userInfo->save();
            $this->success(lang('user.login'),['token'=>$token,'userInfo'=>$userInfo]);
            // return json(['status' => 'success', 'message' => '登录成功', 'token' => $token, 'userInfo' => $userInfo]);
        }
    }
    // 手机发送验证码
    public function sendphonecode(){
       $phone = '+'.input("uncode").input("phone");
        $result=sendCode::singleSend($phone);
        // $data = json_decode($result,true);
        if($result['code']!=0){
            $this->error(lang('user.codeerror'));
        }
        $this->success('',$result);
    }
    /**
     * 邮箱找回密码
     */
    public function passwordEmail()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                
                if($input['checklable']==1){
                    validate(UserValidate::class)->scene('registerphone')->check($input);
                }else{
                    validate(UserValidate::class)->scene('passwordEmail')->check($input);
                }
                
            } catch ( ValidateException $e ) {
                $this->error($e->getError());
                // return json(['status' => 'error', 'message' => $e->getError()]);
            }
            if($input['checklable']==2){
                if (! password_verify($input['code'].'index_password_email_code'.$input['email'].$input['salt'].$this->request->ip(), $input['rcode'])) {
                $this->error(lang('user.captchaError'));
                // return json(["status" => "error", "message" => lang('user.captchaError')]);
                }
            }else if($input['checklable']==1){
                 $regtype = 1;
                //          手机验证码验证
                    $pcode = cache::get('+'.$input['uncode'].$input['phone']);
                 //   var_dump($input['code']);
                    if($input['code']!=$pcode){
                        $this->error(lang('user.codeerror'));
                    }
                   
            }else{
                $this->error(lang('user.codeerror'));
            }
            
            if($input['checklable']==1){
                $save = UserModel::where(['mobile'=>$input['phone'],'uncode'=>$input['uncode']])->find();
            }else{
                $save = UserModel::where('email', $input['email'])->find();
            }
            
            if (! $save) {
                $this->error(lang('user.accountnot'));
                // return json(['status' => 'error', 'message' => lang('user.accountnot')]);
            }
            $save->password = $input['password'];
            $save->save();
            $this->success(lang('system.operation_succeeded'));
            // return json(['status' => 'success', 'message' => lang('system.operation_succeeded')]);
        }
    }

    /**
     * 短信找回密码
     */
    public function passwordMobile()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('passwordMobile')->check($input);
            } catch ( ValidateException $e ) {
                return json(['status' => 'error', 'message' => $e->getError()]);
            }
            if (! password_verify($input['code'].'index_password_mobile_code'.$input['mobile'].$input['salt'].$this->request->ip(), $input['rcode'])) {
                return json(["status" => "error", "message" => lang('user.captchaError')]);
            }
            $save = UserModel::where('mobile', $input['mobile'])->find();
            if (! $save) {
                return json(['status' => 'error', 'message' => '账号不存在']);
            }
            $save->password = $input['password'];
            $save->save();
            return json(['status' => 'success', 'message' => '修改成功']);
        }
    }
    
    /**
     * 邮箱注册
     */
    public function registerEmail()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
            if($input['checklable']==1){
                validate(UserValidate::class)->scene('registerphone')->check($input);
            }else{
                validate(UserValidate::class)->scene('registerEmail')->check($input);
            }
                
            } catch ( ValidateException $e ) {
                $this->error($e->getError());
                // return json(['status' => 'error', 'message' => $e->getError()]);
            }
            
//            if($input['code']!='5427'&&$input['checklable']==2){
//                $regtype = 2;
//                if (! password_verify($input['code'].'index_register_email_code'.$input['email'].$input['salt'].$this->request->ip(), $input['rcode'])) {
//                    $this->error(lang('user.codeerror'));
//                    // return json(["status" => "error", "message" => '邮箱验证码错误']);
//                }
//            }else if($input['checklable']==1){
//                 $regtype = 1;
//                //          手机验证码验证
//                    $pcode = cache::get('+'.$input['uncode'].$input['phone']);
//                    if($input['code']!=$pcode){
//                        $this->error(lang('user.codeerror'));
//                    }
//
//            }else{
//                $this->error(lang('user.codeerror'));
//            }
            if($input['checklable']==1&&UserModel::where(['mobile'=>$input['phone'],'uncode'=>$input['uncode']])->value('id')){
                 $this->error(lang('user.phoneerror'));
            }else if ($input['checklable']==2&&UserModel::where('email', $input['email'])->value('id')){
                  
                    $this->error(lang('user.emailerror'));
                    
                // return json(['status' => 'error', 'message' => '邮箱号已被注册']);
                
            }
           
            // if($input["password"]!=$input["confirmpassword"]){
            //     $this->error(lang('user.passwordconfirm'));
            // }
            $invite_one_uid=0;
            $invite_two_uid=0;
            $invite_three_uid=0;
            // var_dump(isset($input['invitation_code'])&&$input['invitation_code']!=='');
            // die;
            if(isset($input['invitation_code'])&&$input['invitation_code']!==''){
                //一级邀请人
                $invite_one_list=UserModel::where('invitation_code',$input['invitation_code'])->find();
                if(!$invite_one_list) $this->error("invalid invitation code");
                $invite_one_uid=$invite_one_list->id;
                //二级邀请人
                $invite_two_uid=UserModel::where('id',$invite_one_uid)->value('invite_one_uid')??0;
                //三级邀请人
                $invite_three_uid=UserModel::where('id',$invite_two_uid)->value('invite_one_uid')??0;
            }

            $group = UserGroup::where('default',1)->find();
            $group_id = 0;
            $integral = 0;
            if ($group) {
                $group_id = $group['id'];
                $integral = $group['integral'];
            }
        //     var_dump(request()->domain());
        // die;
            
            $ungaddress=$this->rand(34);
            // var_dump(strlen($ungaddress));
            // die;
            $date = date('Y-m-d H:i:s');
            $registerInfo = UserModel::create([
                'group_id'         => $group_id,
                'nickname'         => $input['nickname']?$input['nickname']:lang('system.nickname_default'),
                'cover'            => '../../static/images/header/header'.rand(1,8).'.png',
                'sex'              => 0,
                'email'            => isset($input['email'])?$input['email']:'',
                'mobile'           => isset($input['phone'])?$input['phone']:'',
                'uncode'           => isset($input['uncode'])?$input['uncode']:'',
                'ungaddress'       => $ungaddress,
//                'regtype'          => $regtype,
                'account'          => "",
                'password'         => $input['password'],
                'pay_paasword'     => "",
                'describe'         => 'system.describe',
                'birthday'         => date('Y-m-d'),
                'now_integral'     => $integral,
                'history_integral' => $integral,
                'balance'          => 10,
                'whatsapp'         => isset($input['whatsApp']) ? $input['whatsApp'] : '',
                'login_ip'         => $this->request->ip(),
                'login_count'      => 1,
                'login_time'       => $date,
                'update_time'      => $date,
                'create_time'      => $date,
                'status'           => 1,
                'hide'             => 1,
                'invite_one_uid'   => $invite_one_uid,
                'invite_two_uid'   => $invite_two_uid,
                'invite_three_uid' => $invite_three_uid
            ]);

			
			// $hashids = new Hashids();
			$hashids = new Hashids(env('hashids'), 8,env('hashids_write'));
			$game_account=$hashids->encode($registerInfo->id);
			$apigame=new ApiGame();
			$result=$apigame->create_user($game_account,$input['password']);
			$ret  = json_decode($result,true);
            $userInfo['game_account']=$game_account;
            $userInfo['id']=$registerInfo->id;
            $ungewm = \create_qrcode($ungaddress,$userInfo);
            UserModel::where('id',$registerInfo->id)->update(['QR_code'=>$ungewm]);
            // 创建ung账户
            $unguser['uid'] = $registerInfo->id;
            $unguser['num'] = 10;
            $unguser['update_time'] =time();
            $unguser['add_time'] =time();
            Db::name('ung_user')->insert($unguser);
			//测试强制等于0
			// $ret['status']=0;
			if($ret['status']==0){
				UserModel::where('id',$registerInfo->id)->update(['game_account'=>$game_account,'nickname'=>$input['nickname']?$input['nickname']:$game_account]);
				// 给直接邀请人添加注册奖励
			}
            if($input['invitation_code']){
                $amount=2;
                //更新一级邀请人数
                $one_count = UserModel::where("id",$invite_one_uid)->count();
                UserModel::where('id',$invite_one_uid)->inc('balance',$amount)->update(['invite_one_num'=>$one_count]);
                //更新二级邀请人数
                if($invite_two_uid){
                    $two_count = UserModel::where("id",$invite_two_uid)->count();
                    UserModel::where("id",$invite_two_uid)->update(["invite_two_num"=>$two_count]);
                }
                //更新三级邀请人数
                if($invite_three_uid){
                    $three_count = UserModel::where("id",$invite_three_uid)->count();
                    UserModel::where("id",$invite_three_uid)->update(["invite_three_num"=>$three_count]);
                }
                //添加资金列表
                $userbalance=$invite_one_list->balance;
                $content='{user.inviteusers} '.$registerInfo->nickname.' {user.inviteregister} $'.$amount.' reward';
                $admin_content='用户'.$invite_one_list->nickname.'邀请用户'.$registerInfo->nickname.'注册获得'.$amount.'美金';
                capital_flow($invite_one_uid,$registerInfo->id,5,1,$amount,$userbalance,$content,$admin_content);
            }
			// $id=$hashids->decode($game_account);
            // 绑定事件
            event('RegisterEnd', $registerInfo);
            // 注册完执行登录操作
            // 生成令牌
            $password=UserModel::where('id',$registerInfo->id)->value('password');
            $token = password_hash($registerInfo->id . $this->request->ip() . $password, PASSWORD_BCRYPT, ['cost' => 12]);
            UserToken::create([
                'user_id'     => $registerInfo->id,
                'token'       => $token,
                'create_time' => date('Y-m-d H:i:s')
            ]);
            $this->success(lang('user.registersuccess'),['token'=>$token,'userInfo'=>$registerInfo]);
            // return json(['status' => 'success', 'message' => '注册成功']);
        }
    }
    
    /**
     * 手机注册
     */
    public function registerMobile()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('registerMobile')->check($input);
            } catch ( ValidateException $e ) {
                return json(['status' => 'error', 'message' => $e->getError()]);
            }
            if (! password_verify($input['code'].'index_register_mobile_code'.$input['mobile'].$input['salt'].$this->request->ip(), $input['rcode'])) {
                return json(["status" => "error", "message" => '手机验证码错误']);
            }
            if (UserModel::where('mobile', $input['mobile'])->value('id')) {
                return json(['status' => 'error', 'message' => '手机号已被注册']);
            }
            $group = UserGroup::where('default',1)->find();
            $group_id = 0;
            $integral = 0;
            if ($group) {
                $group_id = $group['id'];
                $integral = $group['integral'];
            }
            $date = date('Y-m-d H:i:s');
            $registerInfo = UserModel::create([
                'group_id'         => $group_id,
                'nickname'         => '未命名',
                'cover'            => '/upload/avatar.jpg',
                'sex'              => 0,
                'email'            => "",
                'mobile'           => $input['mobile'],
                'account'          => "",
                'password'         => $input['password'],
                'pay_paasword'     => "",
                'describe'         => "这个人很懒，什么都没有留下。",
                'birthday'         => date('Y-m-d'),
                'now_integral'     => $integral,
                'history_integral' => $integral,
                'balance'          => 0,
                'login_ip'         => '',
                'login_count'      => 0,
                'login_time'       => $date,
                'update_time'      => $date,
                'create_time'      => $date,
                'status'           => 1,
                'hide'             => 1,
            ]);
            // 绑定事件
            event('RegisterEnd', $registerInfo);
            return json(['status' => 'success', 'message' => '注册成功']);
        }
    }
    // 生成假地址
   function rand($len)
    {
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $string=time();
        for(;$len>=1;$len--)
        {
            $position=rand()%strlen($chars);
            $position2=rand()%strlen($string);
            $string=substr_replace($string,substr($chars,$position,1),$position2,0);
        }
        return $string;
    }
    /**
     * 发送注册邮箱验证码
     */
    public function sendRegisterEmailCode()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('codeEmail')->check($input);
            } catch ( ValidateException $e ) {
                $this->error($e->getError());
                // return json(['status' => 'error', 'message' => $e->getError()]);
            }
            if (UserModel::where('email', $input['email'])->value('id')) {
                $this->error(lang('user.emailoccupy'),['status'=>'error']);
                // return json(['status' => 'error', 'message' => '邮箱号已被注册']);
            }
            $result = sendCode::email($input['email'], 'index_register_email_code',lang('user.userregister'));
            $this->success('',$result);
            // return json($result);
        }
    }

    /**
     * 发送注册短信验证码
     */
    public function sendRegisterMobileCode()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('codeMobile')->check($input);
            } catch ( ValidateException $e ) {
                return json(['status' => 'error', 'message' => $e->getError()]);
            }
            if (UserModel::where('mobile', $input['mobile'])->value('id')) {
                return json(['status' => 'error', 'message' => '手机号已被注册']);
            }
            $result = sendCode::sms($input['mobile'], 'index_register_mobile_code', '26BEKytK3bCe');
            return json($result);
        }
    }

    /**
     * 发送修改密码邮箱验证码
     */
    public function sendPasswordEmailCode()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('codeEmail')->check($input);
            } catch ( ValidateException $e ) {
                $this->error($e->getError());
                // return json(['status' => 'error', 'message' => $e->getError()]);
            }
            if (UserModel::where('email', $input['email'])->value('id')) {
                $result = sendCode::email($input['email'], 'index_password_email_code', lang('user.forgot'));
                $this->success('',$result);
                // return json($result);
            } else {
                $this->error(lang('user.unregistered'));
                // return json(['status' => 'error', 'message' => '邮箱号未注册']);
            }
        }
    }

    /**
     * 发送修改密码短信验证码
     */
    public function sendPasswordMobileCode()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('codeMobile')->check($input);
            } catch ( ValidateException $e ) {
                return json(['status' => 'error', 'message' => $e->getError()]);
            }
            if (UserModel::where('mobile', $input['mobile'])->value('id')) {
                $result = sendCode::sms($input['mobile'], 'index_password_mobile_code', '26BEKytK3bCe');
                return json($result);
            } else {
                return json(['status' => 'error', 'message' => '手机号未注册']);
            }
        }
    }
}