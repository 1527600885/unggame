<?php

namespace app\api\controller;

use app\api\model\v2\ChatKefuUser;
use app\api\model\v2\ChatRecord;
use app\api\model\v2\ChatUser;
use app\common\lib\Redis;
use think\Request;
use app\api\BaseController;
use think\facade\Db;
class Customer extends BaseController
{
	protected $noNeedLogin = [];
	public function initialize(){
		parent::initialize();
		$this->CustomerLogModel = new \app\api\model\MkCustomerLog;//聊天记录
		$this->CustomerSetModel = new \app\api\model\MkCustomerSet;//客服
		$this->CustomerPropagandaModel = new \app\api\model\MkCustomerPropaganda;//客服页面宣传语录
	}
	
//	//获取客服相关的信息
//	public function customerlist(){
//		$data['userInfo']=$this->request->userInfo;
//		//获取客服相关语录
//		$data['Propaganda']=$this->CustomerPropagandaModel->where('status',1)->order('id asc')->select();
//		//随机获取在线的客服信息
//		$data['customer']=$this->CustomerSetModel->where('status',1)->whereNotNull('client_id')->extra('rand()')->find();
//
//		$this->success(lang('system.success'),$data);
//	}
    //获取客服相关的信息
    public function customerlist(){
        $data['userInfo']=$this->request->userInfo;
//        //获取客服相关语录
//        $data['Propaganda']=$this->CustomerPropagandaModel->where('status',1)->order('id asc')->select();
        //随机获取在线的客服信息
        $kefu_id = ChatKefuUser::where("fid",$this->request->userInfo['game_account'])->where("status",1)->value("kefu_id");
        if($kefu_id){
            $data['customer']=ChatUser::where("name",$kefu_id)->find();
        }else{
            $redis = (new Redis())->getRedis();
            $date = date("Y-m-d");
            $kefu_id = $redis->sPop("kefu_list_{$date}");
            if(!$kefu_id){
                $kefu_id_list =  ChatUser::where("state",0)->column("name");
                foreach ($kefu_id_list as $v)
                {
                    $redis->sAdd("kefu_list_{$date}",$v);
                }
                $redis->expire("kefu_list_{$date}",24*60*60);
                $kefu_id = $redis->sPop("kefu_list_{$date}");
            }
            $data['customer']=ChatUser::where("name",$kefu_id)->find();
            ChatKefuUser::create(["fid"=>$this->request->userInfo['game_account'],"kefu_id"=>$data['customer']['name']]);

        }
        $count =  ChatRecord::where("ftoid",$this->request->userInfo['game_account'])->where("foid",$data['customer']['name'])
            ->count();
        if ($count > 200) {
            $count = $count - 200;
        } else {
            $count = 0;
        }
        ChatRecord::where("ftoid",$this->request->userInfo['game_account'])
            ->where("foid",$data['customer']['name'])
            ->where("state",0)
            ->update(['state'=>1]);
        $data['chat_record'] = ChatRecord::where("ftoid",$this->request->userInfo['game_account'])
            ->where("foid",$data['customer']['name'])
            ->limit($count,200)
            ->order("id asc")
            ->select();
        $this->success(lang('system.success'),$data);
    }
    public function saveMessage()
    {
        $data = input("post.");
        $message['fid']=$data['room_id'];
        $message['toid']=$data['toid'];
        $message['content']=$data['content'];
        $message['time']=$data['time'];
        $message['isonline']=$data['isonline'];
        $message['type'] =$data['type'];
        $message['foid'] =$data['toid'];
        $message['ftoid'] =$data['room_id'];
        $message['source_type'] = $data['source_type'] ?? 1;
        ChatRecord::create($message);
        $this->success("success");
    }
    public function chatRecord()
    {
        $kefu_id = ChatKefuUser::where("fid",$this->request->userInfo['game_account'])->where("status",1)->value("kefu_id");
        if(!$kefu_id){
            $redis = (new Redis())->getRedis();
            $date = date("Y-m-d");
            $kefu_id = $redis->sPop("kefu_list_{$date}");
            if(!$kefu_id){
                $kefu_id_list =  ChatUser::where("state",0)->column("name");
                foreach ($kefu_id_list as $v)
                {
                    $redis->sAdd("kefu_list_{$date}",$v);
                }
                $redis->expire("kefu_list_{$date}",24*60*60);
                $kefu_id = $redis->sPop("kefu_list_{$date}");
            }
            ChatKefuUser::create(["fid"=>$this->request->userInfo['game_account'],"kefu_id"=>$kefu_id]);
        }
        $lists = ChatRecord::where("ftoid",$this->request->userInfo['game_account'])->where("foid",$kefu_id)->paginate(12);
        $this->success("success",$lists);
    }
    public function readMessage()
    {
        ChatRecord::where("ftoid",$this->request->userInfo['game_account'])
            ->where("state",0)
            ->update(['state'=>1]);
    }
}
