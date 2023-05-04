<?php


namespace app\api\controller\v3;


use app\api\BaseController;
use app\api\model\v2\GameList;

class Game extends BaseController
{
    protected $noNeedLogin = ['*'];

    /**
     *recommended slot Games
     */
    public function recommendedList()
    {
        $where[] = ['displayStatus', '=', 1];
        $where[] = ['groom_sort', '>', 0];//排序序列号>0
        $sort = "groom_sort asc";
        $this->success("success", $this->getList($where, $sort));
    }
    public function interestingList()
    {
        $where[] = ['displayStatus', '=', 1];
        $where[] = ['tablegame_sort', '>', 0];//排序序列号>0
        $sort = "tablegame_sort asc";
        $this->success("success", $this->getList($where, $sort));
    }
    public function getList($where, $sort)
    {
        $whereKey = json_encode($where);
        $key = "gamelist_{$whereKey}_{$sort}";
        $lists = GameList::where($where)->order($sort)->cache($key)->select();
        $count = count($lists);
        $lists = array_chunk($lists, 4);
        return compact("count", "lists");
    }
}