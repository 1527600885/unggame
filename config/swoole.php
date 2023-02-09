<?php
//// use app\listener\WsSendMsg;
//// use app\listener\WsRoomJoin;
//// use app\listener\WsRoomLeave;
//// use app\listener\WsClose;
//// use app\listener\WsConnect;
//// use app\listener\WsOnopen;
//
//use think\swoole\websocket\socketio\Handler;
//use think\swoole\websocket\socketio\Parser;
//// use app\websocket\privater\Handler;
//// use app\websocket\privater\Packet;
//
//return [
//    'http'       => [
//        'enable'     => true,
//        'host'       => '0.0.0.0',
//        'port'       => 9501,
//        'worker_num' => swoole_cpu_num(),
//        'options'    => [],
//    ],
//    'websocket'  => [
//        'enable'        => true,
//        'handler'       => Handler::class,
//		'parser'        => Parser::class,
//        'ping_interval' => 25000,
//        'ping_timeout'  => 60000,
//        'room'          => [
//            'type'  => 'table',
//            'table' => [
//                'room_rows'   => 8192,
//                'room_size'   => 2048,
//                'client_rows' => 4096,
//                'client_size' => 2048,
//            ],
//            'redis' => [
//                'host'          => '127.0.0.1',
//                'port'          => 6379,
//                'max_active'    => 3,
//                'max_wait_time' => 5,
//            ],
//        ],
//        'listen'        => [
//			// 'onOpen'  => WsOnopen::class,// 握手
//			// 'sendmsg' => WsSendMsg::class,  // 发送消息
//			// 'connect' => WsConnect::class,  // 客户端启动
//			// 'join'  => WsRoomJoin::class, // 加入群组
//			// 'close'  => WsClose::class, // 加入群组
//			// 'leave' => WsRoomLeave::class, // 退出群组
//		],
//        'subscribe'     => [],
//    ],
//    'rpc'        => [
//        'server' => [
//            'enable'     => false,
//            'host'       => '0.0.0.0',
//            'port'       => 9000,
//            'worker_num' => swoole_cpu_num(),
//            'services'   => [],
//        ],
//        'client' => [],
//    ],
//    //队列
//    'queue'      => [
//        'enable'  => true,
//        'workers' => [],
//    ],
//    'hot_update' => [
//        'enable'  => env('APP_DEBUG', false),
//        'name'    => ['*.php'],
//        'include' => [app_path()],
//        'exclude' => [],
//    ],
//    //连接池
//    'pool'       => [
//        'db'    => [
//            'enable'        => true,
//            'max_active'    => 3,
//            'max_wait_time' => 5,
//        ],
//        'cache' => [
//            'enable'        => true,
//            'max_active'    => 3,
//            'max_wait_time' => 5,
//        ],
//        //自定义连接池
//    ],
//	//开启协程
//	'coroutine'  => [
//	    'enable' => true,
//	    'flags'  => SWOOLE_HOOK_ALL,
//	],
//    'tables'     => [
//		'm2fd' => [
//			'size' => 102400,
//			'columns' => [
//				['name' => 'fd', 'type' => \Swoole\Table::TYPE_STRING, 'size' => 50]
//			]
//		],
//		'fd2m' => [
//			'size' => 102400,
//			'columns' => [
//				['name' => 'member_id', 'type' => \Swoole\Table::TYPE_INT]
//			]
//		],
//	],
//    //每个worker里需要预加载以共用的实例
//    'concretes'  => [],
//    //重置器
//    'resetters'  => [],
//    //每次请求前需要清空的实例
//    'instances'  => [],
//    //每次请求前需要重新执行的服务
//    'services'   => [],
//];
