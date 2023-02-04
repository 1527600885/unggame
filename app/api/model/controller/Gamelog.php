<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\Request;
use think\facade\Cache;
use app\api\BaseController;

class Gamelog extends BaseController
{
	protected $noNeedLogin = ['*'];
	public function initialize(){
		parent::initialize();
		$this->GamelogModel = new \app\api\model\Gamelog;//游戏日志模型
	}
	
    /**
     * 首页游戏记录
     *
     * @return \think\Response
     */
    public function index()
    {
		$data=cache('index_money');
		if(!$data){
			for($i=0;$i<9;$i++){
				$data[$i]['nickname']=randStr(2)."***".randStr(2);
				$data[$i]['money']=rand(100,9999).".".rand(10,99);
			}
			$data=array_sort($data,'money');
			cache('index_money',$data,7200);
		}
		$this->success(lang('system.success'),$data);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
