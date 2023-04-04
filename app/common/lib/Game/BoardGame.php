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
    protected  $max_level = 25;
    public function __construct()
    {


    }



    public function getBetPrice($bet_level)
    {
        $list = [1 => 5, 2 => 10, 3 => 15, 4 => 20, 5 => 30, 6 => 40];
        return $list[$bet_level];
    }

}