<?php
// +----------------------------------------------------------------------
// | 多语言设置
// +----------------------------------------------------------------------

return [
    // 默认语言
    'default_lang'    => env('lang.default_lang', 'en-us'),
    // 允许的语言列表
    'allow_lang_list' => ['en-us','en-id','en-my','ja-jp','km-km','ko-kr','th-th','vi-vn'],
    // 多语言自动侦测变量名
    'detect_var'      => 'lang',
    // 是否使用Cookie记录
    'use_cookie'      => false,
    // 多语言cookie变量
    'cookie_var'      => 'think_lang',
    // 多语言header变量
    'header_var'      => 'think-lang',
    // 扩展语言包
    'extend_list'     => [
		'en-us' => [ //英语
			app()->getAppPath() . 'lang/en-us/common.php',
		],
		'en-id' => [//印尼语
			app()->getAppPath() . 'lang/en-id/common.php',
		],
		'en-my' => [//马来语
			app()->getAppPath() . 'lang/en-my/common.php',
		],
		'ja-jp' => [//日语
			app()->getAppPath() . 'lang/ja-jp/common.php',
		],
		'km-km' => [//柬埔寨语
			app()->getAppPath() . 'lang/km-km/common.php',
		],
		'ko-kr' => [//韩语
			app()->getAppPath() . 'lang/ko-kr/common.php',
		],
		'th-th' => [//泰语
			app()->getAppPath() . 'lang/th-th/common.php',
		],
		'vi-vn' => [//越南语
			app()->getAppPath() . 'lang/vi-vn/common.php',
		]
	],
    // Accept-Language转义为对应语言包名称
    'accept_language' => [
		'zh-hans-cn' => 'zh-cn',
    ],
    // 是否支持语言分组
    'allow_group'     => true,
];
