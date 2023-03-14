<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use app\api\model\Helplist;
use app\api\model\v2\AccountType;

class Index extends BaseController
{
    public function helpList()
    {
       $list = Helplist::where("status",1)->field("id,title")->select();
       $this->success("success",$list);
    }
    public function helpdetail($id)
    {
        $this->success("success",Helplist::find($id));
    }
    public function accountTypeList()
    {
        $list = AccountType::order("id desc")->column("name");
        $this->success("success",$list);
    }
}