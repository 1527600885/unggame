<?php


namespace app\common\lib\Game;


class SlotsGame
{
    protected $goodsList = [
        1 => 5,
        2 => 9,
        3 => 10,
        4 => 11,
        5 => 12,
        6 => 13,
        7 => 14,
        8 => 15,
        9 => 20,
        10 => 20,
        11 => 20,
        12 => 20
    ];
    public function getlists()
    {
        $data =  [];
        $count = 0;
        foreach ($this->goodsList as $k=>$v)
        {
            $data = array_pad($data,$count+$v,$k);
        }
        shuffle($data);
        $result = [];
        for($i=0;$i<=5;$i++)
        {
            $result[] = array_slice($data,0,5);
        }
        return $result;
    }
}