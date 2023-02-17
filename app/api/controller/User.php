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

use app\api\model\GameBetLog;
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
use Hashids\Hashids;
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
			// try {
			//     $input = input('post.');
			//     validate(UserValidate::class)->scene('set')->check($input);
			// } catch ( ValidateException $e ) {
			// 	$this->error($e->getError());
			// }
            // $this->request->userInfo->nickname = $input['nickname'];
			// $this->request->userInfo->sex      = $input['sex'];
			if($input['mobile']){
				$this->request->userInfo->mobile   = $input['mobile'];
			}
			if($input['messenger']){
				$this->request->userInfo->messenger= $input['messenger'];
			}
			if($input['whatsapp']){
				$this->request->userInfo->whatsapp= $input['whatsapp'];
			}
			// if($input['telegram']){
			// 	$this->request->userInfo->telegram= $input['telegram'];
			// }
			// if($input['line']){
			// 	$this->request->userInfo->line= $input['line'];
			// }
            $this->request->userInfo->save();
			$userInfo=$this->request->userInfo;
			$data=UserModel::where('id',$userInfo->id)->find();
			$amount=1;
			if(!$data->mobile){
				$content='{user.addmobile}'.$amount.'{capital.money}';
				$admin_content='用户'.$userInfo->nickname.'添加手机号码资金增加'.$amount.'美元';
				UserModel::where('id',$userInfo->id)->inc('balance')->update();
				capital_flow($userInfo->id,$userInfo->id,7,1,$amount,$userInfo->balance,$content,$admin_content);
			}
			if(!$data->messenger){
				$content='{user.addmessenger}'.$amount.'{capital.money}';
				$admin_content='用户'.$userInfo->nickname.'添加Facebook资金增加'.$amount.'美元';
				UserModel::where('id',$userInfo->id)->inc('balance')->update();
				capital_flow($userInfo->id,$userInfo->id,7,1,$amount,$userInfo->balance,$content,$admin_content);
			}
			if(!$data->whatsapp){
				$content='{user.addwhatsapp}'.$amount.'{capital.money}';
				$admin_content='用户'.$userInfo->nickname.'添加whatsapp资金增加'.$amount.'美元';
				UserModel::where('id',$userInfo->id)->inc('balance')->update();
				capital_flow($userInfo->id,$userInfo->id,7,1,$amount,$userInfo->balance,$content,$admin_content);
			}
			
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
	        // $this->request->userInfo->cover    = str_replace(request()->domain(), '', $input['cover']);
	        $this->request->userInfo->cover    = $input['cover'];
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
            $field    = 'id,group_id,nickname,sex,email,mobile,cover,describe,birthday,history_integral,hide,invite_num';
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
			// $this->request->userInfo['cover']=$this->host.$this->request->userInfo['cover'];
			// 今天的利润'uid'=>$userInfo->id,'type'=>3,'money_type'=>1
			$this->request->userInfo['profit']=round($this->request->userInfo['balance']+$this->request->userInfo['UNG'],2);
			if($type=='team'){
				$uid=$this->request->userInfo['id'];
				$invitation_code=$this->request->userInfo['invitation_code'];
				if(!$invitation_code){
					$hashids = new Hashids(env('hashids'), 6,env('hashids_write_other'));
					$data['invitation_code']=$hashids->encode($uid);
					UserModel::where('id',$uid)->update($data);
				}else{
					$data['invitation_code']=$invitation_code;
				}
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
						$mail[$k]['usercount']=UserModel::where('mobile','like',$v['phoneNumber'])->count();
					}
					$data['mail_list']=$mail;
				}
				//计算提现减免的手续费用
				$one_money_all=0;
				$two_money_all=0;
				$three_money_all=0;
				foreach($data['invite_one'] as $k=>$v){
					$one_money=OrderModel::where(['uid'=>$v->id,'status'=>1])->sum('money');
					$v->amount=$one_money;
					$v->rewards=$one_money*(10/100);
					$one_money_all=+$one_money;
					$v->add_time=date('Y-m-d',strtotime($v->create_time));
				}
				foreach($data['invite_two'] as $k=>$v){
					$two_money=OrderModel::where(['uid'=>$v->id,'status'=>1])->sum('money');
					$v->amount=$two_money;
					$v->rewards=$two_money*(5/100);
					$two_money_all=+$two_money;
					$v->add_time=date('Y-m-d',strtotime($v->create_time));
				}
				foreach($data['invite_three'] as $k=>$v){
					$three_money=OrderModel::where(['uid'=>$v->id,'status'=>1])->sum('money');
					$v->amount=$three_money;
					$v->rewards=$three_money*(3/100);
					$three_money_all=+$three_money;
					$v->add_time=date('Y-m-d',strtotime($v->create_time));
				}
				//查询邀请人充值的金额
				$data['money']=$one_money_all+$two_money_all+$three_money_all;
				$data['reward']=$one_money_all*(10/100)+$two_money_all*(5/100)+$three_money_all*(3/100);
				$shareurl="https://m.unicgm.com/pages/center/register?invitationcode=".$this->request->userInfo['game_account'];
				//查询分享文案
				$share_content=ShareSetModel::where(['type'=>$type,'language'=>$this->lang])->value('content');
				$data['share_content']=$share_content;
				$data['share_content_app']=str_replace(['<p>','</p>'],'',$share_content);
				// $data['share_content']=str_replace(['{{nickname}}','{{href}}','{{QR_code}}'],[$this->request->userInfo['nickname'],$shareurl,request()->domain().$this->request->userInfo['QR_code']],$share_content);
				// $data['share_content_app']=str_replace(['<p>','</p>','{{nickname}}','{{href}}','{{QR_code}}'],['','',$this->request->userInfo['nickname'],$shareurl,''],$share_content);
				$this->success(lang('success'),$data);
			}else{
				$this->request->userInfo['safetyall']=100;
				$safetyindex=0;
				if($this->request->userInfo['email']){
					$safetyindex=$safetyindex+20;
//					$this->request->userInfo['safetytext']=lang('user.safetylow');
				}
				if($this->request->userInfo['mobile']){
					$safetyindex=$safetyindex+20;
//					$this->request->userInfo['safetytext']=lang('user.safetylow');
				}
				if($this->request->userInfo['pay_paasword']==1){
					$safetyindex=$safetyindex+20;
//					$this->request->userInfo['safetytext']=lang('user.safetycommonly');
				}
				if($this->request->userInfo['whatsapp']){
					$safetyindex=$safetyindex+20;
//					$this->request->userInfo['safetytext']=lang('user.safetyhigh');
				}
				if($this->request->userInfo['messenger']){
					$safetyindex=$safetyindex+20;
//					$this->request->userInfo['safetytext']=lang('user.safetyperfect');
				}
				if($safetyindex <= 40)
				{
                    $this->request->userInfo['safetytext']=lang('user.safetylow');
                }else if($safetyindex <= 60)
                {
                    $this->request->userInfo['safetytext']=lang('user.safetycommonly');
                }else if($safetyindex <=80){
                    $this->request->userInfo['safetytext']=lang('user.safetyhigh');
                }else{
                    $this->request->userInfo['safetytext']=lang('user.safetyperfect');
                }
				$this->request->userInfo['safetyindex']=$safetyindex;
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
	/* 
	 * 查看用户的敏感密码信息
	 * 
	 */
	public function seeuserpassword(){
		$type=input('post.type');
		$userInfo=$this->request->userInfo;
		$data=UserModel::where('id',$userInfo->id)->value($type);
		$this->success(lang('system.operation_succeeded'),$data);
	}
	/**
	 * 获取钱包的信息
	 */
	public function getwallet(){
		$userInfo=$this->request->userInfo;
		if($userInfo){
			// 用户余额
			$data['balance']=$userInfo->balance;
			// 今天的游戏赢得
			$gamewind=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>3,'money_type'=>1])->whereDay('add_time')->sum('amount');
			//今日游戏输的
			$gamefail=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>3,'money_type'=>2])->whereDay('add_time')->sum('amount');
			$profit = round(GameBetLog::where(['user_id'=>$userInfo->id])->whereDay('betTime')->sum('netPnl'),2);
            $data['profit']=$profit > 0 ? $profit : 0 ;
			// 今日流水
			$data['water']=round(GameBetLog::where(['user_id'=>$userInfo->id])->whereDay('betTime')->sum('betAmount'),2);
			// echo CapitalFlowmodel::getLastSql();exit;
			// 今日股息
			$data['dividend']=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>4,'money_type'=>1])->whereDay('add_time')->sum('amount');
			// 总获得的股息
			$data['dividends']=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>4,'money_type'=>1])->sum('amount');
			// 资金的详细列表
			$data['list']=CapitalFlowmodel::where([
				['uid','=',$userInfo->id],
				['money_type','<>',0]
			])->page(1)->limit(10)->order('id desc')->select();
			foreach($data['list'] as $k=>$v){
				$v->add_times=date('Y-m-d',$v->add_time);
				$v->content=getlang($v->content);
			}
			$this->success(lang('system.operation_succeeded'),$data);
		}
	}
	// 	获取资金明细
    public function getwalletinfo(){
        $userInfo=$this->request->userInfo;
        $type = input('post.type');
        $pageNum=input('pageNum/d');
		$pageSize=input('pageSize/d');
        if($type==0){
            $where =[['uid','=',$userInfo->id],
			['money_type','<>',0]];
			$gamelog_count = CapitalFlowmodel::where($where)->order('id desc')->count();
			
        }else if($type==6){
            $where =[['uid','=',$userInfo->id],
			['money_type','<>',0],
			['type','in',"6,7"]];
			$gamelog_count = CapitalFlowmodel::where($where)->order('id desc')->count();
        }else{
            $where =[['uid','=',$userInfo->id],
			['money_type','<>',0],
			['type','=',$type]];
			$gamelog_count = CapitalFlowmodel::where($where)->order('id desc')->count();
        }
        $data['totalSize']=$gamelog_count;
		$data['totalPage']=ceil($gamelog_count/$pageSize);
    	$data['list']=CapitalFlowmodel::where($where)->page($pageNum)->limit($pageSize)->order('id desc')->select();
		foreach($data['list'] as $k=>$v){
			$v->add_times=date('Y-m-d',$v->add_time);
			$v->content=getlang($v->content);
		}
		$this->success(lang('system.operation_succeeded'),$data);
    }
	public function searchwallet()
    {
        
    }
}
