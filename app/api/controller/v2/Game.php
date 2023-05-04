<?php


namespace app\api\controller\v2;


use app\admin\model\Config as ConfigModel;
use app\api\BaseController;
use app\api\model\UserSign;
use app\api\model\v2\GameList;
use app\api\model\RankList;
use app\api\model\v2\TopGame;
use app\api\model\v2\TopLivegame;
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
//        $where[] = ["gameType", "=", "LIVE"];
//        $where[] = ['displayStatus','=',1];
//        $where[] = ['is_groom','=',1];//是否首页推荐
//        $where[] = ['groom_sort','>',0];//排序序列号>0
        $list=[];
        $gamelist = TopLivegame::select();
        foreach ($gamelist as &$v){
            $v['id'] = $v['game_id'];
        }
        foreach ($gamelist as $key => $value){
               
              if(count($list)<4){
                  $list[]=$value;
              }else{
                //   var_dump($key);
                //   die;
                $lists[]=$list;
                $list=[];
                $list[]=$value;
              }
          }
          if(count($list)>0){
              $lists[]=$list;
          }
          $data['data']=$lists;
          $data['count']=count($gamelist);
        $this->success("success",$data);
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
        $size = 1000;
        $key = "gamelist_{$whereKey}_{$size}_{$page}";
        $list=[];
        $gamelist = Cache::get($key);
        $gamelist=false;
        if (!$gamelist) {
            $gamelist = GameList::field('*,if(groom_sort is null,2000000,groom_sort) as groom_sorts')->where($where)->order("groom_sort asc,hot desc")->paginate($size);
          foreach ($gamelist as $key => $value){
               
              if(count($list)<4){
                  $list[]=$value;
              }else{
                //   var_dump($key);
                //   die;
                $lists[]=$list;
                $list=[];
                $list[]=$value;
              }
          }
          if(count($list)>0){
              $lists[]=$list;
          }
          $data['data']=$lists;
          $data['count']=count($gamelist);
            // Cache::set($key, $gamelist, 300);
        }
        return $data;
    }
    public function getbigwin(){
        $list = GameList::where("topgame_sort",">",0)->cache("topgame_list")->append(["nickname","price"])->order("topgame_sort desc")->select();
        $this->success("success",$list);
    }
    public function getRankData()
    {
        $wowList = TopGame::where("type",1)->append(["nickname","price"])->order("id desc")->select()->toArray();
        foreach ($wowList as &$item){
            $item["id"] = $item['game_id'];
        }
        $wow = array_chunk($wowList,2);
        $topList = TopGame::where("type",2)->append(["nickname","price"])->order("id desc")->select()->toArray();
        foreach ($topList as &$value){
            $value['id'] = $value['game_id'];
        }
        $top =  array_chunk($topList,2);
        $topGame = compact("wow","top");
        $topList = RankList::order("profit desc,update_time desc,id desc")->select()->toArray();
        $redis = (new Redis())->getRedis();
        $date = date("Ymd");
        $topThree = $redis->get("top_three_{$date}");
        if(!$topThree)
        {
            $topThree = $this->getRandData();
            $redis->set("top_three_{$date}",json_encode($topThree),24*60*60);
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
        $randData = [bcmul(mt_rand(1000000,3000000),0.01,2),bcmul(mt_rand(1000000,3000000),0.01,2),bcmul(mt_rand(1000000,3000000),0.01,2)];
        rsort($randData);
        for($i=0;$i<3;$i++)
        {
            $id =  mt_rand(1,10000);
            $profit = $randData[$i];
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
        if($gameData && $gameData['type'] == 1){
            $this->success(lang('game.run_game'),["gamename"=>$gameData->gameName,"game_url"=>$gameData['trygameurl']]);
        }
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
                $data['is_like'] = $result['is_liked'];
            }
        }
        $this->success('success',$data);
    }

}