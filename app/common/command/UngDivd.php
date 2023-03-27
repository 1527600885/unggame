<?php
	namespace app\common\command;

	use app\api\model\User;
	use think\App;
	use think\console\Command;
	use think\console\Input;
	use think\console\Output;
	use think\facade\Env;
	use think\facade\Db;
	use app\common\lib\Redis;
	/**
	 * 
	 */
	class UngDivd extends Command
	{
		
		protected function configure()
	    {
	        $this->setName('ungdivd')
	            ->setDescription('Set dividend');
	    }
	    protected function execute(Input $input, Output $output)
	    {
	    	try{
	    		Db::startTrans();
	    		$redis = (new Redis())->getRedis();
	    		$userdataid = $redis->Smembers('ung_user_id');
		        if(count($userdataid)>0){
			        	$ungset = Db::name('ung_set')->order('id asc')->find();
			        	$ungset['realinterest'] = (string)$ungset['realinterest'];
			        	foreach ($userdataid as $key => $value) {
			        		$isexists = $redis->hExists('ung_user_divd:ung_user_'.$value,'num');
			        		if($isexists){
			        			$ungdata  = $redis->hGetall('ung_user_divd:ung_user_'.$value);
					   // 		var_dump($data);
			        			if($ungdata['num']>0 && time()>=$ungdata['divd_time']+3600){
			        			    $userinfo = Db::name('user')->where('id',$value)->find();
			        			    if($userinfo){
			        			        $divd = bcmul($ungdata['num']."",$ungset['realinterest']."",5)/100;
			        			        // var_dump($divd);
    					    			$data['balance']=bcadd($divd."",$userinfo['balance']."",5);
    					    			Db::name('user')->where('id',$value)->update($data);
    					    			$userflow['uid'] = $value;
    						            $userflow['type'] = 4;//ung账单类型
    						            $userflow['other_id'] = 0;//关联ung_user_log表
    						            $userflow['money_type'] = 1;
    						            $userflow['amount'] = $divd ;//金额
    						            $userflow['balance'] = bcadd((string)$divd,(string)$userinfo['balance'],5);//用户余额
    						            $userflow['content'] ="UNG coin dividend ".'$'.$userflow['amount'];;//前端显示
    						            $userflow['admin_content'] = "用户".$userinfo['nickname']."UNG资产股息,金额增加".$divd."美元";
    						            $userflow['add_time'] = time();
    						            Db::name("capital_flow")->insert($userflow);//金额日志表
    						            
    						            // 股息日志表
    						            $divddata['userid']=$value;
    						            $divddata['ungcoin']=$ungdata['num'];
    						            $divddata['realinterest']=$ungset['realinterest'];
    						            $divddata['divdmoney']=$divd;
    						            $divddata['create_time']=time();
    						            Db::name("ung_user_divd")->insert($divddata);
    								    Db::commit();
    								    $redis->hSet('ung_user_divd:ung_user_'.$value,'divd_time',time());
			        			    }
			        			}
			        		}else{
			        			// 从数据库中写入redis
					        	$ung_user_data = Db::name('ung_user')->where('uid',$value)->find();

				        		$redis->hSet('ung_user_divd:ung_user_'.$ung_user_data['uid'],'num',$ung_user_data['num']);
					            $redis->hSet('ung_user_divd:ung_user_'.$ung_user_data['uid'],'update_time',time());
					            $redis->sAdd('ung_user_id',$ung_user_data['uid']);
					        		
			        	}
			        }
		        }else{
		        	// 从数据库中写入redis
		        	$ung_data = Db::name('ung_user')->select()->toArray();
		        	foreach ($ung_data as $key => $value) {
		        		$redis->hSet('ung_user_divd:ung_user_'.$value['uid'],'num',$value['num']);
			            $redis->hSet('ung_user_divd:ung_user_'.$value['uid'],'update_time',time());
			            $redis->hSet('ung_user_divd:ung_user_'.$value['uid'],'divd_time',time());
			            $redis->sAdd('ung_user_id',$value['uid']);
		        	}
		        }
	    		// $where[]=[now(),'>',SUBDATE(update_time,interval -1 hour)];
	    		// $userInfo = Db::name("ung_user")->alias('a')->where($where)->join('mk_user b','a.uid= b.id')->select()->toArray();
	    	}catch(Exception $e){
	    		Db::rollback();
	    	}
	    	
	    }
	}
?>