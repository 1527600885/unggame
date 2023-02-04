<?php
// +----------------------------------------------------------------------
// | 全局中间件定义文件
// +----------------------------------------------------------------------

return [
	// Session初始化
    \think\middleware\SessionInit::class,
    // 环境检测
    app\api\middleware\AppCheck::class,
	// 多语言加载
	\think\middleware\LoadLangPack::class,
	app\api\middleware\AllowCrossDomain::class,
];
