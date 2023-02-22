<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use app\common\lib\Redis;
use think\exception\HttpResponseException;
use think\facade\Cache;
use Hashids\Hashids;
use think\facade\Session;
use think\facade\Db;
use QL\QueryList;
use QL\Ext\CurlMulti;

use think\Request;

class Game extends BaseController
{
    protected $noNeedLogin = ['gameindex','ftplist','gamelist','gamebrand','notice','notice_list','opengameurl'];
    public function initialize(){
        parent::initialize();
        $this->apigame = new \app\common\game\ApiGame;//游戏相关接口
        $this->GameBrandModel = new \app\api\model\GameBrand;//游戏品牌模型
        $this->GameListModel = new \app\api\model\GameList;//游戏列表模型
        $this->GamelogModel = new \app\api\model\Gamelog;//游戏日志模型
        $this->UserModel = new \app\api\model\User;//用户模型
        $this->GamecollectionModel = new \app\api\model\Gamecollection;//游戏收藏模型
        $this->NoticeModel = new \app\api\model\Notice;//游戏公告
        $this->CarouselChartModel = new \app\api\model\CarouselChart;//轮播图
        $this->gametype = ['RNG','FISH','PVP','LIVE','SPORT'];
    }

    /**
     * 显示首页游戏公告和首页的轮播图
     *
     * @return \think\Response
     */
    public function notice(){
        $data['noticeone']=$this->NoticeModel->where('is_show',1)->order("id desc")->find();
        $data['carouselchart']=$this->CarouselChartModel->where('status',1)->order('index_sort asc')->select();
        if($data['noticeone']){
            $content=json_decode($data['noticeone']->content,true);
            $data['noticeone']->content=$content[$this->lang];
        }
        $data['time']=date('H:i');
        $this->success(lang('system.success'),$data);
    }
    /**
     * 显示公告列表
     *
     * @return \think\Response
     */

