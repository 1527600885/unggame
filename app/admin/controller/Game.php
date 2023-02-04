<?php
namespace app\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use app\common\game\ApiGame as apigame; //游戏相关接口
use app\admin\model\GameBrand as GameBrandModel; //游戏品牌模型
use app\admin\model\GameList as GameListModel;//游戏列表模型
/**
 * 游戏的相关管理
 */
class Game  extends BaseController
{
	// 游戏品牌列表
	public function brand(){
		$brand=GameBrandModel::select();
		View::assign('brand', $brand);
		return View::fetch();
	}
	// 游戏列表
	public function gamelist(){
		return View::fetch();
	}
}
?>