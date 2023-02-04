<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use think\facade\Cache;
use Hashids\Hashids;
use think\facade\Session;
use think\facade\Db;

use think\Request;
class Rate extends BaseController
{
	protected $noNeedLogin = ['*'];
	public function initialize(){
		$this->NoticeModel = new \app\api\model\Notice;//游戏公告
	}
}
