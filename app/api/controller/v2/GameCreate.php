<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use app\api\model\GameBetLog;
use app\api\model\Gamelog;
use app\api\model\v2\GameList;
use app\api\model\v2\Platgame;
use app\api\model\v2\PlatgameLog;
use app\api\model\v2\PlatgameLogDetail;

class GameCreate extends BaseController
{
    public function getResult($game_id = 1,$bet_level = 1,$level =1,$position = 1)
    {
        $game_log = PlatgameLog::where("game_id",$game_id)->where("result",0)->where("user_id",$this->request->userInfo['id'])->find();
        $game = GameList::where("id",$game_id)->find();
        $gameClass = new $game->game_class();
        $bet = $gameClass->getBetPrice($bet_level);
        $balance = $this->request->userInfo->balance;

        if(!$game_log)
        {
            if($balance < $bet)
            {
                $this->error("Insufficient balance.");
            }
            $log_id = Gamelog::where("gid",$game_id)->where("uid",$this->request->userInfo->id)->where("type",1)->value("id");
            $this->request->userInfo->balance = bcadd($this->request->userInfo->balance,-$bet);
            $this->request->userInfo->save();
            $game_log  = PlatgameLog::create(["game_id"=>$game_id,"bet"=>$bet,"create_time"=>time(),"user_id"=>$this->request->userInfo['id'],"log_id"=>$log_id,"bet_level"=>$bet_level]);
        }
        $win = PlatgameLog::where("user_id",$this->request->userInfo['id'])->where("game_id",$game_id)->field("sum(awards-bet) as total")->find();
        $win = $win['total'];
        $times = PlatgameLogDetail::where("user_id",$this->request->userInfo['id'])->where("log_id",$game_log['id'])->count()+1;
        $data = $gameClass->setTimes($times)
            ->setLevel($level)
            ->setBetLevel($bet_level)
            ->setWin($win)
            ->getGameResult();
        $awards = bcmul($gameClass->getPayOut($times),$bet,2);
        if($data['is_over']){
            $game_log->result = $data['result'];
            $game_log->awards = $data['result'] == 1 ? $awards : 0;
            $game_log->profit = bcadd(-$game_log->bet."",$game_log->awards,2);
            $game_log->end_time = time();
            $game_log->save();
            $user = $this->request->userInfo;
            $user->balance = bcadd($user->balance,$game_log->awards,2);

            $game_name  = $game->gameName;
            if($game_log->profit == 0){
                $content='{capital.gamecontento}'.$game_name.'{capital.gamecontentf}';
                $admin_content='用户'.$user->nickname.'游玩游戏'.$game_name.'资金不变';
                $money_type=0;

            }else if($game_log->profit > 0){
                $money_type=1;
                $content='{capital.gamecontento}'.$game_name.'{capital.gamecontentt}'.$game_log['profit'].'{capital.money}';
                $admin_content='用户'.$user->nickname.'游玩游戏'.$game_name.'资金增加'.$game_log['profit'].'美元';
            }else{
                $money_type=2;
                $content='{capital.gamecontento}'.$game_name.'{capital.gamecontenth}'.abs($game_log['profit']).'{capital.money}';
                $admin_content='用户'.$user->nickname.'游玩游戏'.$game_name.'资金减少'.abs($game_log['profit']).'美元';
            }
            GameBetLog::create([
                "tcgGameCode"=>$game->tcgGameCode,
                "game_id"=>$game->id,
                "betTime"=>time(),
                "endTime"=>time(),
                "productType"=>0,
                "betAmount"=>$game_log->bet,
                "game_account"=>$this->request->userInfo->game_account,
                "netPnl"=>$game_log->profit,
                "user_id"=>$this->request->userInfo->id
            ]);
            capital_flow($user->id,$game_id,3,$money_type,abs($game_log->profit),$user->balance,$content,$admin_content,$game_log->id);
        }
        PlatgameLogDetail::create([
            "game_id"=>$game_id,
            "bet"=>$bet,
            "result"=>$data['result'],
            "awards"=>$awards,
            "create_time"=>time(),
            "user_id"=>$this->request->userInfo['id'],
            "log_id"=>$game_log['id'],
            "level"=>$level,
            "bet_level"=>$bet_level,
            "times"=>$times,
            "position"=>$position
        ]);
        $this->success("success",[
            "result"=>$data['result'],
            "nextPayout"=>$gameClass->getPayOut($times+1),
            "nextPrice"=>bcmul($gameClass->getPayOut($times+1),$bet,2),
            "balance"=> $this->request->userInfo->balance
        ]);
    }
    public function endGame($game_id)
    {
        $game_log = PlatgameLog::where("game_id",$game_id)->where("result",0)->where("user_id",$this->request->userInfo['id'])->find();
        if(!$game_log) $this->error("game is end");
        $awards = PlatgameLogDetail::where("log_id",$game_log['id'])->max("awards");
        $game_log->result = 1;
        $game_log->awards = $awards;
        $game_log->end_time = time();
        $game_log->profit = bcadd($awards,$game_log['bet'],2);
        $game_log->save();
        $this->request->userInfo->balance = bcadd($this->request->userInfo->balance,$awards,2);
        $this->request->userInfo->save();
        $money_type=1;
        $game = GameList::where("id",$game_id)->find();
        $game_name = $game->game_name;
        $user = $this->request->userInfo;
        $content='{capital.gamecontento}'.$game_name.'{capital.gamecontentt}'.$game_log['profit'].'{capital.money}';
        $admin_content='用户'.$user->nickname.'游玩游戏'.$game_name.'资金增加'.$game_log['profit'].'美元';
        GameBetLog::create([
            "tcgGameCode"=>$game->tcgGameCode,
            "game_id"=>$game->id,
            "betTime"=>time(),
            "endTime"=>time(),
            "productType"=>0,
            "betAmount"=>$game_log->bet,
            "game_account"=>$this->request->userInfo->game_account,
            "netPnl"=>$game_log->profit,
            "user_id"=>$this->request->userInfo->id
        ]);
        capital_flow($user->id,$game_id,3,$money_type,$game_log->profit,$user->balance,$content,$admin_content);
        $this->success("success",["awards"=>$awards,"balance"=>$this->request->userInfo->balance]);
    }
    public function getLastGame($game_id)
    {
        $game_log = PlatgameLog::where("game_id",$game_id)->where("result",0)->where("user_id",$this->request->userInfo['id'])->find();
        if(!$game_log) $this->success("success",["balance"=>$this->request->userInfo->balance,"history"=>""]);
        $list = PlatgameLogDetail::where("log_id",$game_log['id'])->field("game_id,result,awards,user_id,level,bet_level,times,position")->order("times asc")->select();
        $game = GameList::where("id",$game_id)->find();
        $gameClass = new $game->game_class();
        $nextTime = end($list)[0]['times']+1;
        $gameClass->setLevel(end($list)[0]['level']);
        $bet = $game_log['bet'];
        $bet_level = $game_log['bet_level'];
        $next = ["nextPayout"=>$gameClass->getPayOut($nextTime),"nextPrice"=>bcmul($gameClass->getPayOut($nextTime),$game_log['bet'],2)];
        $awards = end($list)[0]['awards'];
        $this->success("success",["balance"=>$this->request->userInfo->balance,"history"=>compact("next","list","bet","bet_level","awards")]);
    }
    public function getPayoutList($game_id)
    {
        $game = GameList::where("id",$game_id)->find();
        $gameClass = new $game->game_class();
        $this->success("success",$gameClass->allPayout());
    }
}