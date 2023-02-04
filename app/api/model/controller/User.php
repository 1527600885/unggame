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

use think\File as Fileupload;
use think\facade\Filesystem;
use think\exception\ValidateException;
use app\api\BaseController;
use app\api\addons\sendCode;
use app\api\model\User as UserModel;
use app\api\validate\User as UserValidate;
use app\api\model\MailList as MailListModel;
use app\api\model\Order as OrderModel;
use app\api\model\ShareSet as ShareSetModel;
use app\api\model\CapitalFlow as CapitalFlowmodel;
/**
 * 个人中心模块
 */
class User  extends BaseController
{
    /**
     * 用户鉴权
     */
    // protected $middleware = [\app\api\middleware\AuthCheck::class];
    /**
     * 修改个人资料
     */
    public function set()
    {
        if ($this->request->isPost()) {
            $input = input('post.');
			try {
			    $input = input('post.');
			    validate(UserValidate::class)->scene('set')->check($input);
			} catch ( ValidateException $e ) {
				$this->error($e->getError());
			}
            $this->request->userInfo->nickname = $input['nickname'];
			$this->request->userInfo->sex      = $input['sex'];
			if(strpos($input['mobile'],'*') == false){
				$this->request->userInfo->mobile   = $input['mobile'];
			}
			if($input['messenger']){
				$this->request->userInfo->messenger= $input['messenger'];
			}
			if($input['whatsapp']){
				$this->request->userInfo->whatsapp= $input['whatsapp'];
			}
			if($input['telegram']){
				$this->request->userInfo->telegram= $input['telegram'];
			}
			if($input['line']){
				$this->request->userInfo->line= $input['line'];
			}
            $this->request->userInfo->save();
			$this->success(lang('system.setting_succeeded'));
            // return json(['status' => 'success','message' => '设置成功']);
        }
    }
	
	/**
	 * 修改头像
	 */
	public function set_image()
	{
	    if ($this->request->isPost()) {
	        $input = input('post.');
	        $this->request->userInfo->cover    = str_replace(request()->domain(), '', $input['cover']);
	        $this->request->userInfo->save();
			$this->success(lang('system.setting_succeeded'));
	        // return json(['status' => 'success','message' => '设置成功']);
	    }
	}
	/**
	 * 设置支付密码
	 */
	public function set_pay_paasword(){
		if($this->request->isPost()){
			try {
			    $input = input('post.');
			    validate(UserValidate::class)->scene('set_pay_paasword')->check($input);
			} catch ( ValidateException $e ) {
				$this->error($e->getError());
			}
			$this->request->userInfo->pay_paasword    = $input['pay_paasword'];
			$this->request->userInfo->save();
			$this->success(lang('system.setting_succeeded'));
		}
	}
    
