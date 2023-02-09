<?php
//// +----------------------------------------------------------------------
//// | soole配置
//// +----------------------------------------------------------------------
//// use think\swoole\websocket\socketio\Handler;
//use app\websocket\privater\Handler;
//
//$listen = [];
//foreach (plugin_list() as $key => $plugin) {
//    $path = plugin_path() . $plugin['name'] . '/listen/websocket';
//    if (is_dir($path)) {
//        $array = scandir($path);
//        if (! empty($array)) {
//            foreach ($array as $key => $value) {
//                if ($value != '.' && $value != '..') {
//                    $fileName = str_replace(strrchr($value, '.'), '', $value);
//                    $fileKey  = strtolower($fileName);
//                    $listen[$fileKey] = "plugins\\" . $plugin['name'] . "\\listen\\websocket\\" . $fileName;
//                }
//            }
//        }
//    }
//}
//return [
//    'server'     => [
//        'host'      => '0.0.0.0', // 监听地址
//        'port'      => 9501, // 监听端口
//        'mode'      => SWOOLE_PROCESS, // 运行模式 默认为SWOOLE_PROCESS
//        'sock_type' => SWOOLE_SOCK_TCP, // sock type 默认为SWOOLE_SOCK_TCP
//        'options'   => [
//            'pid_file'              => runtime_path() . 'swoole.pid',
//            'log_file'              => runtime_path() . 'swoole.log',
//            'daemonize'             => true,
//            // Normally this value should be 1~4 times larger according to your cpu cores.
//            'reactor_num'           => 1,
//            'worker_num'            => 1,
//            'task_worker_num'       => 1,
//            'enable_static_handler' => true,
//            'document_root'         => root_path('public'),
//            'package_max_length'    => 20 * 1024 * 1024,
//            'buffer_output_size'    => 10 * 1024 * 1024,
//            'socket_buffer_size'    => 128 * 1024 * 1024,
//        ],
//    ],
//    'websocket'  => [
//        'enable'        => true,
//        'handler'       => Handler::class,
//        'ping_interval' => 25000,
//        'ping_timeout'  => 75000,
//        'room'          => [
//            'type'  => 'table',
//            'table' => [
//                'room_rows'   => 4096,
//                'room_size'   => 2048,
//                'client_rows' => 8192,
//                'client_size' => 2048,
//            ],
//            'redis' => [
//                'host'          => '127.0.0.1',
//                'port'          => 6379,
//                'max_active'    => 3,
//                'max_wait_time' => 5,
//            ],
//        ],
//        'listen'    => $listen,
//        'subscribe' => [],
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
//    //开启协程
//    'coroutine'  => [
//        'enable' => true,
//        'flags'  => SWOOLE_HOOK_ALL,
//    ],
//    'tables'     => [
//    	'm2fd' => [
//    		'size' => 102400,
//    		'columns' => [
//    			['name' => 'fd', 'type' => \Swoole\Table::TYPE_STRING, 'size' => 50]
//    		]
//    	],
//    	'fd2m' => [
//    		'size' => 102400,
//    		'columns' => [
//    			['name' => 'member_id', 'type' => \Swoole\Table::TYPE_INT]
//    		]
//    	],
//    ],
//];