    public function notice_list(){
        $type=input('type/d');
        $pageNum=input('pageNum/d');
        $pageSize=input('pageSize/d');
        if($type==0){
            $uidarr=['uid','=',null];
        }else{
            if($this->nologuserinfo){
                $uidarr=['uid','=',$this->nologuserinfo['id']];
            }else{
                $uidarr=['uid','=',null];
            }
        }

        $noticecount=$this->NoticeModel->where([
            ['is_show','=',1],
            $uidarr
        ])
            ->order("id desc")
            ->count();

        $noticelist=$this->NoticeModel->where([
            ['is_show','=',1],
            $uidarr
        ])
            ->order("id desc")
            ->page($pageNum)
            ->limit($pageSize)
            ->select();
        $data['totalSize']=$noticecount;
        $data['totalPage']=ceil($noticecount/$pageSize);
        if($pageNum<$data['totalPage']){
            $data['hasNext']=true;
        }else{
            $data['hasNext']=false;
        }
        $data['list']=$noticelist;
        foreach($noticelist as $k=>$v){
            $v->content=json_decode($v->content,true)[$this->lang];
        }
        $this->success(lang('system.success'),$data);
    }
    /**
     * 显示首页游戏列表
     *
     * @return \think\Response
     */
    public function gameindex(){
		$data=[];
		$collectionlist=null;
		$productCode=input("post.productCode");//游戏分类ID
		$type=input("post.type");
		$cachename='indexdata_'.$this->gamelang.'_';
		if($type){
			$gameType=$this->gametype[$type];
			var_dump($gameType);
			die;
			$cachename.=$gameType."_";
		}
		if($productCode){
			$cachename.=$productCode."_";
		}
		$cachename.=$this->request->ip();
		$cache=cache($cachename);
		// if(!$cache){
			
		// 	cache($cachename,$data,'7200');
		// }else{
		// 	$data=$cache;
		// }
		$userInfo=$this->nologuserinfo;
        // 用户收藏游戏列表ID
		if($userInfo){
			$collectionlist=$this->GamecollectionModel->where('uid',$userInfo->id)->select();
		}
		//获左侧边栏游戏分类
		if($type){
			$hot_where=[
				['displayStatus','=',1],
				['gameType','=',$gameType],//游戏类型
				['category_put','=',1],//当前分类下推荐
				['category_sort','>',0],
			];
			// $hot_where=['displayStatus'=>1,'gameType'=>$gameType,'category_put'=>1];
// 			排序
			$hot_order='category_sort asc,hot desc';
		}else{
		  //  默认分类，全局查找
			$hot_where=[
				['displayStatus','=',1],
				['is_groom','=',1],//是否首页推荐
				['groom_sort','>',0],//排序序列号>0
			];
			// $hot_where=['displayStatus'=>1,'is_groom'=>1];
			$hot_order='groom_sort asc,hot desc';//排序号排序
		}
		//获取热门游戏列表
		$hot_game=$this->GameListModel->withoutField('add_time')->where($hot_where)->limit(6)->page(1)->order($hot_order)->select();
		
		foreach($hot_game as $k=>$v){
			$gameName=json_decode($v->gameName,true);//游戏名称字段
			$v->gameName=croppstring($gameName[$this->gamelang],6);//gamelang:游戏语言   
			
			$gameImage=json_decode($v->gameImage,true);//游戏图片
			if($this->gamelang=='KO'){
				$v->gameImage=$gameImage['EN'];
			}elseif($this->gamelang=='MS'){
				$v->gameImage=$gameImage['ID'];
			}else{
				$v->gameImage=$gameImage[$this->gamelang];
			}
			$v->type="heart";
			if($collectionlist){
				foreach($collectionlist as $key=>$value){
					if($v->id==$value->gid){
						$v->type="heart-filled";//收藏
					}
				}
			}
		}
		//获取电子游戏
		if($productCode){
			$where=[
				'displayStatus'=>1,
				'gameType'=>'RNG',
				'productCode'=>$productCode
			];
		}else{
			$where=[
				'displayStatus'=>1,
				'gameType'=>'RNG'
			];
		}
		$hot_order='groom_sorts asc,hot desc';//排序号排序
		$rng_game=$this->GameListModel->field('*,if(groom_sort is null,2000000,groom_sort) as groom_sorts')->withoutField('add_time')->where($where)->limit(13)->page(1)->order($hot_order)->select();//查询随机排序
		foreach($rng_game as $k=>$v){
			$gameName=json_decode($v->gameName,true);
			$v->gameName=croppstring($gameName[$this->gamelang],6);
			$gameImage=json_decode($v->gameImage,true);
			if($this->gamelang=='KO'){
				$v->gameImage=$gameImage['EN'];
			}elseif($this->gamelang=='MS'){
				$v->gameImage=$gameImage['ID'];
			}else{
				$v->gameImage=$gameImage[$this->gamelang];
			}
			$v->type="heart";
			if($collectionlist){
				foreach($collectionlist as $key=>$value){
					if($v->id==$value->gid){
						$v->type="heart-filled";
					}
				}
			}
		}
		$hot_order='groom_sorts asc,hot desc';//排序号排序
		//获取捕鱼游戏
		$fish_game=$this->GameListModel
		->field('*,if(groom_sort is null,2000000,groom_sort) as groom_sorts')
		->withoutField('add_time')
		->where([
			['displayStatus','=',1],
			['gameType','=','FISH'],
			['productCode','<>','AG']
			])
		->limit(4)
		->page(1)
		->order($hot_order)
		->select();
		foreach($fish_game as $k=>$v){
			$gameName=json_decode($v->gameName,true);
			$v->gameName=croppstring($gameName[$this->gamelang],6);
			$gameImage=json_decode($v->gameImage,true);
			if($this->gamelang=='KO'){
				$v->gameImage=$gameImage['EN'];
			}elseif($this->gamelang=='MS'){
				$v->gameImage=$gameImage['ID'];
			}else{
				$v->gameImage=$gameImage[$this->gamelang];
			}
		}
		$data['hot_game']=$hot_game;//hot_game
		$data['rng_game']=$rng_game;//computer game
		$data['fish_game']=$fish_game;//fishing game
		$this->success(lang('system.success'),$data);
	}

    /**
     * 收藏游戏
     *
     * @return \think\Response
     */
    public function collection(){
        $tcgGameCode=input('tcgGameCode');
        $userInfo=$this->request->userInfo;
        $gameinfo=$this->GameListModel->where('tcgGameCode',$tcgGameCode)->find();
        //查询是否收藏过
        $collection_count=$this->GamecollectionModel->where(['uid'=>$userInfo->id,'gid'=>$gameinfo->id])->count();
        if($collection_count==0){
            //收藏操作
            $id=$this->GamecollectionModel->insertGetId(['uid'=>$userInfo->id,'gid'=>$gameinfo->id,'add_time'=>time()]);
            if($id){
                $this->GameListModel->where('id',$gameinfo->id)->inc('collection')->update();
                $this->success(lang('system.operation_succeeded'),"heart-filled");
            }else{
                $this->error(lang('system.operation_failed'));
            }
        }else{
            //取消收藏操作
            $this->GamecollectionModel->where(['uid'=>$userInfo->id,'gid'=>$gameinfo->id])->delete();
            $this->GameListModel->where('id',$gameinfo->id)->dec('collection')->update();
            $this->success(lang('system.operation_succeeded'),"heart");
        }
    }

