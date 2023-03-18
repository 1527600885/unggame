<?php


namespace app\api\controller\v2;


use app\admin\model\Config as ConfigModel;
use app\api\BaseController;
use app\api\model\v2\GameList;
use app\api\model\RankList;
use app\api\model\v2\TopGame;
use app\common\game\ApiGame;
use app\common\lib\Redis;
use Hashids\Hashids;
use think\facade\Cache;

/**
 * 游戏
 * Class Game
 * @package app\api\controller\v2
 */
class Game extends BaseController
{
    protected $noNeedLogin = ['*'];
    /**
     *获取推荐游戏列表
     */
    public function recommendedList()
    {
        $where[] = ["gameType", "<>", "LIVE"];
        $where[] = ['displayStatus','=',1];
        $where[] = ['is_groom','=',1];//是否首页推荐
        $where[] = ['groom_sort','>',0];//排序序列号>0
        $this->success("success",$this->getList($where));
    }

    /**
     *获取真人游戏列表
     */
    public function liveList()
    {
        $where[] = ["gameType", "=", "LIVE"];
        $where[] = ['displayStatus','=',1];
        $where[] = ['is_groom','=',1];//是否首页推荐
        $where[] = ['groom_sort','>',0];//排序序列号>0
        $this->success("success",$this->getList($where));
    }

    /**
     * 获取列表数据
     * @param $where
     * @return mixed|\think\Paginator
     * @throws \think\db\exception\DbException
     */
    private function getList($where)
    {
        $page = input("param.page");
        $whereKey = json_encode($where);
        $size = 24;
        $key = "gamelist_{$whereKey}_{$size}_{$page}";
        $gamelist = Cache::get($key);
        if (!$gamelist) {
            $gamelist = GameList::field('*,if(groom_sort is null,2000000,groom_sort) as groom_sorts')->where($where)->order("groom_sort asc,hot desc")->paginate($size);
            Cache::set($key, $gamelist, 300);
        }
        return $gamelist;
    }
    public function getRankData()
    {
        $wow = array_chunk(TopGame::where("type",1)->append(["nickname","price"])->order("id desc")->select()->toArray(),2);
        $top =  array_chunk(TopGame::where("type",2)->append(["nickname","price"])->order("id desc")->select()->toArray(),2);
        $topGame = compact("wow","top");
        $topList = RankList::order("profit desc,update_time desc,id desc")->select()->toArray();
        $redis = (new Redis())->getRedis();
        $date = date("Ymd");
        $topThree = $redis->get("top_three_{$date}");
        if(!$topThree)
        {
            $topThree = $this->getRandData();
            $redis->set("top_three_{$date}",json_encode($topThree),24*60*60*60);
        }else{
            $topThree = json_decode($topThree,true);
        }
//        $topThree = array_slice($topList,0,3);
        $this->success("success",compact("topGame","topThree","topList"));
    }
    public function getRandData()
    {
        $hashids = new Hashids(env('hashids'), 6,env('hashids_write'));
        $data = [];
        $size = 24;
        $key = "gamelist_{$size}";
        $gamelist = Cache::get($key);
        if (!$gamelist) {
            $gamelist = GameList::field('*,if(groom_sort is null,2000000,groom_sort) as groom_sorts')->order("groom_sort asc,hot desc")->paginate($size)->toArray()['data'];
            Cache::set($key, $gamelist, 600);
        }
        for($i=0;$i<3;$i++)
        {
            $id =  mt_rand(1,10000);
            $profit = mt_rand(10,100000);
            $payout_rate = bcmul(mt_rand(1,2000),0.01,2);
            $gameName=$gamelist[array_rand($gamelist)]['gameName'];
            $headerNumber = mt_rand(1,8);
            $data[] = [
                "id"=>$id,
                "username"=>$hashids->encode($id),
                'profit'=> $profit,
                'payout'=>bcmul($profit,$payout_rate,2),
                'game_name'=>$gameName,
                'avatar'=>"/static/images/header/header{$headerNumber}.png",
            ];
        }
        return $data;
    }
    public function tryGame()
    {
        $id = input("param.id");
        $gameData = GameList::where("id",$id)->find();
        $redis = (new Redis())->getRedis();
        $ip = request()->ip();
        $key = "try_game_account_{$ip}";
        $game = new ApiGame();
        if(!$game_account = $redis->get($key)){
            $game_account = "gs".time();
            $result =  $game->create_user($game_account,"a123456");
            $ret =json_decode($result,true);
            if($ret['status']!=0)
            {
                $this->error(lang("system.busy"));
            }
            $redis->set($key,$game_account);
        }
        $run = $game->getLaunchGameRng($game_account,$game_account, $gameData->productType,0, $gameData->tcgGameCode, "html5" ,$this->gamelang);
        $runData = json_decode($run,true);
        if(isset($runData['error_desc'])){
            $this->error($runData['error_desc']);
        }
        $gamename=$gameData->gameName;
        $this->success(lang('game.run_game'),["gamename"=>$gamename,"game_url"=>$runData['game_url']]);
    }
    public function getGameDetail()
    {
        $id = input("param.id");
        $data  =GameList::where(compact("id"))->field("id,gameName,gameImage,game_release_date,author,description,images,trialSupport,tcgGameCode")->find();
        $data['is_favorite'] = 0;
        $data['is_like'] = 0;
        if($this->nologuserinfo)
        {
            $result =  \app\api\model\v2\GameInteraction::where("user_id",$this->nologuserinfo['id'])->where("game_id",$id)->find();
            if($result){
                $data['is_favorite'] = $result['is_favorite'];
                $data['is_like'] = $result['is_like'];
            }
        }
        $this->success('success',$data);
    }
}