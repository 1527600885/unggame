<?php


namespace app\admin\controller;


use app\admin\model\Config as ConfigModel;
use app\admin\model\GameList;
use app\admin\model\Themes;
use think\facade\View;

class Numberone extends Config
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        View::assign([
            'numberoneuser' =>  ConfigModel::getVal('numberoneuser'),
            'numbertwouser' =>  ConfigModel::getVal('numbertwouser'),
        ]);
//        var_dump( ConfigModel::getVal('numbertwouser'));
        return View::fetch();
    }
    public function getGameList($key = "")
    {
        $gameList = GameList::where("gameName","like","%{$key}%")->order("id desc")->field("id,gameName as value,gameImage")
            ->select()->each(function ($item,$index){
                $item['value'] = json_decode($item['value'],true)['EN'];
                $item['gameImage'] = json_decode($item['gameImage'],true)['EN'];
                return $item;
            });

        return json(["code"=>0,"data"=>$gameList]);
    }
}