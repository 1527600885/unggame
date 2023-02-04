<?php
namespace app\http;
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 */
use GatewayWorker\Lib\Gateway;
use app\api\BaseController;
use think\Request;
use app\api\model\MkCustomerLog as CustomerLogModel;
use app\api\model\User as UserModel;
use app\api\model\MkCustomerSet as CustomerSetModel;
use app\admin\model\Admin as AdminModel;
use app\api\model\WssOnline as WssOnlineModel;
use think\facade\Session;

class Events extends BaseController
{
	protected $noNeedLogin = [];
	public function initialize(){
		parent::initialize();
		// $this->CustomerLogModel = new \app\api\model\MkCustomerLog;//聊天记录
		// $this->CustomerSetModel = new \app\api\model\MkCustomerSet;//客服
		// $this->CustomerPropagandaModel = new \app\api\model\MkCustomerPropaganda;//客服页面宣传语录
	}
	
   
   /**
    * 有消息时
    * @param int $client_id
    * @param mixed $message
    */
   public static function onMessage($client_id, $message)
   {
	    // $userInfo=$this->request->userInfo;
        // debug
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";
        
        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if(!$message_data)
        {
            return ;
        }
        
        // 根据类型执行不同的业务
        switch($message_data['type'])
        {
            // 客户端回应服务端的心跳
            case 'pong':
                return;
            // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
            case 'login':
                // 判断是否有房间号
                if(!isset($message_data['room_id']))
                {
                    throw new \Exception("\$message_data['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
                }
                
                // 把房间号昵称放到session中
                $room_id = $message_data['room_id'];
                $client_name = htmlspecialchars($message_data['client_name']);
                $_SESSION['room_id'] = $room_id;
                $_SESSION['client_name'] = $client_name;
                $clients_list = Gateway::getClientSessionsByGroup($room_id);
				
				if($message_data['identity']==1){
					//普通用户登录
					$userinfo=UserModel::field('`id`,`nickname`,`cover`')->where('id',$message_data['uid'])->find();
					$customerLog=CustomerLogModel::where(['to_id'=>$message_data['uid'],'room_id'=>$message_data['room_id']])->page(1)->limit(10)->select();
					$oldmsg=[];
					foreach($customerLog as $k=>$v){
						if($v->to_id){
							$oldmsg[$k]['type']='to';
							$oldmsg[$k]['class']='to-user-msg';
						}else{
							$oldmsg[$k]['type']='form';
							$oldmsg[$k]['class']='from-user-msg';
						}
						$oldmsg[$k]['msg']=$v->content;
						$oldmsg[$k]['uid']=$v->to_id;
					}
					//记录上线
					$onlinedata['type']=1;
					// WssOnlineModel::
					// 每次重连，切换可用的客服
					$customer=CustomerSetModel::whereNotNull('client_id')->extra('rand()')->find();
				}else{
					//客服登录
					$oldmsg='';
					$customer='';
					CustomerSetModel::where('admin_account',$message_data['account'])->update(['client_id'=>$client_id]);
					$onlinedata['type']=2;
					$userinfo=CustomerSetModel::field('id')->where('admin_account',$message_data['account'])->find();
					// $userinfo=CustomerSetModel::alias('a')
					// ->join('')
					foreach($clients_list as $tmp_client_id=>$item)
					{
					    // $clients_list[$tmp_client_id] = $item['client_name'];
						$useone=WssOnlineModel::alias('w')
						->join('mk_user u','w.uid=u.id')
						->where(['w.type'=>1,'w.client_id'=>$tmp_client_id])
						->find();
						if($useone){
							$clients_list[$tmp_client_id]['cover']=$useone->cover;
							$clients_list[$tmp_client_id]['uid']=$useone->id;
						}
						$clients_list[$tmp_client_id]['client_id'] = $tmp_client_id;
						$clients_list[$tmp_client_id]['client_name'] = $item['client_name'];
						$clients_list[$tmp_client_id]['type'] = 1;
					}
				}
				$onlinedata['uid']=$userinfo->id;
				$onlinedata['client_id']=$client_id;
				$onlinedata['add_time']=time();
				WssOnlineModel::insert($onlinedata);
				
				$clients_list[$client_id]['client_name']= $client_name;
				$clients_list[$client_id]['client_id']= $client_id;
				$clients_list[$client_id]['type']= 2;
				// 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx} 
                $new_message = array(
				'type'=>$message_data['type'], 
				'client_id'=>$client_id, 
				'client_name'=>htmlspecialchars($client_name),
				'userinfo'=>$userinfo,
				'oldmsg'=>$oldmsg,
				'identity'=>$message_data['identity'],
				'customer'=>$customer,
				'time'=>time()
				);
                Gateway::sendToGroup($room_id, json_encode($new_message));
                Gateway::joinGroup($client_id, $room_id);
               
                // 给当前用户发送用户列表 
				// $_SESSION['client_key']=count($clients_list);
				// dump($clients_list);
                $new_message['client_list'] = $clients_list;
                Gateway::sendToCurrentClient(json_encode($new_message));
                return;
                
            // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
            case 'say':
                // 非法请求
                if(!isset($_SESSION['room_id']))
                {
                    throw new \Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
                }
                $room_id = $_SESSION['room_id'];
                $client_name = $_SESSION['client_name'];
                
				$data=[
					'to_id'=>$message_data['uid'],
					'form_id'=>$message_data['to_client_id'],
					'room_id'=>$message_data['room_id'],
					'type'=>$message_data['type'],
					'identity'=>$message_data['identity'],
					'online_time'=>time(),
					'content'=>nl2br(htmlspecialchars($message_data['content'])),
					'add_time'=>time()
				];
				CustomerLogModel::insert($data);
				$userinfo=UserModel::field('`id`,`nickname`,`cover`')->where('id',$message_data['uid'])->find();
                // 私聊
                if($message_data['to_client_id'] != 'all')
                {
                    $new_message = array(
                        'type'=>'say',
                        'from_client_id'=>$client_id, 
                        'from_client_name' =>$client_name,
                        'to_client_id'=>$message_data['to_client_id'],
						'mine'=> 0,
                        'content'=>nl2br(htmlspecialchars($message_data['content'])),
                        'userinfo'=>$userinfo,
						// 'time'=>date('Y-m-d H:i:s'),
						'time'=>time(),
						'uid'=>$message_data['uid'], 
                    );
                    Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));
                    // $new_message['content'] = "<b>你对".htmlspecialchars($message_data['to_client_name'])."说: </b>".nl2br(htmlspecialchars($message_data['content']));
					$new_message['content'] = nl2br(htmlspecialchars($message_data['content']));
					$new_message['mine'] = 1;
                    return Gateway::sendToCurrentClient(json_encode($new_message));
                }
                
                $new_message = array(
                    'type'=>'say', 
                    'from_client_id'=>$client_id,
                    'from_client_name' =>$client_name,
                    'to_client_id'=>'all',
                    'content'=>nl2br(htmlspecialchars($message_data['content'])),
                    'userinfo'=>$userinfo,
					// 'time'=>date('Y-m-d H:i:s'),
					'time'=>time(),
					'uid'=>$message_data['uid'], 
                );
                return Gateway::sendToGroup($room_id ,json_encode($new_message));
        }
   }
   
   /**
    * 当客户端断开连接时
    * @param integer $client_id 客户端id
    */
   public static function onClose($client_id)
   {
       // debug
       echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
       
       // 从房间的客户端列表中删除
       if(isset($_SESSION['room_id']))
       {
           $room_id = $_SESSION['room_id'];
		   $kefu=WssOnlineModel::where('client_id',$client_id)->find();
		   WssOnlineModel::where('client_id',$client_id)->delete();
		   $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
           if($kefu->type==2){
				//客服退出，清楚在线的客户端id
				CustomerSetModel::where('client_id',$client_id)->update(['client_id'=>null]);
				//给当前链接该客服的客户更换客服
				$customer=CustomerSetModel::whereNotNull('client_id')->extra('rand()')->find();
				$new_message['customer']=$customer;
		   }
		   Gateway::sendToGroup($room_id, json_encode($new_message));
       }
   }
  
}
