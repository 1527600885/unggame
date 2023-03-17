<?php


namespace app\api\controller\v2;


use app\admin\model\Config as ConfigModel;
use app\api\BaseController;
use app\api\model\v2\GameList;
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
        $key = "gamelist_{$whereKey}_{$page}";
        $gamelist = Cache::get($key);
        if (!$gamelist) {
            $gamelist = GameList::field('*,if(groom_sort is null,2000000,groom_sort) as groom_sorts')->where($where)->order("groom_sort asc,hot desc")->paginate(12);
            Cache::set($key, $gamelist, 300);
        }
        return $gamelist;
    }
    public function getRankData()
    {
        $numberoneData = ConfigModel::getVal("numberoneuser");
        $numbertwoData = ConfigModel::getVal("numbertwouser");
        $numberthreeData = ConfigModel::getVal("numberthreeuser");
        $numberfourData = ConfigModel::getVal("numberfouruser");
        $topGame = [[$numberoneData,$numbertwoData],[$numberthreeData,$numberfourData]];
        $topList = RankList::order("profit desc,update_time desc,id desc")->select()->toArray();
        $topThree = array_slice($topList,0,3);
        $this->success("success",compact("topGame","topThree","topList"));
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