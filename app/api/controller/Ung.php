<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use app\api\model\v2\UngUserDivd;
use app\api\model\v2\UngUserLog;
use think\Request;
use think\facade\Cache;
use think\facade\Db;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Response\QrCodeResponse;
use app\common\lib\Redis;
use app\api\validate\User as UserValidate;
class Ung extends BaseController
{
	protected $noNeedLogin = ['ungset','ungdata','qrcode'];
	public function initialize(){
		parent::initialize();
		$this->UngSetModel = new \app\api\model\UngSet;//虚拟币设置
		$this->UngUserModel = new \app\api\model\UngUser;//用户持有虚拟币数量
		$this->UngUserLogModel = new \app\api\model\UngUserLog;//虚拟币日志
		$this->CapitalFlowModel = new \app\api\model\CapitalFlow;//资金流水
	}
	// 获取虚拟币的相关设置
	public function ungset(){
	    if($this->nologuserinfo){
	        $userInfo=$this->nologuserinfo;
	        $userung= Db::name('ung_user')->where('uid',$userInfo['id'])->find();
    // 	     var_dump($userInfo);
    // 		 die;
    		$ungone=$this->UngSetModel->order('id asc')->find();
    		// $this->lang
    		$ungsetdata["interest"] = $ungone->interest;
    		$ungsetdata["content"]=json_decode($ungone->content,true)[$this->lang];
    		//php获取昨日起始时间戳和结束时间戳 获取昨日分红
            $yesterday_start=mktime(0,0,0,(int)date('m'),(int)date('d')-1,(int)date('Y'));
            $yesterday_end=mktime(0,0,0,(int)date('m'),(int)date('d'),(int)date('Y'))-1;
            $whereTime[]=array("create_time",">=",$yesterday_start);
            $whereTime[]=array("create_time","<=",$yesterday_end);
            $whereTime[]=array("userid","=",$userInfo['id']);
    		$userdvd = Db::name("ung_user_divd")->where($whereTime)->find();
    		if($userdvd){
    		    $ungsetdata['divdmoney'] = sprintf('%.2f', $userdvd["divdmoney"]);
    		}else{
    		    $ungsetdata['divdmoney'] = 0;
    		}
    		$ungsetdata['UNG'] = bcadd($userung['num'] ?? '0','0',2);
    		$ungsetdata['trachecharge']=$ungone->trachecharge;//转账手续费
    		$ungsetdata['servicecharge'] = $ungone->servicecharge;//赎回手续费
    		$ungsetdata['redemptionprice'] = $ungone->redemptionprice;//赎回单价
    		$ungsetdata['balance'] = $userInfo['balance'];//用户余额
    		$ungsetdata['buylimit'] = $ungone->buylimit;//最低购买额度
    		$ungsetdata['price'] = $ungone->price;//最低购买额度
    		$ungsetdata['currency_num'] = $ungone->currency_num;//ung总数量
    		$ungsetdata['distributed'] = $ungone->distributed;//已派发分红
    // 		累计股息金额
            $userdvdall = Db::name("ung_user_divd")->where("userid",$userInfo['id'])->value("SUM(CAST(divdmoney as DECIMAL (18,2))) as divdmoney");
            if($userdvdall){
                $ungsetdata['userdvdall'] = $userdvdall;
            }else{
                $ungsetdata['userdvdall'] = 0;
            }
    // 		die;
    		
	    }else{
	            $ungone=$this->UngSetModel->order('id asc')->find();
    		    $ungsetdata["interest"] = $ungone->interest;
        		$ungsetdata["content"]=json_decode($ungone->content,true)[$this->lang];
        		$ungsetdata['divdmoney']=0;
        		$ungsetdata['UNG']=0;
        		$ungsetdata['userdvdall']=0;
        		$ungsetdata['servicecharge'] = 0;
        		$ungsetdata['interest']=0;
        		$ungsetdata['trachecharge']=$ungone->trachecharge;
        // 		var_dump($ungone->interest);
	    }
	    $this->success(lang('system.success'),$ungsetdata);
	}
	// 获取相关的数据
	public function ungdata(){
	    die;
		$userInfo=$this->nologuserinfo;

		$date=strtotime(date('Y-m-d 23:59:59'))-24*60*60;
		$time=time();
// 		var_dump($userInfo);
		if($userInfo){
		  // 昨天的股息总额
			$data['amountdata']=$this->CapitalFlowModel->where(['uid'=>$userInfo['id'],'type'=>4,'money_type'=>1])->whereDay('add_time','yesterday')->sum('amount');
			//昨天的总利润
			$data['amountalldata']=$this->CapitalFlowModel->where(['uid'=>$userInfo['id'],'type'=>4,'money_type'=>1])->sum('amount');
		}else{
			$data['amountdata']=$this->CapitalFlowModel->where(['type'=>4,'money_type'=>1])->whereDay('add_time','yesterday')->sum('amount');
			$data['amountalldata']=$this->CapitalFlowModel->where(['type'=>4,'money_type'=>1])->sum('amount');
			if($data['amountdata']==0){
				// 虚假
			}
			if($data['amountalldata']==0){
				// 虚假
			}
		}
		// 活动用户-真实
		$usernum=$this->UngUserModel->where('num','>',0)->group('uid')->count();
		// 活动用户-虚假
		$cacheusernum=cache('usernum');//用脚本增加人数
// 		if($cacheusernum){
// 			cache('usernum',$cacheusernum+rand(10,100));
// 		}else{
// 			$cacheusernum=rand(100,1000);
// 			cache('usernum',$cacheusernum);
// 		}
		$data['usernum']=$cacheusernum;
// 		总营业额
        // 真实
        // $data = 
        //虚假
		// 用户持有的总虚拟币数量-真实
		$ungnum=$this->UngUserModel->sum('num');
		// 用户持有的总虚拟币数量-虚假  累计总营业额
		$cacheungnum=cache('ungnum');
		if($cacheungnum){
			cache('ungnum',$cacheungnum+rand(1,1));
		}else{
			$cacheungnum=rand(100,300);
			cache('ungnum',$cacheungnum);
		}
		$data['ungnum']=$ungnum+$cacheungnum;
		// 平台总运行天数
		$datenumarr=cache('datenum');
		if($datenumarr){
			if($datenumarr['time']<$date){
				$datenumarr['num']=$datenumarr['num']+1;
				$datenumarr['time']=$time;
				cache('datenum',$datenumarr);
			}
			$data['datenum']=$datenumarr['num'];
		}else{
			$datenumone=rand(10,100);
			cache('datenum',['num'=>$datenumone,'time'=>$time]);
			$data['datenum']=$datenumone;
		}
		$this->success(lang('system.success'),$data);
	}
	// 转赠
	public function transfer(){
	    // $redis = (new Redis())->getRedis();
	    // $counts = $redis->keys('ung_user_divd*');

	    try{
	        $input = input('post.');
	        
	        validate(UserValidate::class)->scene('ung_transfer')->check(input('post.'));
	    }catch(\Exception $e ){
	        $this->error($e->getError());
	    }
	     
		$userinfo = Db::name("user")->where("id", $this->request->userInfo->id)->find();
		$userUng =  Db::name("ung_user")->where("uid", $this->request->userInfo->id)->find();
		$useridcard =  Db::name("user_idcard")->where("user_id", $this->request->userInfo->id)->find();
		if(!$useridcard || $useridcard['status']!=1){
			$this->error(lang('user.realnameverification'),['code'=>4]);
		}
		if($userinfo['is_check']==0){
			$this->error(lang('user.userverify'),['code'=>3]);
		}
		if($userinfo['pay_paasword']==0){
		    $this->error(lang('user.pay_paasword_empty'),['code'=>2]);
		}
		
		$ungaddress = input("ungaddress");//收账区块地址
		$quantity = input("num");//数量
// 		$actual   = input("actual");//手续费
        $password   = input("pay_password");//支付密码
        $userdata = Db::name("user")->where("id",$userinfo['id'])->where("pay_paasword",$password)->find();
        if(!$userdata){
            $this->error(lang('user.pay_paasword_error'));
        }
		$touser = Db::name("user")->alias('a')->where("a.ungaddress",$ungaddress)->join('mk_ung_user b ','b.uid= a.id')->find();
		if($touser['is_check']==0){
			$this->error(lang('user.userverify'),['code'=>3]);
		}
		if(!$touser){
		    $this->error(lang('user.addresseror'),['code'=>8]);
		}
		if($userinfo["ungaddress"]==$ungaddress){
		    $this->error(lang('user.toyouself'),['code'=>5]);
		}
		if($quantity>bcmul($userUng['num'],$userUng['pledgenum'],5)){
		    $this->error(lang('user.UNGinsufficient'),['code'=>6]);
		}
        //获取数字资产设置
        $ungset = Db::name("ung_set")->order('id asc')->find();
        // 生成唯一订单号
        $subzm=['F','B','H'];
	    $orderno = $subzm[rand(0,2)].date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        // 转出方
		$insert_log['uid'] = $userinfo['id'];
		$insert_log['type'] = 1;//操作类型
		$insert_log['num'] = $quantity;//数量
		$insert_log['allnum'] = bcsub($userUng['num'],$quantity,5);//余额
		$insert_log['price'] = $ungset['price'];//价格
		$insert_log['touserid'] = $touser['uid'];//收入方ID
		$insert_log['orderno'] = $orderno;//订单编号
		$insert_log['total_price'] = bcmul($quantity,$ungset['price'],5);//总价值
		$insert_log['actual'] = bcmul($quantity,$ungset['trachecharge'],5)/100;//手续费
		$insert_log['add_time'] = time();
// 		转入方
        $touser_log['uid'] = $touser['uid'];
		$touser_log['type'] = 2;//操作类型
		$touser_log['num'] = bcsub((string)$quantity,(string)$insert_log['actual'],5);//数量
		$insert_log['srtotalnum'] = bcadd($touser['num'],$touser_log['num'],5);//余额
		$touser_log['price'] = $ungset['price'];//价格
		$touser_log['touserid'] = $userinfo['id'];//转入方ID
		$touser_log['orderno'] = $orderno;//订单编号
		$touser_log['total_price'] = bcmul((string)$touser_log['num'],(string)$ungset['price'],5);//总价值
		$touser_log['total_price'] = $insert_log['actual'];//手续费
		$touser_log['add_time'] = time();
        //计算ung
        //开启事务
        Db::startTrans();
        try {
            \app\api\model\User::where("id",$userinfo['id'])->update(["last_trade_time"=>time()]);
            \app\api\model\User::where("id",$touser['uid'])->update(["last_trade_time"=>time()]);
            // 修改数量
            Db::name("ung_user")->where("uid",$userinfo["id"])->update(["num"=>bcsub($userUng["num"],$quantity,5)]); 
            Db::name("ung_user")->where("uid",$touser['uid'])->update(["num"=>bcadd($touser['num'],$touser_log['num'],5)]);
            // 增加记录
            Db::name("ung_user_log")->insert($insert_log);
            // 增加手续费池
            Db::name("ung_set")->where('id',$ungset['id'])->update(['allcharge'=>bcadd((string)$ungset['allcharge'],(string)$insert_log['actual'],5)]);
            
            // $redis->hSet('ung_user_divd:ung_user_'.$touser['id'],'divd_time',time());
            Db::commit();
            //放入redis
		    $redis = (new Redis())->getRedis();
            if(!$redis->exists('ung_user_divd:ung_user_'.$userinfo['id'])){
                 $redis->hSet('ung_user_divd:ung_user_'.$userinfo['id'],'divd_time',time());
            }
            if(!$redis->exists('ung_user_divd:ung_user_'.$touser['uid'])){
                 $redis->hSet('ung_user_divd:ung_user_'.$touser['uid'],'divd_time',time());
            }
            $redis->hSet('ung_user_divd:ung_user_'.$userinfo['id'],'num',bcsub($userUng["num"],$quantity,5));
            $redis->hSet('ung_user_divd:ung_user_'.$userinfo['id'],'update_time',time());
            $redis->hSet('ung_user_divd:ung_user_'.$touser['uid'],'num',bcadd($touser['num'],$touser_log['num'],5));
            $redis->hSet('ung_user_divd:ung_user_'.$touser['uid'],'update_time',time());
            $redis->sAdd('ung_user_id',$userinfo['id']);
            $redis->sAdd('ung_user_id',$touser['uid']);
            // if(!$redis->get('ung_user_id_pop'.$userinfo['id'])){
            //     $redis->set('ung_user_id_pop'.$userinfo['id'],$userinfo['id']);
            // }
            // if(!$redis->get('ung_user_id_pop'.$touser['uid'])){
            //     $redis->set('ung_user_id_pop'.$touser['uid'],$touser['uid']);
            // }
            $this->success(lang('system.success'),['code'=>1]);
            // Db::name("ung_user_log")->insert($touser_log);
        } catch (Exception $e) {
            Db::rollback();
            $this->error(lang('user.emailerror'),['code'=>7]);
        }
       
	}

