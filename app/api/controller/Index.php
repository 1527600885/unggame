<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use app\api\model\GameBrand;
use app\api\model\GameList;
use app\common\game\ApiGame;
use app\common\lib\Game\SlotsGame;
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
        $file = file_get_contents(__DIR__."/s.txt");
        $data = json_decode($file,true);
        foreach ($data['value'] as $v){
            $isExit = GameList::where("tcgGameCode",$v['roomId'])->find();
            if(!$isExit){
                $list[] = $v;
                $gameName = [
                    "EN" =>$v['description'],
                    "TH" =>$v['description'],
                    "VI" =>$v['description'],
                    "ID" =>$v['description'],
                    "KM" =>$v['description'],
                    "MS" =>$v['description'],
                    "JA" =>$v['description'],
                    "KO" =>$v['description']
                ];
                $gameImage = [
                    "EN" =>$v['showIcon'],
                    "TH" =>$v['showIcon'],
                    "VI" =>$v['showIcon'],
                    "ID" =>$v['showIcon'],
                    "KM" =>$v['showIcon'],
                    "MS" =>$v['showIcon'],
                    "JA" =>$v['showIcon'],
                    "KO" =>$v['showIcon']
                ];
                $productType = GameList::where("productCode",$v['vassalage'])->value("productType");
                $save[] = [
                    "displayStatus"=>$v['displayStatus'],
                    "gameType"=>$v['gameType'],
                    "gameName"=>json_encode($gameName),
                    "gameImage"=>$gameImage,
                    "tcgGameCode"=>$v['roomId'],
                    "productCode"=>$v['vassalage'],
                    "productType"=>$productType,
                    "platform"=>$v['platform'],
                    "trialSupport"=>$v['supportPlay'],
                    "add_time"=>time(),
                ];
            }
        }
        $this->success("success",compact("list"));
    }
    public function testSlots()
    {
        $game = new SlotsGame();
        $data = $game->getResult();
        $this->success("success",compact("data"));
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
