<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use Hashids\Hashids;

class Index extends BaseController
{
	protected $noNeedLogin = ['*'];
	protected $noNeedCheckIp = ['error503'];
	public function initialize(){
		parent::initialize();
	}
    public function index()
    {
        $service = new \app\common\game\ApiGame();
        $username = "xgpdvbpn";
        $start_date = "2023-02-09 00:00:00";
        $end_date = "2023-02-10 00:00:00";
        $page = 1;
        $data = $service->get_bet_details_member($username, $start_date, $end_date, $page);
        var_dump($data);
		// $res=exportOpenSSLFile();
		// $res=json_decode($res,true);
		// dd(strlen($res['public']));
		// $publicarr=[];
		// for($i=0;$i<16;$i++){
			
		// }
		// $str=authcode('欢迎您的到来！',$res['public'],$res['private'],"E");
		// $jstr=authcode($str,$res['public'],$res['private']);
		// return $jstr;
		// $salt=createsalt();
		// dump($salt);
    }
    public function error503()
    {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
    }
	//相关的配置
	public function config(){
		// dump(lang('welcome'));exit;
		// $key=cache('key');
		// dd($key);
		// if($key==null){
		// 	$res=exportOpenSSLFile();
		// 	$res=json_decode($res,true);
		// 	cache('key',$res);
		// }else{
		// 	$res=$key;
		// }
		$system_config=[
			'version'=>'1.0.0'
		];
		$this->success(lang('system.success'),$system_config);
	}
}
