<?php
	declare (strict_types = 1);
	namespace app\api\controller;
	use app\api\BaseController;
	use think\facade\Cache;
	use think\facade\Db;
	use app\common\lib\Redis;
	use app\api\validate\User as UserValidate;
	class DayDividend extends BaseController{
			public function initialize(){
			parent::initialize();
			$this->DayDividendModel = new \app\api\model\DayDividend;//虚拟币设置
		}
		public function getlist()
		{
			$datalist = $this->DayDividendModel->where()->limit(5)->order('date','desc');
			$this->success(lang('system.success'),$datalist);
		}
	}
?>