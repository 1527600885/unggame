<?php


namespace app\common\lib\Game;


use app\common\lib\GameBaseService;

class EscapeGame extends GameBaseService
{
    protected $payoutList = [
        1=>[1.46,2.18,3.27,4.91,7.37,11.05,16.57,24.86],
        2=>[1.29,1.72,2.3,3.07,4.09,5.45,7.27,9.69,12.92,17.22,22.97,30.62],
        3=>[1.21, 1.52, 1.89, 2.37, 2.96, 3.7, 4.63, 5.78, 7.23, 9.03, 11.29, 14.12, 17.64, 22.06, 22.57, 34.46, 43.08, 53.85],
    ];
    protected $baseRate = 1;
    public function getBetPrice($bet_level)
    {
        $list = [1 => 5, 2 => 10, 3 => 15, 4 => 20, 5 => 30, 6 => 40];
        return $list[$bet_level];
    }
}