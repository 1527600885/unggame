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

use app\admin\model\Config as ConfigModel;
use app\api\model\GameBetLog;
use app\api\model\UngSet;
use app\api\model\UngUser;
use app\api\model\v2\UserIdcard;
use app\api\validate\IdCard;
use think\facade\Validate;
use think\File as Fileupload;
use think\facade\Filesystem;
use think\exception\ValidateException;
use app\api\BaseController;
use app\api\addons\sendCode;
use app\api\model\User as UserModel;
use app\api\validate\User as UserValidate;
use app\api\model\MailList as MailListModel;
use app\api\model\Order as OrderModel;
use app\api\model\CurrencyAll;
use app\api\model\ShareSet as ShareSetModel;
use app\api\model\CapitalFlow as CapitalFlowmodel;
use Hashids\Hashids;
use think\facade\Db;
/**
 * 个人中心模块
 */
class User  extends BaseController
{
    protected $noNeedLogin = ['chatupload'];
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
			if(isset($input['mobile']) && !empty($input['mobile'])){
				$this->request->userInfo->mobile   = $input['mobile'];
			}
			if(isset($input['messenger']) && !empty($input['messenger'])){
				$this->request->userInfo->messenger= $input['messenger'];
			}
			if(isset($input['whatsapp']) && !empty($input['whatsapp'])){
				$this->request->userInfo->whatsapp= $input['whatsapp'];
			}
            if(isset($input["other_accounts"]) && !empty($input['other_accounts']))
            {
                $this->request->userInfo->other_accounts= $input['other_accounts'];
            }
            if(isset($input['country']) && !empty($input['country']))
            {
                $this->request->userInfo->country= $input['country'];
            }
			// if($input['telegram']){
			// 	$this->request->userInfo->telegram= $input['telegram'];
			// }
			// if($input['line']){
			// 	$this->request->userInfo->line= $input['line'];
			// }
            $user = $this->request->userInfo;
            $this->request->userInfo->save();
			$userInfo=$this->request->userInfo;
//			$amount=1;
//			if(!$user->mobile && $userInfo->mobile){
//				$content='{user.addmobile}'.$amount.'{capital.money}';
//				$admin_content='用户'.$userInfo->nickname.'添加手机号码资金增加'.$amount.'美元';
//				UserModel::where('id',$userInfo->id)->inc('balance')->update();
//				capital_flow($userInfo->id,$userInfo->id,7,1,$amount,$userInfo->balance,$content,$admin_content);
//			}
//			if(!$user->messenger && $userInfo->messenger){
//				$content='{user.addmessenger}'.$amount.'{capital.money}';
//				$admin_content='用户'.$userInfo->nickname.'添加Facebook资金增加'.$amount.'美元';
//				UserModel::where('id',$userInfo->id)->inc('balance')->update();
//				capital_flow($userInfo->id,$userInfo->id,7,1,$amount,$userInfo->balance,$content,$admin_content);
//			}
//			if(!$user->whatsapp && $userInfo->whatsapp){
//				$content='{user.addwhatsapp}'.$amount.'{capital.money}';
//				$admin_content='用户'.$userInfo->nickname.'添加whatsapp资金增加'.$amount.'美元';
//				UserModel::where('id',$userInfo->id)->inc('balance')->update();
//				capital_flow($userInfo->id,$userInfo->id,7,1,$amount,$userInfo->balance,$content,$admin_content);
//			}
			$data = $userInfo;
		    $data['pay_password'] = $userInfo['pay_password']==0?0:1;
		    $data['password'] = '';
			$this->success(lang('system.setting_succeeded'),$data);
            // return json(['status' => 'success','message' => '设置成功']);
        }
    }
    public function setcurrency(){
        $datas = input('post.');
        UserModel::where(['id'=>$this->request->userInfo->id])->update(['currency'=>$datas['cid']]);
        $result = CurrencyAll::where(['id'=>$datas['cid']])->select()->toArray();
        $data=array();
        $rateList = cacheRate();
        $data['balance'] = $this->request->userInfo->balance;
        // $country = getipcountry($this->request->ip());
        foreach ($result as $v) {
                if ($v['type'] == 2) {
                    $v['rate'] = $rateList[$v['name']];
                } else {
                    $v['rate'] = bcdiv('1', getCoinMarketCap('USD', $v['name']), 8);
                }
                $v['amount'] = bcmul($v['rate'], $data['balance'], 8);
                
                $data['symbol'] = $v['symbol'];
                $data['country_amount'] = $v['amount'];
                
                
            }
        $this->success(lang('success'),$result);
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
			$data = $this->request->userInfo->toArray();
		    $data['pay_password'] = $this->request->userInfo['pay_password']==0?0:1;
		    $data['password'] = '';
			$this->success(lang('system.setting_succeeded'),$data);
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
				$data = $this->request->userInfo->toArray();
		    $data['pay_password'] = $this->request->userInfo['pay_password']==0?0:1;
		    $data['password'] = '';
			$this->success(lang('system.setting_succeeded'),$data);
		}
	}
     // 获取用户区块链地址
    public function addressinfo()
    {
        if ($this->request->isPost()) {
            
            $field    = 'ungaddress';
            $userInfo = UserModel::where('id', input('code'))->field($field)->find();
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
					$v->Registreward = $v->is_check==1? 5 : 'Unverified';
					$v->rewards=$one_money*(10/100);
					$one_money_all=+$one_money;
					$v->add_time=date('Y-m-d',strtotime($v->create_time));
				}
				foreach($data['invite_two'] as $k=>$v){
					$two_money=OrderModel::where(['uid'=>$v->id,'status'=>1])->sum('money');
					$v->amount=$two_money;
					$v->rewards=$two_money*(5/100);
					$v->Registreward = $v->is_check==1? 5 : 'Unverified';
					$two_money_all=+$two_money;
					$v->add_time=date('Y-m-d',strtotime($v->create_time));
				}
				foreach($data['invite_three'] as $k=>$v){
					$three_money=OrderModel::where(['uid'=>$v->id,'status'=>1])->sum('money');
					$v->amount=$three_money;
					$v->rewards=$three_money*(3/100);
					$v->Registreward = $v->is_check==1? 5 : 'Unverified';
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
				$data['Registdollars'] = 5;
                $data['First_level_dollar'] = '10.00%';
                $data['Second_level_dollar'] = '5.00%';
                $data['Third_level_dollar'] = '3.00%';
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
				$ungdata = UngUser::where("uid",$this->request->userInfo['id'])->find();
				$this->request->userInfo['ung_num'] = $ungdata['num'] ?? 0;
				$this->request->userInfo['ledgenum'] = $ungdata['pledgenum'] ?? 0;
				$this->request->userInfo['interest'] = $ungdata['interest'] ?? 0;
				$this->request->userInfo['ung_rate'] = UngSet::value("interest");
                $this->request->userInfo['ung_price'] = bcmul($this->request->userInfo['ung_num'],UngSet::value('price'),3);
                $currency = CurrencyAll::where(['id'=>$this->request->userInfo['currency']])->find()->toArray();
                // var_dump($currency['type']);
                if($currency['type'] == 2){
                    $rateList = cacheRate();
                    $rate = $rateList[$currency['name']];
                }else{
                    $rate = bcdiv('1', convert_scientific_number_to_normal(strval(getCoinMarketCap('USD', $currency['name']))), 8);
                }
                $currency['rateamount'] = bcmul($rate, $this->request->userInfo['balance'], 8);
                $currency['shortenNumber'] =shortenNumber(floatval($currency['rateamount']));
                // var_dump($rate);
                
                $this->request->userInfo['currency'] = $currency;
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
    public function chatUpload()
    {
        $this->upload();
    }
    /**
     * 上传图片
     */
    public function upload()
    {
        try {
            require root_path() .'extend/Aws/aws-autoloader.php';
            $file = $this->request->file('file');
            validate([ 'file' => [ 'fileExt'=>config('upload.ext')['image'], 'fileSize' =>config('upload.size')['image'] ]])->check(['file' => $file]);
            $fileInfo = pathinfo($file);
			$extension = strtolower($file->getOriginalExtension());
            $filePath = $fileInfo['dirname'] . '/' . $fileInfo['basename'];
            $files = new Fileupload($filePath);
            $savePath = 'upload/user/'.date('Ymd').'/';
            $fileName = $files->hash() . '.' . $extension;
            $bucket = env('aws.bucket'); // 容器名称[调整填写自己的容器名称]
            $key = $_FILES['file']['tmp_name']; // 要上传的文件
            $region = env('aws.region');//地区
        // $endpoint = 'https://obs-hazz.cucloud.cn';//
            $ak = env('aws.ak');// ak
            $sk = env('aws.sk');// sk
            $s3 = new \Aws\S3\S3Client([
                'version' => 'latest',
                's3ForcePathStyle' => true,
                'region' => $region,
                // 'endpoint' => $endpoint,
                'credentials' => [
                    'key' => $ak,
                    'secret' => $sk,
                ],
            // 'scheme' => 'http',
            // 'debug' => true,
            ]);
           
            $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $savePath.$fileName,
                'Body'   => fopen($key,"r"),
            ]);
//             $file = $this->request->file('file');
//             validate([ 'file' => [ 'fileExt'=>config('upload.ext')['image'], 'fileSize' =>config('upload.size')['image'] ]])->check(['file' => $file]);
//             $fileInfo = pathinfo($file);
// 			$extension = strtolower($file->getOriginalExtension());
//             $filePath = $fileInfo['dirname'] . '/' . $fileInfo['basename'];
//             $files = new Fileupload($filePath);
//             $savePath = public_path() . 'upload/user/'.date('Ymd').'/';
//             $fileName = $files->hash() . '.' . $extension;
//             $files->move($savePath,$savePath.$fileName);
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

    public function bindEmail(){
        $input = input("post.");
        if(! $input['is_fill']  || empty($input['email']))
        {
            $input['email'] = UserModel::where('id', $this->request->userInfo['id'])->value('email');
            if(!$input['email']) $this->error(lang("user.emailEmpty"));
        }
        if (! password_verify($input['code'].'index_bind_email_code'.$input['email'].$input['salt'].request()->ip(), $input['rcode'])) {
            $this->error(lang('user.codeerror'));
        }
        $email = UserModel::where('email', $input['email'])->value('id');
        $input['type'] = $input['type'] ?? 0;
        if ($email && $input['type'] == 0) {
            $this->error(lang('user.emailerror'));
        }
        if($input['type'] != 0){
            $this->request->userInfo->email       = $input['email'];
            $this->request->userInfo->update_time = date('Y-m-d H:i:s');
            $this->request->userInfo->save();
        }

        $data = $this->request->userInfo->toArray();
        if(!$data['is_check_email']){
            $amount = 10;
            $content='{user.addemail}'.$amount.'{capital.money}';
            $admin_content='用户'.$this->request->userInfo->nickname.'添加校验邮箱资金增加'.$amount.'美元';
            UserModel::where('id',$this->request->userInfo->id)->inc('balance',$amount)->update();
            capital_flow($this->request->userInfo->id,$this->request->userInfo->id,7,1,$amount,$this->request->userInfo->balance,$content,$admin_content);
        }
        $data['pay_password'] = $this->request->userInfo['pay_password']==0?0:1;
        $data['password'] = '';
        $this->success("Operation successful.",$data);
    }
    public function bindMobile(){
        $input = input('post.');
        if(!$input['is_fill'] || empty($input['mobile']))
        {
            $mobile = UserModel::where('id', $this->request->userInfo['id'])->value('mobile');
            if(!$mobile) $this->error(lang("user.mobileEmpty"));
        }
        $code = cache($mobile);
        if($code != $input['code']) $this->error(lang("user.codeerror"));
        $usermobile = UserModel::where('mobile', $input['mobile'])->value('id');
        $input['type'] = $input['type'] ?? 0;
        if ($usermobile && $input['type'] == 0 ) {
            $this->error(lang('user.mobileexistence'));
        }
        if($input['type'] !=0){
            $this->request->userInfo->mobile = $input['mobile'];
            $this->request->userInfo->update_time = date('Y-m-d H:i:s');
            $this->request->userInfo->save();
        }
        $data = $this->request->userInfo->toArray();
        if(!$this->request->userInfo['is_check_mobile']){
            $amount = 10;
            $content='{user.addmobile}'.$amount.'{capital.money}';
            $admin_content='用户'.$this->request->userInfo->nickname.'添加校验手机资金增加'.$amount.'美元';
            UserModel::where('id',$this->request->userInfo->id)->inc('balance',$amount)->update();
            capital_flow($this->request->userInfo->id,$this->request->userInfo->id,7,1,$amount,$this->request->userInfo->balance,$content,$admin_content);
        }
        $data['pay_password'] = $this->request->userInfo['pay_password']==0?0:1;
        $data['password'] = '';
        $this->success(lang('user.mobilesuccess'),$data);
    }

    /**
     * 发送绑定邮箱验证码
     */
    public function sendBindEmailCode()
    {
        if ($this->request->isPost()) {
            $email = $this->request->post("email");
            if($email && !Validate::is($email,"email")){
                $this->error(lang("user.email"));
            }
            if($email){
                $email = UserModel::where("email",$email)->find();
                if($email){
                    $this->error(lang("user.emailoccupy"));
                }
            }
            $input['email']= $email ?:UserModel::where("id",$this->request->userInfo['id'])->value("email");
            $result = sendCode::email($input['email'], 'index_bind_email_code', lang('user.bindemail'));
            $this->success('success',$result);
			// return json($result);
        }
    }

    /**
     * 发送绑定手机验证码
     */
    public function sendBindMobileCode()
    {
        if ($this->request->isPost()) {
            $mobile = $this->request->post("mobile");
            if($mobile && !Validate::is($mobile,"length:4,16|number")){
                $this->error(lang("user.mobileError"));
            }
            $uncode = $this->request->post("uncode");
            if ($mobile) {
                if(UserModel::where('mobile', $mobile)->where("uncode",$uncode)->value('id')){
                    $this->error(lang("user.mobileexistence"));
                }
                $phone = '+'.$uncode.$mobile;
            }else{
                $phone = '+'.$this->request->userInfo['uncode'].$this->request->userInfo['mobile'];
            }

            $result = sendCode::singleSend($phone);
            $this->success("success",$result);
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
		    if (! password_verify($input['code'].'index_modifyPassword_email_code'.$input['email'].$input['salt'].$this->request->ip(), $input['rcode'])) {
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
		    $data = $save->toArray();
		    $data['pay_paasword'] = $save->pay_paasword==0?0:1;
		    $data['password'] = '';
			$this->success(lang('system.operation_succeeded'),$data);
		    // return json(['status' => 'success', 'message' => lang('system.operation_succeeded')]);
		}
	}
	public function passwordMobile()
    {
        $input = input("post.");
        if($input['code'] != cache($this->request->userInfo["mobile"]))
        {
            $this->error(lang('user.captchaError'));
        }
        $save = $this->request->userInfo;
        $save->password = $input['password'];
        $save->update_time = date('Y-m-d H:i:s');
        $save->save();
        $data = $save->toArray();
        $data['pay_paasword'] = $save->pay_paasword==0?0:1;
        $data['password'] = '';
        $this->success(lang('system.operation_succeeded'),$data);
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
		    if (! password_verify($input['code'].'index_modifyPassword_email_code'.$input['email'].$input['salt'].$this->request->ip(), $input['rcode'])) {
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
		    $data = $save->toArray();
		    $data['pay_paasword'] = $save->pay_paasword==0?0:1;
		    $data['password'] = '';
			$this->success(lang('system.operation_succeeded'),$data);
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
			$gamewind=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>3,'money_type'=>1])->whereDay('add_time')->value("SUM(CAST(amount as DECIMAL (18,3))) as amount");
			//今日游戏输的
			$gamefail=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>3,'money_type'=>2])->whereDay('add_time')->value("SUM(CAST(amount as DECIMAL (18,3))) as amount");
			$profit = round(GameBetLog::where(['user_id'=>$userInfo->id])->whereDay('betTime')->sum('netPnl'),2);
            $data['profit']=$profit > 0 ? $profit : 0 ;
			// 今日流水
			$data['water']=round(GameBetLog::where(['user_id'=>$userInfo->id])->whereDay('betTime')->sum('betAmount'),2);
			// echo CapitalFlowmodel::getLastSql();exit;
			// 今日股息
			$data['dividend']=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>4,'money_type'=>1])->whereDay('add_time')->value("SUM(CAST(amount as DECIMAL (18,3))) as amount") ?? 0;
			// 总获得的股息
			$data['dividends']=CapitalFlowmodel::where(['uid'=>$userInfo->id,'type'=>4,'money_type'=>1])->value("SUM(CAST(amount as DECIMAL (18,3))) as amount") ?? 0;
            $withdrawConfig =  ConfigModel::getVal('withdraw');
			$data['miniwithrawal'] = $withdrawConfig['minprice'];
			$data['rate'] = $withdrawConfig['rate'];
			// 资金的详细列表
			$data['list']=CapitalFlowmodel::where([
				['uid','=',$userInfo->id],
				['money_type','<>',0]
			])->page(1)->limit(10)->order('id desc')->select();
			foreach($data['list'] as $k=>$v){
				$v->add_times=date('Y-m-d',$v->add_time);
				$v->content=getlang($v->content);
			}
			if($userInfo['id'] == "656")
			{
			    $rand = mt_rand(1,3);
			    if($rand == 1){
                    $data['balance'] = 402301.58;
                    $data['profit'] = 23575.54;
                    $data['water'] =  8616.52;
                    $data['dividend'] = 12532.24;
                    $data['dividends'] = 150696.58;
                    unset($data['list']);
                    $data['list'][] = ["id"=>1,"money_type"=>1,"content"=>"UNG coin dividend $12532.24","amount"=>"12532.24","add_times"=>date("Y-m-d"),"balance"=>"402301.58"];
                    $data['list'][] = ["id"=>1,"money_type"=>1,"content"=>"Play game HEIST Tossand earn $5350","amount"=>"5350.68","add_times"=>date("Y-m-d"),"balance"=>"389769.34"];
                    $data['list'][] = ["id"=>1,"money_type"=>1,"content"=>"Play game HEIST Tossand earn $8305.5","amount"=>"8305.5","add_times"=>date("Y-m-d"),"balance"=>"384418.66"];
                }else if($rand == 2){
			        $data['balance'] = 20562.21;
                    $data['profit'] = 320.00;
                    $data['water'] = 50.00;
                    $data['dividend'] = 230.1;
                    $data['dividends'] = 5231.21;
                    unset($data['list']);
                    $data['list'][] = ["id"=>1,"money_type"=>1,"content"=>"Play game Minesweeper and earn $320.00","amount"=>"320.00","add_times"=>date("Y-m-d"),"balance"=>"20562.21"];
                    $data['list'][] = ["id"=>1,"money_type"=>1,"content"=>"User sign-in rewards $20.00","amount"=>"20.00","add_times"=>date("Y-m-d"),"balance"=>"20242.21"];
                }else{
                    $data['balance'] = 4523.51;
                    $data['profit'] = 1532.1;
                    $data['water'] = 634.12;
                    $data['dividend'] = 23.1;
                    $data['dividends'] = 450.21;
                    unset($data['list']);
                    $data['list'][] = ["id"=>1,"money_type"=>1,"content"=>"Play game Monster Hunterand and earn $610.00","amount"=>"610.00","add_times"=>date("Y-m-d"),"balance"=>"4523.51"];
                    $data['list'][] = ["id"=>1,"money_type"=>1,"content"=>"Play game Oneshot Fishingand and earn $120.2","amount"=>"120.2","add_times"=>date("Y-m-d"),"balance"=>"3913.51"];
                }

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
    public function modifyPasswordSendCode()
    {
        $user = $this->request->userInfo;
        if($user->is_check_email)
        {
            $account = UserModel::where("id",$user['id'])->value("email");
            $result = sendCode::email($account, 'index_modifyPassword_email_code', "modify Password");
            $type = "email";
        }else if($user->is_check_mobile)
        {
            $account = UserModel::where("id",$user['id'])->value("mobile");
            $phone = '+'.$user['uncode'].$account;
            $result=sendCode::singleSend($phone);
            // $data = json_decode($result,true);
            if($result['code']!=0){
                $this->error(lang('user.codeerror'));
            }
            $type = "mobile";
        }else{
            $this->error("Please verify your account first.","",466);
        }
        $this->success("success",["type"=>$type,"data"=>$result,"account"=>$user[$type]]);
    }
	public function searchwallet()
    {
        
    }
    public function addIdCardImage()
    {
        $data = input("post.");
        $validate = new IdCard();
        if(!$validate->check($data))
        {
            $this->error($validate->getError());
        }
        $userIdCard = UserIdcard::where("user_id",$this->request->userInfo['id'])->find();
        if($userIdCard && $userIdCard->status == 1) $this->error("Duplicate submission");
        $save = [
            "user_id"=>$this->request->userInfo['id'],
            "idCard_image"=>$data['idCard_image'],
            "idCard_image_with_hand"=>$data['idCard_image_with_hand'],
            "surname"=>$data['surname'],
            "name"=>$data['name'],
            "status"=>0
        ];
        if($userIdCard)
        {
            $userIdCard->save($save);
        }else{
            UserIdcard::create($save);
        }
        $this->success("Submission successful.");
    }
    public function getUserIdCard()
    {
        return $this->success("success",UserIdcard::where(["user_id"=>$this->request->userInfo['id']])->find());
    }
    public function logoff()
    {
        UserModel::destroy($this->request->userInfo['id']);
        $this->success("success");
    }
    public function outaccount(){
        $id=$this->request->userInfo['id'];
        UserModel::where(['id'=>$id])->update(['status'=>0]);
        $this->success("success");
    }
}
