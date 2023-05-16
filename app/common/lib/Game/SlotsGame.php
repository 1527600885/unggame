<?php


namespace app\common\lib\Game;


class SlotsGame
{
    protected $bet = 5;
    protected $total_win = 0;
    protected $total_house = 0;
    protected $total_apple = 0;
    protected $rounds_lefts ;
    /*
     * 1=城堡
     * 2=苹果
     * 3=金苹果
     * 4=wild
     * 5=公主
     * 6=王子
     * 7=王后
     * 8=王
     * 9=小矮人
     * 10=A
     * 11=K
     * 12=Q
     * 13=J
     * 14=篮子
     * 15=+
     * 16=x
     * 17gold
     * */
    protected $goodsList = [
        1 => 5,//城堡
        2 => 8,//苹果
        3 => 1,//金苹果
        4 => 10,//任意
        5 => 10,
        6 => 12,
        7 => 13,
        8 => 14,
        9 => 15,
        10 => 20,
        11 => 20,
        12 => 20,
        13 => 20
    ];
    protected $rate = [
        4 => [3 => 1, 4 => 2.5, 5 => 12.5],
        5 => [3 => 1, 4 => 2.5, 5 => 12.5],
        6 => [3 => 0.5, 4 => 2, 5 => 10],
        7 => [3 => 0.25, 4 => 1.5, 5 => 7.5],
        8 => [3 => 0.25, 4 => 1.25, 5 => 6],
        9 => [3 => 0.25, 4 => 1, 5 => 5],
        10 => [3 => 0.25, 4 => 0.5, 5 => 2.5],
        11 => [3 => 0.25, 4 => 0.5, 5 => 2.5],
        12 => [3 => 0.25, 4 => 0.5, 5 => 2.5],
        13 => [3 => 0.25, 4 => 0.5, 5 => 2.5],
    ];
    protected $win_lines = [
        [[0, 0], [1, 0], [2, 0], [3, 0], [4, 0]],
        [[0, 1], [1, 1], [2, 1], [3, 1], [4, 1]],
        [[0, 2], [1, 2], [2, 2], [3, 2], [4, 2]],
        [[0, 3], [1, 3], [2, 3], [3, 3], [4, 3]],
        [[0, 0], [1, 1], [2, 2], [3, 1], [4, 0]],
        [[0, 3], [1, 2], [2, 1], [3, 2], [4, 3]],
        [[0, 2], [1, 1], [2, 0], [3, 1], [4, 2]],
        [[0, 1], [1, 2], [2, 3], [3, 2], [4, 3]],
        [[0, 0], [1, 1], [2, 0], [3, 1], [4, 0]],
        [[0, 3], [1, 2], [2, 3], [3, 2], [4, 3]],
        [[0, 1], [1, 0], [2, 1], [3, 0], [4, 1]],
        [[0, 2], [1, 3], [2, 2], [3, 3], [4, 2]],
        [[0, 1], [1, 2], [2, 1], [3, 2], [4, 1]],
        [[0, 2], [1, 1], [2, 2], [3, 1], [4, 2]],
        [[0, 0], [1, 1], [2, 1], [3, 1], [4, 0]],
        [[0, 3], [1, 2], [2, 2], [3, 2], [4, 3]],
        [[0, 1], [1, 0], [2, 0], [3, 0], [4, 1]],
        [[0, 2], [1, 3], [2, 3], [3, 3], [4, 2]],
        [[0, 1], [1, 2], [2, 2], [3, 2], [4, 1]],
        [[0, 2], [1, 1], [2, 1], [3, 1], [4, 2]],
        [[0, 0], [1, 0], [2, 1], [3, 0], [4, 0]],
        [[0, 3], [1, 3], [2, 2], [3, 3], [4, 3]],
        [[0, 1], [1, 1], [2, 0], [3, 1], [4, 1]],
        [[0, 2], [1, 2], [2, 3], [3, 2], [4, 2]],
        [[0, 1], [1, 1], [2, 2], [3, 1], [4, 1]]
    ];