    /**
     * 启动电子类游戏
     *
     * @return \think\Response
     */
    public function rungame(){
        $userInfo=$this->request->userInfo;
        $redis = (new Redis())->getRedis();
        $log = $redis->get("user_wait_recapture_{$userInfo->id}");
        if($log){
            try{
                $this->recapture();

            }catch (\Exception $e){

            }
            $this->error("Please wait for the funds to be withdrawn from the game",null,405);
        }
        $redis = (new Redis())->getRedis();
        $key = "user_rungame_{$this->request->userInfo->id}";
        $rungame = $redis->get($key);
        if($rungame){
            $this->error(lang("frequent_operation") ,null,405);
        }else{
            $redis->set($key,1,5);
        }

        $tcgGameCode=input('tcgGameCode');
        $gameinfo=$this->GameListModel->where('tcgGameCode',$tcgGameCode)->find();
        $gamename=json_decode($gameinfo->gameName,true)[$this->gamelang];

        try{
            Db::startTrans();
            if($userInfo->balance<=0){
                //没有资金的情况下，将不进行资金的操作
                $result=$this->apigame->getLaunchGameRng($userInfo->game_account,$userInfo->nickname,$gameinfo->productType,1,$tcgGameCode,'html5',$this->gamelang);
                $ret=json_decode($result,true);
                // var_dump($ret);die();
                if($ret['status']==0){
                    $id=$this->GamelogModel->insertGetId(['uid'=>$userInfo->id,'gid'=>$gameinfo->id,'amount'=>$userInfo->balance,'add_time'=>time()]);
                    if($id){
                        // var_dump($ret);die();
                        $this->success(lang('game.run_game'),['game_url'=>$ret['game_url'],'gamename'=>$gamename]);
                        // die();
                    }
                }else{
                    $this->error(isset($ret['error_message'])?$ret['error_message']:$ret['error_desc']);
                }
                // $this->error(lang('game.money_funds'));
            }else{
                //将全部资金带入游戏
                $rechargeno="game_recharge_".$userInfo->game_account.time();
                // dump($userInfo->game_account);
                // dump($gameinfo->productType);
                // dump($userInfo->balance);
                // dump($rechargeno);
                // exit;
                $result=$this->apigame->user_transfer($userInfo->game_account,$gameinfo->productType,1,$userInfo->balance,$rechargeno);
                //游戏成功了
                $ret=json_decode($result,true);
                //  var_dump($ret);die();
                //强制赋予成功返回，方便测试
                // $ret=['status'=>0,'transaction_status'=>'SUCCESS'];
                if($ret['status']==0){
                    if($ret['transaction_status']=="SUCCESS"){
                        $balance  = $this->UserModel->lock(true)->where('id',$userInfo->id)->value("balance");
                        //转入成功操作
                        $this->UserModel->where('id',$userInfo->id)->update(['balance'=>0]);
                        $id=$this->GamelogModel->insertGetId(['uid'=>$userInfo->id,'gid'=>$gameinfo->id,'amount'=>$userInfo->balance,'add_time'=>time()]);
                        if($id){
                            $result=$this->apigame->getLaunchGameRng($userInfo->game_account,$userInfo->nickname,$gameinfo->productType,1,$tcgGameCode,'html5',$this->gamelang);
                            $ret=json_decode($result,true);
                            if($ret['status']==0){
                                $redis->set("user_wait_recapture_{$userInfo->id}",1);
                                $this->success(lang('game.run_game'),['game_url'=>$ret['game_url'],'gamename'=>$gamename]);
                            }else{
                                $this->error(isset($ret['error_message'])?$ret['error_message']:$ret['error_desc']);
                            }
                        }
                    }elseif($ret['transaction_status']=="PENDING"){
                        //延迟转入操作,查询资金情况
                        $result=$this->apigame->check_transfer($gameinfo->productType,$rechargeno);
                        $ret=json_decode($result,true);
                        if($ret['status']==0){
                            $balance  = $this->UserModel->lock(true)->where('id',$userInfo->id)->value("balance");
                            //已转入成功，重新启动游戏
                            $this->UserModel->where('id',$userInfo->id)->update(['balance'=>0]);
                            $id=$this->GamelogModel->insertGetId(['uid'=>$userInfo->id,'gid'=>$gameinfo->id,'amount'=>$userInfo->balance,'add_time'=>time()]);
                            if($id){
                                $result=$this->apigame->getLaunchGameRng($userInfo->game_account,$userInfo->nickname,$gameinfo->productType,1,$tcgGameCode,'html5',$this->gamelang);
                                $ret=json_decode($result,true);
                                if($ret['status']==0){
                                    $redis->set("user_wait_recapture_{$userInfo->id}",1);
                                    $this->success(lang('game.run_game'),['game_url'=>$ret['game_url'],'gamename'=>$gamename]);
                                }else{
                                    $this->error(isset($ret['error_message'])?$ret['error_message']:$ret['error_desc']);
                                }
                            }
                        }else{
                            $this->error($ret['error_desc']);
                        }
                    }else{
                        //转入失败操作
                        $this->error($ret['error_desc']);
                    }
                }else{
                    $this->error($ret['error_desc'],null,404);
                }
            }
            Db::commit();
        }catch (HttpResponseException $e){
            $re = $e->getResponse();
            Db::commit();
            throw new HttpResponseException($re);
        }catch (\Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }

    }

