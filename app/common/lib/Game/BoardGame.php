<?php


namespace app\common\lib\Game;


use app\common\lib\GameBaseService;

class BoardGame extends GameBaseService
{
    protected $user;
    protected $balance;
    protected $bomb;
    protected $box;
    protected $times;
    protected $level;
    protected $fitList = ["times", "pricetype", "win"];
    protected $win;
    protected $bet_level;
    protected $price;
    protected $baseRate = 1;
    protected $payoutList = [
        1=>[0.5,0.8,1.1,1.15,1.21,1.27,1.34,1.42,1.51,1.61,1.72,1.86,2.01,2.19,2.41,2.68,3.02,3.45,4.02,4.83,5.03,8.04,12.06,20.08],
        2=>[0.8,1,1.25,1.38,1.52,1.69,1.89,2.13,2.41,2.76,3.18,3.71,4.39,5.26,5.43,8.04,10.34,13.79,19.3,28.95,48.25,96.5,150.5],
        3=>[0.9, 1, 1.44, 1.67, 1.95, 2.29, 2.72, 3.26, 3.96, 4.88, 6.1, 7.47, 9.77, 12.88, 16.94, 22.29, 29.34, 38.75, 56.19, 78.47, 98.55, 200.64],
        4=>[0.95,1,1.65,1.96,2.32,3.04,4.96,6.28,8.24,10.68,13.51,17.28,21.63,25.85,31.69,39.54,48.29,58.27,84.5,125.6,230.9]
    ];

    public function __construct()
    {


    }
    public static function allPayout()
    {
        $static = new static();
        return $static->payoutList;
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
    public function pricetype($rate)
    {
//        $decrease = 0.91 - bcmul($this->bet_level, bcmul(0.01, $this->bet_level, 2), 2);
//        $rate = bcmul($rate, $decrease, 8);
        $rate = bcmul(pow(1-bcmul($this->bet_level,0.01,2),$this->bet_level),$rate,8);
        return $rate;
    }

    public function setBetLevel($bet_level)
    {
        $this->bet_level = $bet_level;
        return $this;
    }

    public function setWin($win)
    {
        $this->win = $win;
        return $this;
    }
    public function level($rate)
    {
        $level = $this->level;
        $rate = bcmul(pow(1-bcmul($level,0.01,2),$level),$rate,8);
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

    public function getBetPrice($bet_level)
    {
        $list = [1 => 5, 2 => 10, 3 => 15, 4 => 20, 5 => 30, 6 => 40];
        return $list[$bet_level];
    }
    public function getGameResult()
    {
        $rate = $this->getRate();
        $rand_end = bcmul($rate,10000000);
        $rand = mt_rand(1,10000000);
        if($rand <= $rand_end)
        {
            $result = 1;
            if( $this->times+$this->level == 25){
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
}