<?php


namespace app\common\command;


use app\admin\model\GameList;
use app\api\model\GameBetLog;
use app\api\model\User;
use think\App;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Env;

class GameLog extends Command
{

    protected $gameType = [
        //"ELOTTO",
        "PVP",
        "RNG",
        "SPORTS",
//        "TCG_SEA",
//        "TLOTTO",
//        "TPELOTTO"
    ];
    protected function configure()
    {
        $this->setName('gamelog')
            ->setDescription('get Game bet log');
    }
    protected function execute(Input $input, Output $output)
    {
        $gameType = $this->gameType;
        //和三方建立连接
        $conn_id = ftp_connect(Env::get("GAMECONF.IP"));
        //账号登录
        ftp_login($conn_id, Env::get("GAMECONF.merchantCode"), Env::get("GAMECONF.PASSWORD"));
        //切换到主动模式
        ftp_pasv($conn_id,true);
        $data = [];
        foreach ($gameType as $v){
            $s = $this->getData($v,$conn_id) ?? [];
            $data=array_merge($data,$s);
        }
        $model = new GameBetLog();
        $model->saveAll($data);
        $output->info("处理完成");

    }
    public function getData($gameType,$conn_id)
    {
        //获取本地存储目录
        $app = new App();
        $path = $app->getRootPath();
        $local_path = $path."gamelog";
        $date = date("Ymd");
        //获取所有文件列表
        $contents = ftp_nlist($conn_id, "/$gameType/SETTLED/{$date}");

        //先删除最后一条,避免最后一条数据没有写完
        $key = count($contents);
        if($key>0){
            unset($contents[$key-1]);
        }

        //创建写入日期目录
        $dir = $local_path."/{$gameType}/SETTLED/{$date}";
        if(!is_dir($dir)){
            mkdir($dir,0755);
        }

        //获取已传入的文件
        $exit = scandir($dir);
        foreach ($exit as &$v){
            $v = "/{$gameType}/SETTLED/{$date}/".$v;
        }
        $save = [];
        //筛选出未写入的文件
        $contents = array_diff($contents,$exit);
        try{
            foreach ($contents as $v){
                //拼接存储地址
                $local_file = $local_path.$v;
                $handle = fopen($local_file, 'w');
                //ftp远程文件并写入本地
                if (ftp_fget($conn_id, $handle, $v, FTP_ASCII, 0)) {

                    //获取写入文件的内容
                    $str = file_get_contents($local_file);
                    $datas = json_decode($str,true);
                    //  $datas['page_info']['totalCount'] 记录的总条数
                    if($datas['status'] == 0 && $datas['page_info']['totalCount'] > 0){
                        foreach ($datas['details'] as $data) {
                            $games = GameList::where("tcgGameCode", $data['gameCode'])->find();
                            $user_id = User::where("game_account", $data['username'])->value('id') ?? 0;
                            $save[] = [
                                'tcgGameCode' => $data['gameCode'],
                                "game_id" => $games ? $games['id'] : 0,
                                "betTime" => strtotime($data['betTime']),
                                "endTime" => isset($data["endTime"]) ? strtotime($data["endTime"]) : 0,
                                "productType" => $data["productType"],
                                "betAmount" => $data['betAmount'],
                                "game_account" => $data['username'],
                                "netPnl" => $data['netPnl'],
                                "betOrderNo" => $data['betOrderNo'],
                                "rake" => isset($data['rake']) ? strtotime($data['rake']) : 0,
                                "merchantCode" => $data['merchantCode'],
                                "user_id" => $user_id
                            ];
                        }
                    }

                }else{
                    echo "获取文件{$local_file}异常\n";
                }
            }
            return $save;
        }catch(\Exception $e){
            echo $e->getMessage()."\n";
        }
    }
}