    /**
     * 资金回笼到平台
     *
     * @return \think\Response
     */
    public function recapture(){
        $userInfo=$this->request->userInfo;
        $redis = (new Redis())->getRedis();
        $key = "user_recapture_{$this->request->userInfo->id}";
        $rungame = $redis->get($key);
        if($rungame){
            $this->error(lang("frequent_operation"));
        }else{
            $redis->set($key,1,10);
        }
        try{
            Db::startTrans();
            $gamelog=$this->GamelogModel->where(['uid'=>$userInfo->id,'type'=>1])->lock(true)->order('id desc')->find();
            if($gamelog){
                $game_info=$this->GameListModel->where('id',$gamelog->gid)->find();
                //查询上一个游戏的资金情况
                $result=$this->apigame->get_balance($userInfo->game_account,$game_info->productType);
                $ret=json_decode($result,true);
                if($ret['status']==0){
                    $game_name=json_decode($game_info->gameName,true)[$this->gamelang];
                    if($ret['balance'] > 0){
                        //将资金回笼到平台
                        $result=$this->apigame->user_all_transfer($userInfo->game_account,$game_info->productType,"game_withdrawal_".$userInfo->game_account.time());
                        $ret=json_decode($result,true);
                        $balance=$ret['amount'];
                        $this->UserModel->where('id',$userInfo->id)->update(['balance'=>$balance]);
                        $this->GamelogModel->update(['type'=>2,'id'=>$gamelog->id]);
                        if($gamelog->amount>$balance){
                            //用户输的情况
                            $money_type=2;
                            $amount=bcadd($gamelog->amount."",-$balance."",2);
                            $userbalance=bcadd($gamelog->amount."",-$amount."",2);
                            $content='{capital.gamecontento}'.$game_name.'{capital.gamecontenth}'.$amount.'{capital.money}';
                            $admin_content='用户'.$userInfo->nickname.'游玩游戏'.$game_name.'资金减少'.$amount.'美元';
                        }elseif($balance>$gamelog->amount){
                            //用户赢的情况
                            $money_type=1;
                            $amount= bcadd($balance."",-$gamelog->amount."",2);
                            $userbalance=bcadd($gamelog->amount."",$amount."",2);
                            $content='{capital.gamecontento}'.$game_name.'{capital.gamecontentt}'.$amount.'{capital.money}';
                            $admin_content='用户'.$userInfo->nickname.'游玩游戏'.$game_name.'资金增加'.$amount.'美元';
                        }elseif($balance==$gamelog->amount){
                            // 用户没赢没输的情况
                            $money_type=0;
                            $amount=0;
                            $userbalance=$gamelog->amount;
                            $content='{capital.gamecontento}'.$game_name.'{capital.gamecontentf}';
                            $admin_content='用户'.$userInfo->nickname.'游玩游戏'.$game_name.'资金不变';
                        }
                        capital_flow($userInfo->id,$gamelog->gid,3,$money_type,$amount,$userbalance,$content,$admin_content,$gamelog['id']);
                    }else if($gamelog->amount > 0){//初始金额不为0.全部输光
                        $this->GamelogModel->update(['type'=>2,'id'=>$gamelog->id]);
                        $money_type=2;
                        $amount = $gamelog->amount;
                        $userbalance= 0;
                        $content='{capital.gamecontento}'.$game_name.'{capital.gamecontenth}'.$amount.'{capital.money}';
                        $admin_content='用户'.$userInfo->nickname.'游玩游戏'.$game_name.'资金减少'.$amount.'美元';
                        capital_flow($userInfo->id,$gamelog->gid,3,$money_type,$amount,$userbalance,$content,$admin_content,$gamelog['id']);
                    }
                    $redis->set("user_wait_recapture_{$userInfo->id}",0);
                    $this->success(lang('system.operation_succeeded'));
                }else{
                    $this->error(lang('game.synchronizing_funds'));
                }
            }
            Db::commit();
        }catch (HttpResponseException $e){
            $re = $e->getResponse();
            Db::commit();
            throw new HttpResponseException($re);
        }catch (\Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }
        //查看最后一个游戏得记录

        exit;
    }
    /**
     * 资金回笼到平台--用户手动操作
     *
     * @return \think\Response
     */
    public function user_recapture(){
        $userInfo=$this->request->userInfo;
        //没有回笼资金的以玩游戏
        $gamelog=$this->GamelogModel->where(['uid'=>$userInfo->id,'type'=>1])->where('amount','>',0)->order('id desc')->select();
        if(count($gamelog)>0){
            foreach($gamelog as $k=>$v){
                //逐个查询游戏的资金情况
                $result=$this->apigame->get_balance($userInfo->game_account,$v->productType);
                $ret=json_decode($result,true);
                if($ret['status']==0 && $ret['balance']>0){
                    //将资金回笼到平台
                    $result=$this->apigame->user_all_transfer($userInfo->game_account,$game_info->productType,"game_withdrawal_".$userInfo->game_account.time());
                    $ret=json_decode($result,true);
                    if($ret['status']==0){
                        $balance=$ret['amount'];
                        $this->UserModel->where('id',$userInfo->id)->update(['balance'=>$balance]);
                        $this->GamelogModel->update(['type'=>2,'id'=>$v->id]);

                        if($v->amount>$balance){
                            //用户输的情况
                            $money_type=2;
                            $amount=$v->amount-$balance;
                            $userbalance=$v->amount-$amount;
                            $content='{capital.gamecontento}'.$game_name.'{capital.gamecontentt}'.$amount.'{capital.money}';
                            $admin_content='用户'.$userInfo->nickname.'游玩游戏'.$game_name.'资金增加'.$amount.'美元';
                        }elseif($balance>$v->amount){
                            //用户赢的情况
                            $money_type=1;
                            $amount=bcadd($balance."",-$v->amount."",2);
                            $userbalance=bcadd($v->amount."",$amount."",2);
                            $content='{capital.gamecontento}'.$game_name.'{capital.gamecontenth}'.$amount.'{capital.money}';
                            $admin_content='用户'.$userInfo->nickname.'游玩游戏'.$game_name.'资金减少'.$amount.'美元';
                        }elseif($balance==$v->amount){
                            // 用户没赢没输的情况
                            $money_type=0;
                            $amount=0;
                            $userbalance=$v->amount;
                            $content='{capital.gamecontento}'.$game_name.'{capital.gamecontentf}';
                            $admin_content='用户'.$userInfo->nickname.'游玩游戏'.$game_name.'资金不变';
                        }
                        capital_flow($userInfo->id,$v->gid,3,$money_type,$amount,$userbalance,$content,$admin_content);
                    }
                }
            }
            $userone=$this->UserModel->where('id',$userInfo->id)->find();
            $this->success(lang('system.operation_succeeded'),$userone->balance);
        }else{
            $this->error(lang('game.no_funds_synchronized'));
        }
    }
    /**
     * 展示用户的游戏记录
     *
     * @return \think\Response
     */
    public function user_game_log(){
        $type=input('type/d');
        $pageNum=input('pageNum/d');
        $pageSize=input('pageSize/d');
        $range=input('range');
        // $range[0]=strtotime($range[0]);
        // $range[1]=strtotime($range[1]);
        $userInfo=$this->request->userInfo;
        if($type<5){
            $gametype=$this->gametype[$type];
            $gamelog_count=$this->GamelogModel->withJoin([
                'gamelist'=>['gameName','gameImage','tcgGameCode']
            ])->where(['trialSupport'=>1,'uid'=>$userInfo['id'],'gameType'=>$gametype])
                ->whereTime('gamelog.add_time','between',$range)->count();

            $gamelog_list=$this->GamelogModel->withJoin([
                'gamelist'=>['gameName','gameImage','tcgGameCode']
            ])->where(['trialSupport'=>1,'uid'=>$userInfo['id'],'gameType'=>$gametype])
                ->whereTime('gamelog.add_time','between',$range)
                ->page($pageNum)
                ->limit($pageSize)
                ->order('id desc')
                ->select();
            foreach($gamelog_list as $k=>$v){
                $v->gamelist->gameName=json_decode($v->gamelist->gameName,true)[$this->gamelang];
                $v->gamelist->gameImage=json_decode($v->gamelist->gameImage,true)[$this->gamelang];
                $v->userinfo=$userInfo;
                $v->add_time=date('Y-m-d H:i:s',$v->add_time);
            }
            $data['totalSize']=$gamelog_count;
            $data['totalPage']=ceil($gamelog_count/$pageSize);
            if($pageNum<$data['totalPage']){
                $data['hasNext']=true;
            }else{
                $data['hasNext']=false;
            }
            $data['game_list']=$gamelog_list;
            $this->success(lang('system.success'),$data);
        }else{
            $this->error(lang('system.id'));
        }
    }
    /**
     * 显示游戏列表
     *
     * @return \think\Response
     */
    public function ftplist(){
        // $this->ftpgame = new \app\common\game\FtpGame;//游戏FTP接口
        // $ftplist=$this->ftpgame->nlist();
    }

