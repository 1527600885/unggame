<?php


namespace app\api\controller\v2;


use app\admin\model\Config as ConfigModel;
use app\api\BaseController;
use app\api\model\GameList;
use app\api\model\RankList;
use app\common\game\ApiGame;
use app\common\lib\Redis;
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
        $where[] = ["gameType", "!=", "LIVE"];
        $this->success("success",$this->getList($where));
    }

    /**
     *获取真人游戏列表
     */
    public function liveList()
    {
        $where[] = ["gameType", "=", "LIVE"];
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
        $page = input("page.");
        $where = json_encode($where);
        $key = "gamelist_{$where}_{$page}";
        $gamelist = Cache::get($key);
        if (!$gamelist) {
            $gamelist = GameList::cache("gamelist_{$page}_{$where}")->field('*,if(groom_sort is null,2000000,groom_sort) as groom_sorts')->where($where)->order("groom_sorts asc,hot desc")->paginate(4);
            Cache::set($key, $gamelist, 300);
        }
        return $gamelist;
    }
    public function getRankData()
    {
        $numberoneData = ConfigModel::getVal("numberoneuser");
        $numbertwoData = ConfigModel::getVal("numbertwouser");
        $topGame = [$numberoneData,$numbertwoData];
        $topList = RankList::order("profit desc,update_time desc,id desc")->select();
        $topThree = array_slice($topList,2);
        $this->success("success",compact("topGame","topThree","topList"));
    }
    public function tryGame()
    {
        $tcgGameCode = input("post.tcgGameCode");
        $gameData = GameList::where("tcgGameCode",$tcgGameCode)->find();
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
        $gamename= json_decode($gameData->gameName,true)[$this->gamelang];
        $this->success(lang('game.run_game'),["gamename"=>$gamename,"game_url"=>$runData['game_url']]);
    }

}