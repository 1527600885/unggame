<?php
declare (strict_types = 1);

namespace app\listener;

use think\Container;
use think\swoole\Websocket;

class WsClose
{
    public $websocket = null;

    public function __construct(Container $container)
    {
        $this->websocket = $container->make(Websocket::class);
    }
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {
        // $this->websocket->broadcast()->emit("closecallback", ['msg' => "有人偷偷离开了..."]);
    }
}