<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use app\api\model\Helplist;
use app\api\model\v2\AccountType;
use app\api\model\v2\AppVersions;
use app\api\model\v2\GameList;
use app\common\lib\Redis;
use Hashids\Hashids;
use think\facade\Cache;

class Index extends BaseController
{
    protected $noNeedLogin = ['*'];
    public function helpList()
    {
       $list = Helplist::where("status",1)->field("id,title,descript")->select();
       $this->success("success",$list);
    }
    public function helpdetail($id)
    {
        $this->success("success",Helplist::find($id));
    }
    public function accountTypeList()
    {
        $list = AccountType::order("id desc")->column("name");
        $this->success("success",$list);
    }
    public function getRankData()
    {
       $key = "game_rank_list";
       $redis = (new Redis())->getRedis();
       $rankData = $redis->get($key);
       if(!$rankData)
       {
           $rankData = $this->getRandData();
           $redis->set($key,json_encode($rankData),1);
       }else{
           $rankData = json_decode($rankData,true);
       }
       $this->success("success",$rankData);
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
        for($i=0;$i<10;$i++)
        {
            $id =  mt_rand(1,10000);
            $profit = bcmul(mt_rand(10,1000000),0.01,2);
            $avartnum = mt_rand(1,2);
            $payout = bcmul(mt_rand(12,200),0.1,1);
            $game = $gamelist[array_rand($gamelist)];
            $gameName=$game['gameName'];
            $game_id = $game['id'];
            $data[] = [
                "id"=>$game_id,
                "username"=>$hashids->encode($id),
                'profit'=> $profit,
                'payout'=>$payout.'x',
                'game_name'=>$gameName,
                'avatar'=>"/static/images/index/playlg{$avartnum}.png"
            ];
        }
        return $data;
    }
    public function appVersion($type="andriod")
    {
        $this->success("success",AppVersions::where("platform",$type)->where("status",1)->order("version_code desc,id desc")->find());
    }
}