    public function getRespinResult($param)
    {
        //优先结算苹果奖励
        $board = $param['board'];
        $bs = $param['bs'];
        $rounds_lefts = $param['rounds_lefts'];
        $list = [];
        $list = array_pad($list, 30, 2);//放入30个红苹果
        $list = array_pad($list, 32, 3);//放入2个金苹果
        $list = array_pad($list, 33, 14);//放入篮子
        $list = array_pad($list, 100, 0);//其余放空
        shuffle($list);
        $new_bs = [];
        $bs_pos = [];
        $basket = [];
        $is_success = false;
        foreach ($board as $k => &$v) {
            foreach ($v as $kk => &$vv) {
                if (!in_array($vv, [2, 3, 14])) {
                    if (count($new_bs) < 3) {
                        $re = array_pop($list);
                        if ($re != 0) {
                            $is_success = true;
                            $new_bs[] = [$k, $kk];
                            $vv = $re;
                            if ($re == 14) {
                                $basket = [$k, $kk];
                            } else {
                                $bs[$k][$kk] = mt_rand(1, 20) * 0.5 * $this->bet;
                            }
                        }
                    }
                } else {
                    $bs_pos[] = [$k, $kk];//原来苹果和篮子的位置
                }
            }
        }
        $bs_count = count($new_bs) + count($bs_pos);
        $total = 0;
        foreach ($bs as $k => $v) {
            foreach ($v as $kk => $vv) {
                if ($board[$k][$kk] != 14) {
                    $total += $vv;
                }
            }
        }
        if ($basket) {
            $bs[$basket[0]][$basket[1]] = $total;
        }
        //再结算额外栏目的奖励
        $extra_board = [];
        $boadList = [];
        $boadList = array_pad($boadList, 1, 15);
        $boadList = array_pad($boadList, 2, 16);
        $boadList = array_pad($boadList, 3, 17);
        $boadList = array_pad($boadList, 100, 30);//其余放空
        shuffle($boadList);
        $extra_board_values = [];
        for ($i = 0; $i < 5; $i++) {
            $res = array_pop($boadList);
            $extra_board[] = $res;
            switch ($res) {
                case 15:
                    //加号
                    $extra_values = bcmul(bcmul($this->bet, rand(1, 20)),0.5,1);
                    $extra_board_values[] = $extra_values;
                    foreach ($bs[$i] as &$value)
                    {
                        if(!empty($value))
                        {
                            $value = bcadd($extra_values,$value,1);
                        }
                    }
                    $is_success = true;
                    break;
                case 16:
                    $extra_values = bcmul(rand(2, 20),0.5,1);
                    $extra_board_values[] = $extra_values;
                    foreach ($bs[$i] as $value)
                    {
                        if(!empty($value))
                        {
                            $value = bcmul($extra_values,$value,1);
                        }
                    }
                    $is_success = true;
                    break;
                case 17:
                    $keyList = [];
                    foreach ($board[$i] as $k=>$v){
                        if($v == 2){
                            $keyList[] = $k;
                        }
                    }
                    if(!empty($keyList)){
                        $key = array_rand($keyList);
                        $board[$i][$key] = 3;
                    }
                    $is_success = true;
                    break;
                default:


            }

        }
        if($is_success){
            $rounds_lefts = 3;
        }else{
            $rounds_lefts-=1;
        }
        return compact("board", "bs_count", "bs_pos", "bs", "new_bs","rounds_lefts");
    }

    public function getSpinResult()
    {
        $board = $this->getBoard();
        $winlines = $this->getWinLines($board);
        $bs = $this->getBs($board);
        $total_win = $this->total_win;
        $total_house = $this->total_house;
        $total_apple = $this->total_apple;
        $game_type = "spins";
        return compact("board", "bs", "winlines", "total_win", "total_house", "total_apple", "game_type");
    }

    public function getBs($result)
    {
        foreach ($result as &$v) {
            foreach ($v as &$vv) {
                if ($vv != 2 && $vv != 3) {
                    if ($vv == 1) {
                        $this->total_house += 1;
                    }
                    $vv = 0;
                } else {
                    $vv = mt_rand(1, 8) * 0.5 * $this->bet;
                    $this->total_apple += 1;
                }
            }
        }
        return $result;
    }

    public function getBoard()
    {
        $data = [];
        $count = 0;
        foreach ($this->goodsList as $k => $v) {
            $count += $v;
            $data = array_pad($data, $count, $k);
        }
        shuffle($data);
        $result = [];
        for ($i = 0; $i < 5; $i++) {
            $result[] = array_slice($data, $i * 4, 4);

        }
        return $result;
    }

    public function getWinLines($result)
    {
        $winlines = [];
        foreach ($this->win_lines as $k => $v) {
            $s = [];
            $first = "";
            foreach ($v as $vv) {
                $type = $result[$vv[0]][$vv[1]];
                if ($type <= 3) {
                    break;
                }
                if (!$first) {
                    $s[] = $vv;
                    if ($type > 4) {
                        $first = $type;
                    }
                    continue;
                }
                if ($first) {
                    if ($type == $first || $type == 4) {
                        $s[] = $vv;
                    } else {
                        break;
                    }
                }

            }
            $count = count($s);
            if ($count >= 3) {
                if (!$first) {
                    $first = 4;
                }
                $amounts = $this->rate[$first][$count] * $this->bet;
                $this->total_win += $amounts;
                $winlines[] = ['amount' => $amounts, "line" => $k, "occurrences" => $count, "symbol" => $first, "positions" => $s, "type" => "lb"];
            }
        }
        return $winlines;
    }
}