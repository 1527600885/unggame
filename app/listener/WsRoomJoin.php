<?php
declare (strict_types = 1);

namespace app\listener;

use think\Container;
use think\swoole\Websocket;
use think\swoole\websocket\Room;

class WsRoomJoin
{
    public $websocket = null;

    public function __construct(Container $container)
    {
        $this->websocket = $container->make(Websocket::class);
        $this->room = $container->make(Room::class);
    }
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {
        $this->websocket->join($event['room']);
        $this->websocket->to($event['room'])->emit("joincallback", ['msg' => '欢迎客户端编号:'.$this->websocket->getSender().'加入到房间。','client_id' => $this->websocket->getSender()]);
    }
}