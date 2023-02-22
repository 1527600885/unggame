<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use think\Request;
use think\facade\Cache;
use think\facade\Db;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Response\QrCodeResponse;
class Ung extends BaseController
{
	protected $noNeedLogin = ['ungset','ungdata','qrcode','transfer'];
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
    		 $ungsetdata['UNG'] = $userInfo['UNG'];
    		
    // 		累计股息金额
            $userdvdall = Db::name("ung_user_divd")->where("userid",$userInfo['id'])->sum("divdmoney");
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
        		$ungsetdata['interest']=0;
        // 		var_dump($ungone->interest);
	    }
	    $this->success(lang('system.success'),$ungsetdata);
	}
	// 获取相关的数据
	public function ungdata(){
	   // die;
		$userInfo=$this->nologuserinfo;
		var_dump($userInfo);
		die;
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
	    
		$userinfo = $this->request->userInfo;
		
		
		$ungaddress = input("ungaddress");//收账区块地址
		$quantity = input("quantity");//数量
// 		$actual   = input("actual");//手续费
        $password   = input("password");//支付密码
        $userdata = Db::name("user")->where("id",$userinfo['id'])->where("password",$password)->find();
        if(!$userdata){
            $this->error(lang('支付密码错误'));
        }
		$touser = Db::name("user")->where("ungaddress",$ungaddress)->find();
		if(!$touser){
		    $this->error(lang('user.emailerror'));
		}
		if($touser["ungaddress"]==$ungaddress){
		    $this->error(lang('user.emailerror'));
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
		$insert_log['price'] = $ungset->price;//价格
		$insert_log['touserid'] = $touser['id'];//收入方ID
		$insert_log['orderno'] = $orderno;//订单编号
		$insert_log['total_price'] = bcmul($quantity,$ungset->price);//总价值
		$insert_log['actual'] = bcmul($quantity,$ungset->trachecharge);//手续费
		$insert_log['add_time'] = date();
// 		转入方
        $touser_log['uid'] = $touser['id'];
		$touser_log['type'] = 2;//操作类型
		$touser_log['num'] = bcsub($quantity,$insert_log['actual']);//数量
		$touser_log['price'] = $ungset->price;//价格
		$touser_log['touserid'] = $userinfo['id'];//转入方ID
		$touser_log['orderno'] = $orderno;//订单编号
		$touser_log['total_price'] = bcmul($touser_log['num'],$ungset->price);//总价值
		$touser_log['total_price'] = $insert_log['actual'];//手续费
		$touser_log['add_time'] = date();
        //计算ung
        //开启事务
        Db::startTrans();
        try {
            // 修改数量
            Db::name("user")->where("id",$userinfo["id"])->update("mun",bcsub($userinfo["num"],$quantity)); 
            Db::name("user")->where("id",$touser)->update("mun",bcadd($touser['num'],$touser_log['num']));
            // 增加记录
            Db::name("ung_user_log")->insert($insert_log);
            // 增加手续费池
            Db::name("user_set")->where('id',$ungset['id'])->update('allcharge',bcadd($ungset['allcharge'],$insert_log['actual']));
            Db::commit();
            $this->success(lang('system.success'));
            // Db::name("ung_user_log")->insert($touser_log);
        } catch (Exception $e) {
            Db::rollback();
            $this->error(lang('user.emailerror'));
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
	    $userinfo = $this->request->userInfo;
	    $password = input('password');
	    if($password != $userinfo['password']){
	        $this->error(lang('user.emailerror'));
	    }
		$num = input("num");
		$ungset = Db::name("ung_set")->order('id,asc')->find();
		if($num<$ungset['buylimit']){
		    $this->error(lang('user.emailerror'));
		}
		if($num>$ungset['currency_num']){
		    $this->error(lang('user.emailerror'));
		}
		Db::startTrans();
		try{
		    $userdata = Db::name("user")->where('id',$userinfo['id'])->find();
		    $setdata['UNG']  = bcadd($userdata['ung'],$num);
		    $setdata['balance']  = bcsub($userdata['balance'],bcmul($num,$ungset['price']));
		    Db::name("user")->where('id',$userinfo['id'])->update($setdata);
		    Db::name("user_set")->where('id',$ungset['id'])->update('currency_num',bcsub($ungset['currency_num'],$num));
		}catch(Exception $e){
		    Db::rollback();
		    $this->error(lang('user.emailerror'));
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
		$userinfo=$this->request->userInfo;
		$password = input("password");
		$userdata = Db::name("user")->where("id",$userinfo['id'])->find();
        if(!$userdata || $userdata['password']!=$password){
            $this->error(lang('支付密码错误'));
        }
        //获取数字资产设置
        $ungset = Db::name("ung_set")->order("id,asc")->find();
        $actual = bcmul(bcmul($ungset['servicecharge'],$userdata['ung']),$ungset['price']);//手续费
        $realung = bcsub($userdata['ung'],bcmul($ungset['servicecharge'],$userdata['ung']));//实际回收ung数量
        $allmoney = bcmul($ungset['price'],$userdata['ung']);//账户总金额美刀
        $realmoney = bcsub($allmoney,$actual);//实际到账
        $orderno = $subzm[rand(0,2)].date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        Db::startTrans();
        try{
            $insert_log['uid'] = $userinfo['id'];
    		$insert_log['type'] = 5;//操作类型
    		$insert_log['num'] = $quantity;//数量
    		$insert_log['price'] = $ungset->price;//价格
    		$insert_log['touserid'] = 0;//收入方ID
    		$insert_log['orderno'] = $orderno;//订单编号
    		$insert_log['total_price'] = $realmoney;//总价值
    		$insert_log['actual'] = $actual;//手续费
    		$insert_log['add_time'] = date();
    		$update['ung'] = 0;
    		$update['balance'] = $realmoney;
    		Db::name("user")->where('id',$userinfo['id'])->update($update);//更新金额
            Db::name("ung_user_log")->insert($insert_log);//插入ung日志表
            $userflow['uid'] = $userinfo['id'];
            $userflow['type'] = 8;//ung赎回
            $userflow['money_type'] = 1;
            $userflow['amount'] = $realmoney;
            $userflow['balance'] = $userinfo['balance'];
            $userflow['content'] ="{user.inviteusers}yaoqing{user.inviteregister}$2";//前端显示
            $userflow['admin_content'] = "用户".$userinfo['nickname']."UNG资产赎回,金额增加".$realmoney."美元";
            $userflow['add_time'] = date();
            Db::name("capital_flow")->insert($userflow);
            $setdata['currency_num'] = bcadd($ungset['currency_num'],$realung);//ung总数
            $setdata['allcharge'] = bcadd($ungset['allcharge'],bcmul($ungset['servicecharge'],$userdata['ung']));
            Db::name("ung_set")->where("id",$ungset['id'])->update($setdata);
            Db::commit();
        } catch(Exception $e){
            Db::rollback();
            $this->error(lang('user.emailerror'));
        }
	}
	// 分红规则--由系统自动托管运行
	public function bonus(){
		
	}
}