    /**
     * 显示游戏列表
     *
     * @return \think\Response
     */
    public function gamelist()
    {
        $type=input('type/d');//分类ID
		$pageNum=input('pageNum/d');//每页显示数量
		$pageSize=input('pageSize/d');//页码
		$gameName=input('gameName');//游戏名称
		$productType=input('productType');//产品代码
		$collection_str=null;
		$otherwhere=[];
		if($type<6){
			$userInfo=$this->nologuserinfo;
			$cache_name="gamelist_".$pageNum;
			if($userInfo){
				$cache_name.='_'.$userInfo->game_account;
				$collectionlist=$this->GamecollectionModel->where('uid',$userInfo->id)->select();
				foreach($collectionlist as $key=>$value){
					if($key<count($collectionlist)-1){
						$collection_str.=$value->gid.",";
					}else{
						$collection_str.=$value->gid;
					}
				}
			}
			$where="displayStatus=1";
			// $statusgamebrand=$this->statusgamebrand();
			// if($statusgamebrand){
			// 	$where.=" and productType not in (".$statusgamebrand.")";
			// }
			if($type>0){
				$where.=" and gameType='".$this->gametype[$type-1]."'";
				$cache_name.="_".$this->gametype[$type-1];
			}
			if($gameName){
				$where.=" and gameName->'$.".$this->gamelang."' like '%$gameName%'";
				$gameName=str_replace('"','',json_encode($gameName));
				$gameName.="_".$gameName;
			}
			if($productType){
				$where.=" and productType = '$productType'";
				$cache_name.="_".$productType;
			}
			$cache=cache($cache_name);
			// if(!$cache){
			// 	cache($cache_name,$game_list);
			// }else{
			// 	$game_list=$cache;
			// }
			$game_count=Db::query('select count(*) as gamecount from mk_gamelist where '.$where);
			$game_count=$game_count[0]['gamecount'];
			$data['totalSize']=$game_count;
			$data['totalPage']=ceil($game_count/$pageSize);
			if($pageNum<$data['totalPage']){
				$data['hasNext']=true;
			}else{
				$data['hasNext']=false;
			}
			$game_list=Db::query('select *,if(groom_sort is null,2000000,groom_sort) as groom_sorts from mk_gamelist where '.$where.' ORDER BY groom_sorts asc,hot desc limit '.($pageNum-1)*$pageSize.','.$pageSize);
			foreach($game_list as $k=>$v){
				$gameName=json_decode($v['gameName'],true);
				$game_list[$k]['gameName']=croppstring($gameName[$this->gamelang],6);
				$gameImage=json_decode($v['gameImage'],true);
				if($this->gamelang=='KO'){
					$game_list[$k]['gameImage']=$gameImage['EN'];
				}elseif($this->gamelang=='MS'){
					$game_list[$k]['gameImage']=$gameImage['ID'];
				}else{
					$game_list[$k]['gameImage']=$gameImage[$this->gamelang];
				}
				$game_list[$k]['type']="heart";
				if($collection_str){
					if(strpos($collection_str,(string)$v['id'])!==false){
						$game_list[$k]['type']="heart-filled";
					}
					// foreach($collectionlist as $key=>$value){
					// 	dump($v['id']);dd();
					// 	if($v['id']==$value->gid){
					// 		$game_list[$k]['type']="heart-filled";
					// 	}
					// }
				}
			}
			$data['game_list']=$game_list;
			$this->success(lang('system.success'),$data);
		}else{
			$this->error(lang('system.id'));
		}
    }
    /**
     * 获取屏蔽的游戏品牌列表
     *
     * @return \think\Response
     */
    public function statusgamebrand(){
        $branlist=$this->GameBrandModel->field('`name`,`code`,`logo`')->whereOr([
            [
                ['RNG_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',0]
            ],
            [
                ['LOTT_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',0]
            ],
            [
                ['LIVE_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',0]
            ],
            [
                ['FISH_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',0]
            ],
            [
                ['PVP_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',0]
            ],
            [
                ['SPORT_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',0]
            ],
            [
                ['ESPORT_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',0]
            ]
        ])->select();
        $branstr=null;
        foreach($branlist as $k=>$v){
            if($k+1==count($branlist)){
                $branstr=$branstr.$v->code;
            }else{
                $branstr=$branstr.$v->code.",";
            }
        }
        return $branstr;
    }
    /**
     * 获取游戏品牌列表
     *
     * @return \think\Response
     */
    public function gamebrand()
    {
        $branlist=$this->GameBrandModel->field('`name`,`code`,`logo`')->whereOr([
            [
                ['RNG_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',1]
            ],
            [
                ['LOTT_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',1]
            ],
            [
                ['LIVE_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',1]
            ],
            [
                ['FISH_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',1]
            ],
            [
                ['PVP_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',1]
            ],
            [
                ['SPORT_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',1]
            ],
            [
                ['ESPORT_totalCount','>',0],
                ['logo','<>','null'],
                ['status','=',1]
            ]
        ])->select();
        // echo $this->GameBrandModel->getLastSql();exit;

        $this->success(lang('system.success'),$branlist);
    }
    /**
     * 获取用户的游戏流水
     *
     * @return \think\Response
     */
    public function game_water(){
        // $this->ftpgame = new \app\common\game\FtpGame;//游戏FTP接口
        // $this->ftpgame->nlist();
    }
    /**
     * 获取品牌游戏的总数量
     *
     * @return \think\Response
     */
    public function getgamecount(){
        $brandlist=$this->GameBrandModel->where('gametype','like','%LOTT%')->select();
        foreach($brandlist as $v){
            // $result=$this->apigame->getGameList($v->code,'all','all','ELOTT',0,10);
            $result=$this->apigame->getGameList('EN',$v->code,'all','all','ESPORT',0,10);
            $ret  = json_decode($result,true);
            if($ret['status']==0 && $ret['games']!=null){
                // dump($ret);
                // $this->GameBrandModel->where('id',$v->id)->update(['SPORT_totalCount'=>$ret['page_info']['totalCount'],'addtime'=>time()]);
            }
        }
        exit;
        return json(['code'=>0,'msg'=>'更新成功']);
    }

