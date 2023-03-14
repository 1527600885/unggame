<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use app\api\model\GameList;
use app\common\lib\Redis;
use Endroid\QrCode\Writer\PngWriter;
use Hashids\Hashids;
use think\App;
use app\api\model\User;
class Index extends BaseController
{
	protected $noNeedLogin = ['*'];
	protected $noNeedCheckIp = ['error503',"checkip"];
	public function initialize(){
		parent::initialize();
	}
    public function index()
    {
        User::column();
    }
    public function error503()
    {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        die();
    }
    public function checkIp()
    {
        $email = input("param.email");
        $whiteList = [
            "erinmaud69@gmail.com",
            "welsiedavis928@gmail.com",
            "hadleeha565@gmail.com",
            "574995091@qq.com",
            "lyndibrown70@gmail.com",
            "salliemacara882@gmail.com",
            "demiyou999@gamil.com",
            "kiarral107@gmail.com",
            "elliotlucilius69@gmail.com"
        ];
        if(in_array($email,$whiteList)){
            $ip = request()->ip();
            $redis =  (new Redis())->getRedis();
            $redis->set("ip_white_{$ip}",1);
        }
        header("Location: https://www.unggame.com");
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
	public function platformData()
    {
        $data = \app\api\model\Config::getVal("ungconfig");
        $this->success(lang('system.success'),$data);
    }
}
