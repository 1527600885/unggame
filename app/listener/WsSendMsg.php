<?php
declare (strict_types=1);

namespace app\listener;

use think\Container;
use think\swoole\Websocket;
use think\swoole\websocket\Room;
class WsSendMsg
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
        $this->websocket->broadcast()->emit("sendmsgcallback", $event);
    }
}