	// 接收
	public function receive(){
	    $userinfo = $this->request->userInfo;
        // 获取用户二维码和区块地址
        $data = Db::name("user")->where("id",$userinfo["id"])->field("ungaddress,ungewm")->find();
        $this->success(lang('system.success'),$data);
	}
	// 购买虚拟币
	public function buy(){
	    try{
	        $input = input('post.');
	        validate(UserValidate::class)->scene('ung_buy')->check(input('post.'));
	    }catch( ValidateException $e ){
	        $this->error($e->getError());
	    }
	    $userinfo = Db::name("user")->alias('a')->where("a.id", $this->request->userInfo->id)->join('mk_ung_user b ','b.uid= a.id')->find();
	   // var_dump($userinfo);
	   // die;
		if($userinfo['pay_paasword']==0){
		    $this->error(lang('user.pay_paasword_empty'),['code'=>2]);
		}
	    $password = input('paypassword');
	    if($password != $userinfo['pay_paasword']){
	        $this->error(lang('user.pay_paasword_error'),['code'=>2]);
	    }
		$num = input("num");
		$ungset = Db::name("ung_set")->order('id asc')->find();
		if($num<$ungset['buylimit']){
		    $this->error(lang('user.buy_limit_error'),['code'=>2]);
		}
		if($num>$ungset['currency_num']){
		    $this->error(lang('user.ung_Insufficient'),['code'=>2]);
		}
		if($userinfo['balance']<bcmul($num,$ungset['price'],5)){
		    $this->error(lang('user.banlance_none'),['code'=>2]);
		}
		
		Db::startTrans();
		try{
		    $setungdata['num']  = bcadd($userinfo['num'],$num,5);
		    
		    $setdata['balance']  = bcsub((String)$userinfo['balance'],bcmul($num,$ungset['price'],5));
		    $setdata['last_trade_time'] = time();
		    // 生成唯一订单号
            $subzm=['F','B','H'];
		    $orderno = $subzm[rand(0,2)].date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
		    Db::name("user")->where('id',$userinfo['uid'])->update($setdata);
		    Db::name("ung_user")->where('uid',$userinfo['uid'])->update($setungdata);
		    Db::name("ung_set")->where('id',$ungset['id'])->update(['currency_num'=>bcsub((String)$ungset['currency_num'],$num,5)]);
		    $insert['uid']=$userinfo['uid'];
		    $insert['type']=4;
		    $insert['num']=$num;
		    $insert['price']=$ungset['price'];
		    $insert['orderno']=$orderno;
		    $insert['total_price']=bcmul($num,$ungset['price'],5);
		    $insert['totalnum'] = bcsub((String)$ungset['currency_num'],$num,5);
		    $insert['add_time']=time();
		    $logid = Db::name("ung_user_log")->insert($insert,true);
		    $userflow['uid'] = $userinfo['uid'];
		    $userflow['other_id'] = $logid;//关联ung_user_log表
            $userflow['type'] = 10;//ung账单类型
            $userflow['money_type'] = 2;
            $userflow['amount'] = $insert['total_price'];//资金
            $userflow['balance'] = $setdata['balance'];//余额
            $userflow['content'] ="UNG coin purchase ".'$'.$userflow['amount'];//前端显示
            $userflow['admin_content'] = "用户".$userinfo['nickname']."UNG购买,金额减少".$insert['total_price']."美元";
            $userflow['add_time'] = time();
            Db::name("capital_flow")->insert($userflow);
		    Db::commit();
		    //放入redis
		    $redis = (new Redis())->getRedis();
            if(!$redis->exists('ung_user_divd:ung_user_'.$userinfo['uid'])){
                 $redis->hSet('ung_user_divd:ung_user_'.$userinfo['uid'],'divd_time',time());
            }
            $redis->hSet('ung_user_divd:ung_user_'.$userinfo['uid'],'num',$setungdata['num']);
            $redis->hSet('ung_user_divd:ung_user_'.$userinfo['uid'],'update_time',time());
            $redis->sAdd('ung_user_id',$userinfo['uid']);
		    $this->success(lang('system.success'));
		  //  var_dump(22222222);
		}catch(Exception $e){
		    Db::rollback();
		    $this->error(lang('user.buy_field'));
		}
		
	}
// 	返回购买页面信息
    public function getbuyung(){
        $result = Db::name("ung_set")->order("id,asc")->find();
        $ungdata['interest'] = $result['interest'];
        $ungdata_array = explode('~',$ungdata);
        $this->success(lang('system.success'),$ungdata_array);
    }
	// 赎回虚拟币
	public function sell(){
	    try{
	        $input = input('post.');
	        validate(UserValidate::class)->scene('ung_sell')->check(input('post.'));
	    }catch( ValidateException $e ){
	        $this->error($e->getError());
	    }
	    $useridcard =  Db::name("user_idcard")->where("user_id", $this->request->userInfo->id)->find();
		if(!$useridcard || $useridcard['status']!=1){
			$this->error(lang('user.realnameverification'),['code'=>2]);
		}
		$userinfo=$this->request->userInfo;
		$userdata = Db::name("user")->alias('a')->where("a.id", $this->request->userInfo->id)->join('mk_ung_user b ','b.uid= a.id')->find();
        if($userdata['pay_paasword']==0){
		    $this->error(lang('user.pay_paasword_empty'),['code'=>2]);
		}
	    $password = input('paypassword');
	    if($password != $userdata['pay_paasword']){
	        $this->error(lang('user.pay_paasword_error'),['code'=>2]);
	    }
        //获取数字资产设置
        // 生成唯一订单号
        $subzm=['F','B','H'];
        $ungset = Db::name("ung_set")->order("id asc")->find();
        $actual = bcmul((string)$ungset['servicecharge'],$userdata['num'],5)/100;//手续费UNG
        $actualmoney = bcmul((string)$actual,$ungset['redemptionprice'],5);//手续费$
        $realung = bcsub((string)$userdata['num'],(string)$actual,5);//实际回收ung数量
        $allmoney = bcmul((string)$ungset['redemptionprice'],$userdata['num'],5);//UNG账户兑换成金额美刀
        $realmoney = bcsub((string)$allmoney,(string)$actualmoney,5);//实际到账
        // var_dump($actual);
        // var_dump($actualmoney);
        // var_dump($realmoney);
        //  var_dump(bcadd((string)$realmoney,(string)$userinfo['balance'],5));
        // die;
        $orderno = $subzm[rand(0,2)].date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        Db::startTrans();
        try{
            $insert_log['uid'] = $userinfo['uid'];
    		$insert_log['type'] = 5;//操作类型
    		$insert_log['num'] = $userdata['num'];//数量
    		$insert_log['price'] = $ungset['price'];//价格
    		$insert_log['touserid'] = 0;//收入方ID
    		$insert_log['orderno'] = $orderno;//订单编号
    		$insert_log['total_price'] = $realmoney;//总价值
    		$insert_log['actual'] = $actual;//手续费UNG
    		$insert_log['actualmoney'] =  $actualmoney;//手续费$
    		$insert_log['add_time'] = time();
    		$ungupdate['num'] = 0;
    		$update['balance'] =  bcadd((string)$realmoney,(string)$userinfo['balance'],5);
    		$update['last_trade_time'] = time();
    // 		var_dump($realmoney);
    // 		var_dump($userinfo['balance']);
    // 		var_dump($update['balance']);
    // 		die;
    		Db::name("user")->where('id',$userinfo['id'])->update($update);//更新金额
    		Db::name("ung_user")->where('uid',$userinfo['id'])->update($ungupdate);//更新金额
            $logid = Db::name("ung_user_log")->insert($insert_log,true);//插入ung日志表
            $userflow['uid'] = $userinfo['id'];
            $userflow['type'] = 10;//ung账单类型
            $userflow['other_id'] = $logid;//关联ung_user_log表
            $userflow['money_type'] = 1;
            $userflow['amount'] = $realmoney;//金额
            $userflow['balance'] = bcadd((string)$realmoney,(string)$userinfo['balance'],5);//用户余额
            $userflow['content'] ="UNG coin redeem ".'$'.$userflow['amount'];;//前端显示
            $userflow['admin_content'] = "用户".$userinfo['nickname']."UNG资产赎回,金额增加".$realmoney."美元";
            $userflow['add_time'] = time();
            Db::name("capital_flow")->insert($userflow);
            $setdata['currency_num'] = bcadd((string)$ungset['currency_num'],$realung,5);//ung总数
            $setdata['allcharge'] = bcadd((string)$ungset['allcharge'],(string)$actual,5);//手续费池
            Db::name("ung_set")->where("id",$ungset['id'])->update($setdata);
            Db::commit();
            $redis = (new Redis())->getRedis();
            // if(!$redis->exists('ung_user_divd:ung_user_'.$userinfo['id'])){
            //      $redis->hSet('ung_user_divd:ung_user_'.$userinfo['id'],'divd_time',time());
            // }
            $redis->hSet('ung_user_divd:ung_user_'.$userinfo['id'],'update_time',time());
            $redis->hSet('ung_user_divd:ung_user_'.$userinfo['id'],'num',0);
            $redis->sAdd('ung_user_id',$userinfo['id']);
            $this->success(lang('system.success'));
        } catch(Exception $e){
            Db::rollback();
            $this->error(lang('user.emailerror'));
        }
	}
	// ung质押
	public function pledgenum(){
		try{
	        $input = input('post.');
	        validate(UserValidate::class)->scene('ung_sell')->check(input('post.'));
	    }catch( ValidateException $e ){
	        $this->error($e->getError());
	    }
	    $useridcard =  Db::name("user_idcard")->where("user_id", $this->request->userInfo->id)->find();
		if(!$useridcard || $useridcard['status']!=1){
			$this->error(lang('user.realnameverification'),['code'=>2]);
		}
		$userinfo=$this->request->userInfo;
		$userdata = Db::name("user")->alias('a')->where("a.id", $this->request->userInfo->id)->join('mk_ung_user b ','b.uid= a.id')->find();
        if($userdata['pay_paasword']==0){
		    $this->error(lang('user.pay_paasword_empty'),['code'=>2]);
		}
	    $password = input('paypassword');
	    if($password != $userdata['pay_paasword']){
	        $this->error(lang('user.pay_paasword_error'),['code'=>2]);
	    }
	    if($input['pledgenum'] >$userdata['num'] || bcadd($userdata['pledgenum'],$input['pledgenum'],5)>$userdata['num'] || $input['pledgenum']<0.00001){
	        $this->error(lang('user.pay_ungnum_error'),['code'=>2]);
	    }
	    // 生成唯一订单号
        $subzm=['F','B','H'];
        $orderno = 'Z'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $data['uid'] = $userinfo['id'];
        $data['pledgenum'] = bcadd($userdata['pledgenum'],$input['pledgenum'],5);
        $data['update_time'] = time();
        $pledgedata['uid'] = $userinfo['id'];
        $pledgedata['pledgetotalungnum'] = bcadd($userdata['pledgenum'],$input['pledgenum'],5);
        // var_dump($userdata['pledgenum']);
        // var_dump($data['pledgenum']);
        // die;
        $pledgedata['pledgenum'] = $input['pledgenum'];//增加数量
        $pledgedata['create_time'] = time();
        $pledgedata['orderno'] = $orderno;
        $pledgedata['type'] = 1;
        $pledgedata['talungnum'] = $userdata['num'];
        Db::startTrans();
        try{
        	// 修改ung表
        	Db::name("ung_user")->where('uid',$userinfo['id'])->update($data);
        	// 插入质押记录表
        	$a = Db::name("pledge")->insert($pledgedata);
        	Db::commit();
        	// 修改redis数量
		    $redis = (new Redis())->getRedis();
		    $redis->hSet('ung_user_divd:ung_user_'.$userinfo['id'],'num',$data['pledgenum']);
            $redis->sAdd('ung_user_id',$userinfo['id']);
            $this->success(lang('system.success'));
        	
        }catch(Exception $e){
        	Db::rollback();
        	$this->error(lang('user.emailerror'));
        }
       
	}
	// 解除质押
	public function release(){
		try{
	        $input = input('post.');
	        validate(UserValidate::class)->scene('ung_sell')->check(input('post.'));
	    }catch( ValidateException $e ){
	        $this->error($e->getError());
	    }
	    $useridcard =  Db::name("user_idcard")->where("user_id", $this->request->userInfo->id)->find();
		if(!$useridcard || $useridcard['status']!=1){
			$this->error(lang('user.realnameverification'),['code'=>2]);
		}
		$userinfo=$this->request->userInfo;
		$userdata = Db::name("user")->alias('a')->where("a.id", $this->request->userInfo->id)->join('mk_ung_user b ','b.uid= a.id')->find();
        if($userdata['pay_paasword']==0){
		    $this->error(lang('user.pay_paasword_empty'),['code'=>2]);
		}
	    $password = input('paypassword');
	    if($password != $userdata['pay_paasword']){
	        $this->error(lang('user.pay_paasword_error'),['code'=>2]);
	    }
	    if($input['releasenum'] >$userdata['pledgenum']  || $input['releasenum']<=0){
	        $this->error(lang('user.pay_ungnum_error'),['code'=>2]);
	    }
	   
	      // 生成唯一订单号
        $subzm=['F','B','H'];
        $orderno = 'Z'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $data['uid'] = $userinfo['id'];
        $data['pledgenum'] = bcsub($userdata['pledgenum'],$input['releasenum'],5);
        $data['update_time'] = time();
        $pledgedata['uid'] = $userinfo['id'];
        $pledgedata['pledgetotalungnum'] = $data['pledgenum'];
        $pledgedata['pledgenum'] = $input['releasenum'];//增加数量
        $pledgedata['create_time'] = time();
        $pledgedata['orderno'] = $orderno;
        $pledgedata['type'] = 2;
        $pledgedata['talungnum'] = $userdata['num'];
        Db::startTrans();
        try{
        	// 修改ung表
        	Db::name("ung_user")->where('uid',$userinfo['id'])->update($data);
        	// 插入质押记录表
        	Db::name("pledge")->insert($pledgedata);
        	Db::commit();
        	// 修改redis数量
		    $redis = (new Redis())->getRedis();
		    $redis->hSet('ung_user_divd:ung_user_'.$userinfo['id'],'num',$data['pledgenum']);
            $redis->sAdd('ung_user_id',$userinfo['id']);
            $this->success(lang('system.success'));
        	
        }catch(Exception $e){
        	Db::rollback();
        	$this->error(lang('user.emailerror'));
        }
	}
	// 分红规则--由系统自动托管运行
	public function bonus(){
		
	}
    public function divdList()
    {
       $lists = UngUserDivd::where("userid",$this->request->userInfo['id'])->order("id desc")->paginate(20)->each(function($item){
           $item['create_time'] = date('Y-m-d', $item['create_time']);
           return $item;
       });
       $this->success("success",$lists);
    }
    public function pledges(){ 
        $lists = Db::name('pledge')->where("uid",$this->request->userInfo['id'])->order("id desc")->paginate(20)->each(function($item){
           $item['create_time'] = date('Y-m-d', $item['create_time']);
           return $item;
       });
       $this->success("success",$lists);
    }
    public function userLog()
    {
        $map = [];
        $type = $this->request->param("type/d");
        if($type == 2){
            $map['touserid'] = $this->request->userInfo['id'];
            $type = 1;
        }else if( $type == 0){
            $map['touserid|uid'] = $this->request->userInfo['id'];
        }else{
            $map['uid'] = $this->request->userInfo['id'];
        }
        $user_id =$this->request->userInfo['id'];
        $lists = UngUserLog::withSearch("type",["type"=>$type])->where($map)->order("id desc")->paginate(10)->each(function ($item)use($user_id){
            if($item['type'] == 1 && $item['touserid'] ==$user_id)
            {
                $item['addtype'] = "+";
            }else if(in_array($item['type'],[3,4,6])){
                $item['addtype'] = "+";
            }else{
                $item['addtype'] = "-";
            }
            return $item;
        });
        $this->success("success",$lists);
    }
}