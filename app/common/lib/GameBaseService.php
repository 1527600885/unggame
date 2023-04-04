<?php


namespace app\common\lib;


class GameBaseService
{
    protected $game_type;
    protected $betList;
    protected $baseRate;
    protected $fitList = [];
    public function __construct()
    {

    }
    public function getRate()
    {
        $rate = $this->baseRate;
        foreach ($this->fitList as $v)
        {
           $rate =call_user_func([$this,$v],$rate);
        }
        return $rate;
    }
    public function win($rate)
    {

        $base_rate =  $this->win < 0 ? 1.005 : 0.9;
        $win = abs($this->win);
        $result = intval($win / $this->getBetPrice($this->bet_level));
        for ($i = $result; $i > 1; $i--) {
            $rate = bcmul($rate, $base_rate, 8);
        }
        return $rate;
    }
    public function setWin($win)
    {
        $this->win = $win;
        return $this;
    }
    public function setTimes($times)
    {
        $this->times = $times;
        return $this;
    }
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
    public function setBetLevel($bet_level)
    {
        $this->bet_level = $bet_level;
        return $this;
    }
    public function getGameResult()
    {
        $rate = $this->getRate();
        $rand_end = bcmul($rate,10000000);
        $rand = mt_rand(1,10000000);
        if($rand <= $rand_end)
        {
            $result = 1;
            if( $this->times+$this->level >= count($this->payoutList[$this->level])){
                $is_over = true;
            }else{
                $is_over = false;
            }
        }else{
            $result = 2;
            $is_over = true;
        }
        return compact("result","is_over","rate");

    }
    public  function allPayout()
    {
        return $this->payoutList;
    }
    public function getPayOut($times)
    {
        return $this->payoutList[$this->level][$times - 1];
    }

    public function times($rate)
    {
//        $decrease = 0.91 - bcmul($this->times, bcmul(0.01, $this->times, 2), 2);
//        $rate = bcmul($rate, $decrease, 8);
        $rate = bcmul(pow(1-bcmul($this->times,0.01,2),$this->times),$rate,8);
        return $rate;
    }

    public function pricetype($rate)
    {
//        $decrease = 0.91 - bcmul($this->bet_level, bcmul(0.01, $this->bet_level, 2), 2);
//        $rate = bcmul($rate, $decrease, 8);
        $rate = bcmul(pow(1-bcmul($this->bet_level,0.01,2),$this->bet_level),$rate,8);
        return $rate;
    }


    public function level($rate)
    {
        $level = $this->level;
        $rate = bcmul(pow(1-bcmul($level,0.01,2),$level),$rate,8);
        return $rate;
    }
}