    /**
     * 获取游戏列表
     *
     * @return \think\Response
     */
    public function getgamelist()
    {
        $gametype=input("get.gametype");
        $code=input("get.code");
        $gamelang=config('lang.game_lang_list');
        // $this->GameListModel->delete(true);
        // exit;
        // $result=$this->apigame->getGameList('EN',4,'html5','all','LIVE',1,10);
        // $ret  = json_decode($result,true);
        // dd($ret);
        // $list=cache('brandlist');
        $totalCount=$gametype.'_totalCount';
        $brandone=$this->GameBrandModel->where([['code','=',$code],['gametype','like','%'.$gametype.'%']])->find();
        $brandone->pagenum=(int)ceil($brandone->$totalCount/10);
        $gamelists=[];
        for($i=1;$i<=$brandone->pagenum;$i++){
            // $result=$this->apigame->getGameList('EN',$brandone->code,'all','all',$gametype,$i,10);
            $result=$this->apigame->getGameList('EN',$brandone->code,'all','all','SPORTS',$i,10);
            $ret  = json_decode($result,true);
            if($ret['games']){
                foreach($ret['games'] as $k=>$v){
                    $v['gameImage']=json_encode([
                        'EN'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/EN/'.$v['tcgGameCode'].'.png',
                        'TH'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/TH/'.$v['tcgGameCode'].'.png',
                        'VI'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/VI/'.$v['tcgGameCode'].'.png',
                        'ID'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/ID/'.$v['tcgGameCode'].'.png',
                        'KM'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/KM/'.$v['tcgGameCode'].'.png',
                        'MS'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/MS/'.$v['tcgGameCode'].'.png',
                        'JA'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/JA/'.$v['tcgGameCode'].'.png',
                        'KO'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/KO/'.$v['tcgGameCode'].'.png'
                    ]);
                    if($v['displayStatus']==0){
                        $v['displayStatus']=1;
                    }else{
                        $v['displayStatus']=0;
                    }
                    $v['add_time']=time();
                    $v['gameType']=$gametype;
                    unset($v['gameName']);
                    $gamelists[]=$v;
                }
            }
        }
        // $gamelist=[];
        // foreach($brandlist as $key=>$value){
        // 	$list=cache('brandone'.$key);
        // 	// dump($list);
        // 	if($list==null){
        // 		$data=[];
        // 		foreach($brandlist as $k=>$v){
        // 			$data['name']=$v->name;
        // 			$data['code']=$v->code;
        // 			$data['totalCount']=$v->RNG_totalCount;
        // 			$data['pagenum']=(ceil($v->RNG_totalCount/10));
        // 			cache('brandone'.$k,$data,3600);
        // 			$gamelist[]=$data;
        // 		}
        // 	}else{
        // 		$gamelist[]=$list;
        // 	}
        // }
        // for($i=1;$i<=(int)$gamelist[$gamekey]['pagenum'];$i++){
        // 	$result=$this->apigame->getGameList('EN',$gamelist[$gamekey]['code'],'html5','all','LIVE',$i,10);
        // 	$ret  = json_decode($result,true);
        // 	if($ret['games']){
        // foreach($ret['games'] as $k=>$v){
        // 	$v['gameImage']=json_encode([
        // 		'EN'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/EN/'.$v['tcgGameCode'].'.png',
        // 		'TH'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/TH/'.$v['tcgGameCode'].'.png',
        // 		'VI'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/VI/'.$v['tcgGameCode'].'.png',
        // 		'ID'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/ID/'.$v['tcgGameCode'].'.png',
        // 		'KM'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/KM/'.$v['tcgGameCode'].'.png',
        // 		'MS'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/MS/'.$v['tcgGameCode'].'.png',
        // 		'JA'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/JA/'.$v['tcgGameCode'].'.png',
        // 		'KO'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/KO/'.$v['tcgGameCode'].'.png'
        // 	]);
        // 	$v['add_time']=time();
        // 	unset($v['gameName']);
        // 	$gamelists[]=$v;
        // }
        // 	}
        // }
        // $this->GameListModel->delete(true);
        // $this->GameListModel->insertAll($gamelists);
        // dump($gamelists);
        exit;
    }

