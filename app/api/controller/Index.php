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
        ### 連接的 FTP 伺服器
        $conn_id = ftp_connect('123.51.167.66');

        ### 登入 FTP, 帳號是 USERNAME, 密碼是 PASSWORD
        $login_result = ftp_login($conn_id, 'nicedoidrk', 'a123456');


        // 主目录列表
        $contents = ftp_nlist($conn_id, ".");
        var_dump($contents);
        echo "<br>";
//        $service = new \app\common\game\ApiGame();
//        $username = "xgpdvbpn";
//        $start_date = "2023-02-09 00:00:00";
//        $end_date = "2023-02-10 00:00:00";
//        $page = 1;
//        $data = $service->get_bet_details_member($username, $start_date, $end_date, $page);
//        var_dump($data);
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
        die();
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
