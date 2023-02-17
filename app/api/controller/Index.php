<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use app\api\model\GameList;
use Hashids\Hashids;
use think\App;
use app\api\model\User;
class Index extends BaseController
{
	protected $noNeedLogin = ['*'];
	protected $noNeedCheckIp = ['error503'];
	public function initialize(){
		parent::initialize();
	}
    public function index()
    {
        $app = new App();
        $path = $app->getRootPath();
        $local_path = $path."gamelog";   ### 本機儲存檔案名稱//batchnamejson

        ### 連接的 FTP 伺服器
        $conn_id = ftp_connect('123.51.167.66');
        // echo "aaa"
        // var_dump($conn_id);
        ### 登入 FTP, 帳號是 USERNAME, 密碼是 PASSWORD
        $login_result = ftp_login($conn_id, 'nicedoidrk', 'a123456');
        ftp_pasv($conn_id,TRUE);
        // var_dump($login_result);
        $date = date("Ymd");
        // 主目录列表
        $contents = ftp_nlist($conn_id, "/PVP/SETTLED/{$date}");
        $key = count($contents);
        if($key>0){
            unset($contents[$key-1]);
        }
        $dir = $local_path."/PVP/SETTLED/{$date}";
        if(!is_dir($dir)){
            mkdir($dir,0755);
        }
        $exit = scandir($dir);
        $contents = array_diff($dir,$exit);
        var_dump($contents);die();
        try{
            foreach ($contents as $v){
                $local_file = $local_path.$v;
                $handle = fopen($local_file, 'w');
                if (ftp_fget($conn_id, $handle, $v, FTP_ASCII, 0)) {
                    echo "下載成功, 並儲存到 $local_file\n";
                } else {
                    echo "下載 $remote_file 到 $local_file 失敗\n";
                }
                // fclose($local_file);
            }
        }catch(\Exception $e){
            var_dump($e->getMessage());
        }


        die();
        // print_r($contents);
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