    /**
     * 更改游戏的名字.
     *
     * @return \think\Response
     */
    public function updategamename(){
        $gamekey=input("get.gamekey/d");
        // $gamelang=config('lang.game_lang_list');
        $gamelang=input("get.gamelang");
        // $list=cache('brandlist');
        // $gameone=$this->GameListModel->where('productType',$gamekey)->update(['gameName'=>null]);
        // exit;
        // $brandlist=$this->GameBrandModel->where([['code','=',$gamekey],['ESPORT_totalCount','>',0]])->find();
        // $pagenum=(int)ceil($brandlist->ESPORT_totalCount/10);
        // for($i=1;$i<=$pagenum;$i++){
        // 	$result=$this->apigame->getGameList($gamelang,$brandlist->code,'all','all','SPORTS',$i,10);
        // 	$ret  = json_decode($result,true);
        // 	if($ret['games']){
        // 		foreach($ret['games'] as $k=>$v){
        // 			$gameone=$this->GameListModel->field('`gameName`,`tcgGameCode`')->where(['productType'=>$gamekey,'tcgGameCode'=>$v['tcgGameCode'],'gameType'=>'ESPORT'])->find();
        // 			if($gameone){
        // 				if($gameone->gameName==null){
        // 					$name=json_encode([$gamelang=>$v['gameName']]);
        // 				}else{
        // 					if($gameone->tcgGameCode==$v['tcgGameCode']){
        // 						$name  = json_decode($gameone->gameName,true);
        // 						$name[$gamelang]=$v['gameName'];
        // 						$name=json_encode($name);
        // 					}
        // 				}
        // 				$this->GameListModel->where(['productType'=>$gamekey,'tcgGameCode'=>$v['tcgGameCode']])->update(['gameName'=>$name]);
        // 			}
        // 		}
        // 	}
        // }
        // exit;
    }

