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
			$this->DayDividendModel = new \app\api\model\DayDividend;
		}
		public function getlist()
		{
			$datalist = $this->DayDividendModel->order('date','desc')->limit(5)->select()->each(function($item){
			    $item['date']=date("m-d", $item['date']);
			    return $item;
			});
// 			var_dump($this->DayDividendModel->getLastsql());
			$this->success(lang('system.success'),$datalist);
		}
		public function allgetlist()
		{
			$datalist = $this->DayDividendModel->order('date','desc')->select()->each(function($item){
			    $item['date']=date("m-d", $item['date']);
			    return $item;
			});
// 			var_dump($this->DayDividendModel->getLastsql());
			$this->success(lang('system.success'),$datalist);
		}
	}
?>