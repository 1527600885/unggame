<?php
declare (strict_types = 1);

namespace app\listener;
use think\Container;
use think\swoole\Websocket;
use think\swoole\websocket\Room;
class WsOnopen
{
    public $websocket = null;

    public function __construct(Container $container)
    {
        $this->websocket = $container->make(Websocket::class);
        $this->room = $container->make(Room::class);
		// $this->pingInterval = $this->config->get('swoole.websocket.ping_interval', 25000);
		// $this->pingTimeout  = $this->config->get('swoole.websocket.ping_timeout', 60000);
    }
    /**
     * 事件监听处理
     *
     * @return mixed
     * 受用 WebSocket 客户端连接入口
     */
    public function handle($event)
    {
		$data = json_encode(
            [
				'msg'=>'你好'
            ]
        );
		$this->websocket->push($data);
		// $this->websocket->broadcast()->emit('connectcallback',$data);
    }
}