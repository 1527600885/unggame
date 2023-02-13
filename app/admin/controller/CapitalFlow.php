<?php


namespace app\admin\controller;

use think\facade\View;

use app\admin\BaseController;

use app\admin\model\CapitalFlow as CapitalFlowModel;

/**
 * 账单管理
 */
class CapitalFlow extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input  = input('post.');
            $input['uid'] = input("param.uid",0);
            if(!$input['uid']) $input['type'] = 8;
            $search = ['keyword','status','catalog','uid','type'];
            $order  = [$input['prop'] => $input['order']];
            $count  = CapitalFlowModel::withSearch($search, $input)->count();
            $data   = CapitalFlowModel::withSearch($search, $input)->with(["adminAccount"])->order($order)->page($input['page'], $input['pageSize'])->select();
            return json(['status' => 'success', 'message' => '获取成功', 'data' => $data, 'count' => $count]);
        } else {
           return View::fetch();
        }
    }
}