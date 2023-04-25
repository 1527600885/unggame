<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use app\api\model\GameBrand;
use app\api\model\GameList;
use app\common\game\ApiGame;
use app\common\lib\pay\AxPay;
use app\common\lib\pay\HtPay;
use app\common\lib\pay\JmPay;
use app\common\lib\pay\OePay;
use app\common\lib\pay\SurePay;
use app\common\lib\pay\TopPay;
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
        $model = new SurePay("MYR");
        $model->run("",[
            "trade_amount"=>"100.00",
            "mch_order_no"=>"order".time().rand(100,999),
            "token"=>"6c1a65ba0bfc16c821a51a59d8f45458",
            "customer"=>"cust001",
            "currency"=>"MYR",
            "bankcode"=>"10002493",
        ]);
    }
    public function testAx(){
        $model = new AxPay("INR");
        $model->transfer([
            "mch_transferId"=>"withdraw".time().rand(100,999),
            "transfer_amount"=>1000.00,
            "receive_name"=>"阿三哥",
            "receive_account"=>"12312",
            "ifsc"=>"aaabb0"
        ]);
    }
    public function testJm()
    {
        $model = new JmPay("bdt");
        $model->run("",[
            "trade_amount"=>"100.00",
            "mch_order_no"=>"order".time().rand(100,999),
        ]);
    }
    public function testTop()
    {
        $model = new TopPay("IDR");
        $model->run("",[
            "trade_amount"=>"100.00",
            "mch_order_no"=>"order".time().rand(100,999),
        ]);
    }
    public function testOepay()
    {
        $model = new OePay("IDR");
        $model->run("",[ "trade_amount"=>"100.00",
            "mch_order_no"=>"order".time().rand(100,999)]);
    }
    public function testHtpay()
    {
        $model = new HtPay("PHP");
        $model->run("",[ "trade_amount"=>"100.00",
            "mch_order_no"=>"order".time().rand(100,999)]);
    }
    public function testGetGame()
    {
        $game = new ApiGame();
        $lists = GameBrand::select();
        foreach ($lists as $v){
            $gameTypeList = explode(",",$v['gametype']);
            foreach ($gameTypeList as $vv){
               $ret = $game->getGameList("EN",$v->code,"all","all",$vv,1,10);
               if($ret['games']){
                    foreach($ret['games'] as $k=>$v){
                        $v['gameImage']=json_encode([
                            'EN'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/EN/'.$v['tcgGameCode'].'.png',
                            'TH'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/TH/'.$v['tcgGameCode'].'.png',
                            'VI'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/VI/'.$v['tcgGameCode'].'.png',
                            'ID'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/ID/'.$v['tcgGameCode'].'.png',
                            'KM'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/KM/'.$v['tcgGameCode'].'.png',
                            'MS'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/MS/'.$v['tcgGameCode'].'.png',
                            'JA'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/JA/'.$v['tcgGameCode'].'.png',
                            'KO'=>'https://images.b51613.com:42666/TCG_GAME_ICONS/'.$v['productCode'].'/KO/'.$v['tcgGameCode'].'.png'
                        ]);
                        if($v['displayStatus']==0){
                            $v['displayStatus']=1;
                        }else{
                            $v['displayStatus']=0;
                        }
                        $v['add_time']=time();
                        $v['gameType']=$vv;
                        unset($v['gameName']);
                        $gamelists[]=$v;
                    }
                }
            }
            break;
        }
        var_dump($gamelists);
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