    /**
     * 判断可进入的游戏.
     *
     * @return \think\Response
     */
    public function opengameurl(){
        $page=cache('opengame_page')?cache('opengame_page'):1;
        $getcount=$this->GameListModel->count();
        $getlist=$this->GameListModel->page(1)->limit(10)->order('id asc')->select();
        $gameurl=[];
        $game=[];
        foreach($getlist as $k=>$v){
            $result=$this->apigame->getLaunchGameRng('mwzrmlpx','Ferghana horse',$v->productType,1,$v->tcgGameCode,'html5','EN');
            $ret=json_decode($result,true);
            array_push($gameurl,$ret['game_url']);
            $game[$k]['id']=$v->id;
            $game[$k]['url']=$ret['game_url'];
        }
        //多线程访问
        $ql = QueryList::getInstance();
        $ql->use(CurlMulti::class);
        //or Custom function name
        $ql->use(CurlMulti::class,'curlMulti');

        $ql->curlMulti($gameurl)
            ->success(function (QueryList $ql,CurlMulti $curl,$r) use($game){
                // echo "Current url:{$r['info']['url']}</br>";
                foreach($game as $k=>$v){
                    if($v['url']==$r['info']['url']){
                        $this->GameListModel->where('id',$v['id'])->update(['is_visit'=>1]);
                    }
                }
                //释放资源
                QueryList::destructDocuments();
            })->error(function ($errorInfo,CurlMulti $curl) use($game){
                // echo "Current url:{$errorInfo['info']['url']}</br>";
                foreach($game as $k=>$v){
                    if($v['url']==$r['info']['url']){
                        $this->GameListModel->where('id',$v['id'])->update(['is_visit'=>0]);
                    }
                }
                // print_r($errorInfo['error']);
            })->start([
                // 最大并发数，这个值可以运行中动态改变。
                'maxThread' => 10,
                // 触发curl错误或用户错误之前最大重试次数，超过次数$error指定的回调会被调用。
                'maxTry' => 3,
                // 全局CURLOPT_*
                'opt' => [
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_CONNECTTIMEOUT => 1,
                    CURLOPT_RETURNTRANSFER => true
                ],
                // 'cache' => ['enable' => false, 'compress' => false, 'dir' => null, 'expire' =>86400, 'verifyPost' => false]
            ]);
        $allpage=ceil($getcount/10);
        if($page<$allpage){
            Cache::inc('opengame_page');
            // cache('opengame_page',$page+1);
        }else{
            Cache::delete('opengame_page');
        }
    }
}
