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
}