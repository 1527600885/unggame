<?php
// +----------------------------------------------------------------------
// | OneKeyAdmin [ Believe that you can do better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2023 http://onekeyadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MUKE <513038996@qq.com>
// +----------------------------------------------------------------------
namespace plugins\game\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use plugins\game\admin\model\MkGamelist as MkGamelistModel;
use plugins\game\admin\model\MkGamebrand as MkGamebrandModel;
/**
 * MkGamelist管理
 */
class MkGamelist extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
			$search = ['keyword','gameType','productType'];
            $count = MkGamelistModel::withSearch($search, $input)->count();
			$data  = MkGamelistModel::withSearch($search, $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
            foreach($data as $k=>$v){
            	$gameImage=json_decode($v->gameImage,true);
            	$v->gameImage=$gameImage['EN'] ?? '';
				$gameName=json_decode($v->gameName,true);
				// $v->gameName='英文：'.$gameName['EN'].'<br>印尼语：'.$gameName['ID'].'<br>泰语：'.$gameName['TH'].'<br>越南语：'.$gameName['VI'].'<br>柬埔寨：'.$gameName['KM'].'<br>马来语：'.$gameName['MS'].'<br>日语：'.$gameName['JA'].'<br>韩语：'.$gameName['KO'];
				$v->gameName=$gameName['EN'];
				if($v->gameType=='LOTT'){
					$v->gameType='彩票';
				}elseif($v->gameType=='LIVE'){
					$v->gameType='真人';
				}elseif($v->gameType=='RNG'){
					$v->gameType='电子';
				}elseif($v->gameType=='FISH'){
					$v->gameType='捕鱼';
				}elseif($v->gameType=='PVP'){
					$v->gameType='棋牌';
				}elseif($v->gameType=='SPORT'){
					$v->gameType='体育';
				}elseif($v->gameType=='ESPORT'){
					$v->gameType='电竞';
				}
				if($v->is_visit==1){
					$v->visit='可访问';
				}else{
					$v->visit='不可访问';
				}
			}
			$branlist=MkGamebrandModel::field('`name` as label,`code` as value')->whereOr([
				['RNG_totalCount','>',0],
				['LOTT_totalCount','>',0],
				['LIVE_totalCount','>',0],
				['FISH_totalCount','>',0],
				['PVP_totalCount','>',0],
				['SPORT_totalCount','>',0],
				['ESPORT_totalCount','>',0]
			])->select();
			return json(["status" => "success", "message" => "请求成功", "data" => $data, "count" => $count,"branlist"=>$branlist]);
        } else {
            return View::fetch();
        }
    }
    
    /**
     * 保存新建的资源
     */
    public function save()
    {
        if ($this->request->isPost()) {
            MkGamelistModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }

    public function update()
    {
        if ($this->request->isPost()) {
            $postarr=input("post.");
            // if($postarr['is_groom']==0){
            // 	return json(["status" => "error", "message" => "请先推荐首页"]);
            // }
            // if($postarr['category_put']==0){
            // 	return json(["status" => "error", "message" => "请先推荐品牌"]);
            // }
            unset($postarr['gameType']);
//            unset($postarr['gameName']);
            $postarr['gameName'] = json_encode([
                "EN" =>$postarr['gameName'],
                "TH" =>$postarr['gameName'],
                "VI" =>$postarr['gameName'],
                "ID" =>$postarr['gameName'],
                "KM" =>$postarr['gameName'],
                "MS" =>$postarr['gameName'],
                "JA" =>$postarr['gameName'],
                "KO" =>$postarr['gameName'],
            ]);
            if(stripos($postarr['gameImage'],"http") === false){
                $postarr['gameImage'] =
                    json_encode([
                        "EN" =>"https://image.unggame.com".$postarr['gameImage'],
                        "TH" =>"https://image.unggame.com".$postarr['gameImage'],
                        "VI" =>"https://image.unggame.com".$postarr['gameImage'],
                        "ID" =>"https://image.unggame.com".$postarr['gameImage'],
                        "KM" =>"https://image.unggame.com".$postarr['gameImage'],
                        "MS" =>"https://image.unggame.com".$postarr['gameImage'],
                        "JA" =>"https://image.unggame.com".$postarr['gameImage'],
                        "KO" =>"https://image.unggame.com".$postarr['gameImage']]);
            }else if(stripos($postarr['gameImage'],"https://image") !== false){
                unset($postarr['gameImage']);
            }
            MkGamelistModel::update($postarr);
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkGamelistModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}