    /**
     * 用户资料(别人的)
     */
    public function info()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('info')->check($input);
            } catch ( ValidateException $e ) {
				$this->error($e->getError());
                // return json(['status' => 'error', 'message' => $e->getError()]);
            }
            $field    = 'id,group_id,nickname,sex,email,mobile,cover,describe,birthday,history_integral,hide';
            $userInfo = UserModel::with(['group'])->where('id', $input['id'])->field($field)->find();
            if ($userInfo) {
				$this->success(lang('success'),$userInfo);
                // return json(['status' => 'success', 'message' => '获取成功', 'data' => $userInfo]);
            } else {
				$this->error(lang('user.notRegister'));
                // return json(['status' => 'error', 'message' => '该用户暂未注册']);
            }
        }
    }
	
	/**
	 * 用户资料(自己的)
	 */
	public function userinfo(){
		if($this->request->userInfo){
			$type=input("post.type");
			$this->request->userInfo['cover']=$this->host.$this->request->userInfo['cover'];
			// 今天的利润'uid'=>$userInfo->id,'type'=>3,'money_type'=>1
			$this->request->userInfo['profit']=CapitalFlowmodel::whereOr([
				[
					['uid','=',$this->request->userInfo['id']],
					['type','=',1],
					['money_type','=',1]
				],
				[
					['uid','=',$this->request->userInfo['id']],
					['type','=',3],
					['money_type','=',1]
				],
				[
					['uid','=',$this->request->userInfo['id']],
					['type','=',4],
					['money_type','=',1]
				],
				[
					['uid','=',$this->request->userInfo['id']],
					['type','=',5],
					['money_type','=',1]
				]
				])->sum('amount');
			if($type=='team'){
				$uid=$this->request->userInfo['id'];
				$data['userInfo']=$this->request->userInfo;
				//一级邀请的人
				$data['invite_one']=UserModel::where('invite_one_uid',$uid)->select();
				//二级邀请的人
				$data['invite_two']=UserModel::where('invite_two_uid',$uid)->select();
				//三级邀请的人
				$data['invite_three']=UserModel::where('invite_three_uid',$uid)->select();
				//读取用户的通讯录
				$mail_list=MailListModel::where('uid',$uid)->find();
				$data['mail_list']=[];
				if($mail_list){
					$mail=json_decode($mail_list->mail,true);
					foreach($mail as $k=>$v){
						$mail[$k]['firstName']=mb_substr($v['displayName'],0,1);
						$mail[$k]['phoneNumber']='*****'.mb_substr($v['phoneNumber'],-4);
					}
					$data['mail_list']=$mail;
				}
				//计算提现减免的手续费用
				$invite_one_id=null;
				foreach($data['invite_one'] as $k=>$v){
					if(count($data['invite_one'])==$k+1){
						$invite_one_id=$v->id;
					}else{
						$invite_one_id=$v->id.",";
					}
				}
				//查询我的直接邀请人充值的金额
				$data['money']=OrderModel::whereIn('uid',(string)$invite_one_id)->where('status',1)->sum('money');
				
				if(count($data['invite_one'])<3){
					$data['service_charge']=0;
				}elseif(count($data['invite_one'])>=3 && count($data['invite_one'])<5){
					$data['service_charge']=3;
				}elseif(count($data['invite_one'])>=5 && count($data['invite_one'])<10){
					$data['service_charge']=5;
				}else{
					$data['service_charge']=10;
				}
				$shareurl="https://m.unicgm.com/pages/center/register?invitationcode=".$this->request->userInfo['game_account'];
				if($this->request->userInfo['QR_code']==null){
					$result=create_qrcode($shareurl,$this->request->userInfo);
					$this->request->userInfo['QR_code']=$result;
				}
				$this->request->userInfo['QR_code']=request()->domain().$this->request->userInfo['QR_code'];
				//查询分享文案
				$share_content=ShareSetModel::where(['type'=>$type,'language'=>$this->lang])->value('content');
				$data['share_content']=$share_content;
				$data['share_content_app']=str_replace(['<p>','</p>'],'',$share_content);
				// $data['share_content']=str_replace(['{{nickname}}','{{href}}','{{QR_code}}'],[$this->request->userInfo['nickname'],$shareurl,request()->domain().$this->request->userInfo['QR_code']],$share_content);
				// $data['share_content_app']=str_replace(['<p>','</p>','{{nickname}}','{{href}}','{{QR_code}}'],['','',$this->request->userInfo['nickname'],$shareurl,''],$share_content);
				$this->success(lang('success'),$data);
			}else{
				$this->success(lang('success'),$this->request->userInfo);
			}
		}
	}
	
	
	/**
	 * 保存用户的通讯录
	 */
	public function save_mail(){
		if($this->request->userInfo){
			$uid=$this->request->userInfo['id'];
			$mail=input('post.maillist');
			$mailcont=MailListModel::where('uid',$uid)->count();
			if($mailcont>0){
				$data=['mail'=>json_encode($mail),'update_time'=>time()];
				MailListModel::where('uid',$uid)->update($data);
			}else{
				$data=['uid'=>$uid,'mail'=>json_encode($mail),'update_time'=>time(),'add_time'=>time()];
				MailListModel::insert($data);
			}
			$mail_list=MailListModel::where('uid',$uid)->find();
			$data['mail_list']=[];
			if($mail_list){
				$mail=json_decode($mail_list->mail,true);
				foreach($mail as $k=>$v){
					$mail[$k]['firstName']=mb_substr($v['displayName'],0,1);
					$mail[$k]['phoneNumber']='*****'.mb_substr($v['phoneNumber'],-4);
				}
				$data['mail_list']=$mail;
			}
			$this->success(lang('success'),$data);
		}
	}

    /**
     * 上传图片
     */
    public function upload()
    {
        try {
            $file = $this->request->file('file');
            validate([ 'file' => [ 'fileExt'=>config('upload.ext')['image'], 'fileSize' =>config('upload.size')['image'] ]])->check(['file' => $file]);
            $fileInfo = pathinfo($file);
			$extension = strtolower($file->getOriginalExtension());
            $filePath = $fileInfo['dirname'] . '/' . $fileInfo['basename'];
            $files = new Fileupload($filePath);
            $savePath = public_path() . 'upload/user/'.date('Ymd').'/';
            $fileName = $files->hash() . '.' . $extension;
            $files->move($savePath,$savePath.$fileName);
			// $url = Filesystem::putFile('user', $file); //Tp6.0上传方法
			$url='user/'.date('Ymd').'/'.$fileName;
            $url = '/upload/' . str_replace('\\', '/', $url);
			$this->success(lang('system.success'),$url);
            // return json(['status' => 'success', 'message' => '上传成功', 'data' => $url]);
        } catch (ValidateException $e) {
			$this->error($e->getError());
            // return json(['status' => 'error', 'message' => $e->getError()]);
        }
    }
    
    /**
     * 绑定邮箱
     */
    public function bindEmail()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('bindEmail')->check($input);
            } catch ( ValidateException $e ) {
				$this->error($e->getError());
                // return json(['status' => 'error', 'message' => $e->getError()]);
            }
			$oldemail = UserModel::where('id', $this->request->userInfo['id'])->value('email');
			
            if (! password_verify($input['code'].'index_bind_email_code'.$oldemail.$input['salt'].request()->ip(), $input['rcode'])) {
                $this->error(lang('user.codeerror'));
				// return json(["status" => "error", "message" => '验证码不正确']);
            }
            $email = UserModel::where('email', $input['email'])->value('id');
            if ($email) {
				$this->error(lang('user.emailerror'));
                // return json(['status' => 'error', 'message' => '此邮箱号已被注册']);
            }
            $this->request->userInfo->email       = $input['email'];
            $this->request->userInfo->update_time = date('Y-m-d H:i:s');
            $this->request->userInfo->save();
			$this->success(lang('user.mobilesuccess'));
            // return json(['status' => 'success', 'message' => '绑定成功']);
        }
    }

    /**
     * 绑定手机
     */
    public function bindMobile()
    {
        if ($this->request->isPost()) {
   //          try {
   //              $input = input('post.');
   //              validate(UserValidate::class)->scene('bindMobile')->check($input);
   //          } catch ( ValidateException $e ) {
			// 	$this->error($e->getError());
			// }
			$input = input('post.');
			if(!$input['mobile']){
				$this->error($e->getError());
			}
			
            // if (! password_verify($input['code'].'index_bind_mobile_code'.$input['mobile'].$input['salt'].request()->ip(), $input['rcode'])) {
            //     return json(["status" => "error", "message" => '验证码不正确']);
            // }
            $mobile = UserModel::where('mobile', $input['mobile'])->value('id');
            if ($mobile) {
				$this->error(lang('user.mobileexistence'));
                // return json(['status' => 'error', 'message' => '此手机号已被注册']);
            }
            $this->request->userInfo->mobile      = $input['mobile'];
            $this->request->userInfo->update_time = date('Y-m-d H:i:s');
            $this->request->userInfo->save();
			$this->success(lang('user.mobilesuccess'));
            // return json(['status' => 'success', 'message' => '绑定成功']);
        }
    }

    /**
     * 发送绑定邮箱验证码
     */
    public function sendBindEmailCode()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('codeEmail')->check($input);
            } catch ( ValidateException $e ) {
				$this->error($e->getError());
                // return json(['status' => 'error', 'message' => $e->getError()]);
            }
			$email=UserModel::where('id', $this->request->userInfo['id'])->value('email');
			$input['email']=$email;
    //         if (UserModel::where('email', $input['email'])->value('id')) {
				// $this->error(lang('user.emailerror'));
    //             return json(['status' => 'error', 'message' => '此邮箱号已被注册']);
    //         }
            $result = sendCode::email($input['email'], 'index_bind_email_code', lang('user.bindemail'));
            $this->success('',$result);
			// return json($result);
        }
    }

    /**
     * 发送绑定手机验证码
     */
    public function sendBindMobileCode()
    {
        if ($this->request->isPost()) {
            try {
                $input = input('post.');
                validate(UserValidate::class)->scene('codeMobile')->check($input);
            } catch ( ValidateException $e ) {
                return json(['status' => 'error', 'message' => $e->getError()]);
            }
            if (UserModel::where('mobile', $input['mobile'])->value('id')) {
                return json(['status' => 'error', 'message' => '此手机号已被注册']);
            }
            $result = sendCode::sms($input['mobile'], 'index_bind_mobile_code', '26BEKytK3bCe');
            return json($result);
        }
    }
	/**
	 * 发送修改登录密码邮箱验证码（已登录的情况）
	 */
	public function sendUpdatePasswordCode()
	{
	    if ($this->request->isPost()) {
			$email=UserModel::where('id', $this->request->userInfo['id'])->value('email');
			$input['email']=$email;
	        $result = sendCode::email($input['email'], 'index_password_email_code', lang('user.bindemail'));
	        $this->success('',$result);
			// return json($result);
	    }
	}
	/**
	 * 修改登录密码（已登录的情况）
	 */
	public function passwordEmail(){
		if ($this->request->isPost()) {
		    try {
		        $input = input('post.');
		        validate(UserValidate::class)->scene('passwordEmail')->check($input);
		    } catch ( ValidateException $e ) {
				$this->error($e->getError());
		        // return json(['status' => 'error', 'message' => $e->getError()]);
		    }
			$input['email']=$oldemail = UserModel::where('id', $this->request->userInfo['id'])->value('email');
		    if (! password_verify($input['code'].'index_password_email_code'.$input['email'].$input['salt'].$this->request->ip(), $input['rcode'])) {
		        $this->error(lang('user.captchaError'));
				// return json(["status" => "error", "message" => lang('user.captchaError')]);
		    }
		    $save = UserModel::where('email', $input['email'])->find();
		    if (! $save) {
				$this->error(lang('user.accountnot'));
		        // return json(['status' => 'error', 'message' => lang('user.accountnot')]);
		    }
		    $save->password = $input['password'];
			$save->update_time = date('Y-m-d H:i:s');
		    $save->save();
			$this->success(lang('system.operation_succeeded'));
		    // return json(['status' => 'success', 'message' => lang('system.operation_succeeded')]);
		}
	}
	/**
	 * 发送修改支付密码邮箱验证码（已登录的情况）
	 */
	public function sendPayPasswordCode()
	{
	    if ($this->request->isPost()) {
			$email=UserModel::where('id', $this->request->userInfo['id'])->value('email');
			$input['email']=$email;
	        $result = sendCode::email($input['email'], 'index_pay_password_email_code', lang('user.bindemail'));
	        $this->success('',$result);
			// return json($result);
	    }
	}
	/**
	 * 修改支付密码（已登录的情况）
	 */
	public function pay_passwordEmail(){
		if ($this->request->isPost()) {
		    try {
		        $input = input('post.');
		        validate(UserValidate::class)->scene('set_pay_paasword')->check($input);
		    } catch ( ValidateException $e ) {
				$this->error($e->getError());
		        // return json(['status' => 'error', 'message' => $e->getError()]);
		    }
			$input['email']=$oldemail = UserModel::where('id', $this->request->userInfo['id'])->value('email');
		    if (! password_verify($input['code'].'index_pay_password_email_code'.$input['email'].$input['salt'].$this->request->ip(), $input['rcode'])) {
		        $this->error(lang('user.captchaError'));
				// return json(["status" => "error", "message" => lang('user.captchaError')]);
		    }
		    $save = UserModel::where('email', $input['email'])->find();
		    if (! $save) {
				$this->error(lang('user.accountnot'));
		        // return json(['status' => 'error', 'message' => lang('user.accountnot')]);
		    }
		    $save->pay_paasword = $input['pay_paasword'];
			$save->update_time = date('Y-m-d H:i:s');
		    $save->save();
			$this->success(lang('system.operation_succeeded'));
		    // return json(['status' => 'success', 'message' => lang('system.operation_succeeded')]);
		}
	}
	/**
	 * 获取钱包的信息
	 */
	public function getwallet(){
		$userInfo=$this->request->userInfo;
		if($userInfo){
			// 用户余额
			$data['balance']=$userInfo->balance;
			// 今天的利润
			$data['profit']=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>3,'money_type'=>1])->sum('amount');
			// 今日流水
			$data['water']=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>3])->whereDay('add_time')->sum('amount');
			// 今日股息
			$data['dividend']=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>4,'money_type'=>1])->whereDay('add_time')->sum('amount');
			// 总获得的股息
			$data['dividends']=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>4,'money_type'=>1])->sum('amount');
			// 充值和提现的详细
			$data['list']=CapitalFlowmodel::whereOr([
				[
					['uid','=',$userInfo->id],
					['type','=',1]
				],
				[
					['uid','=',$userInfo->id],
					['type','=',2]
				]
			])->page(1)->limit(10)->select();
			foreach($data['list'] as $k=>$v){
				$v->add_times=date('Y-m-d',$v->add_time);
			}
			$this->success(lang('system.operation_succeeded'),$data);
		}
	}
}
