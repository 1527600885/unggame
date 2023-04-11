<?php


namespace app\api\controller\v2;


use app\api\BaseController;

class Announcement extends BaseController
{
    public function index()
    {
        $map = [];
        if($this->request->userInfo){
            $map[] = ["user_id","in",[0,$this->request->userInfo->id]];
        }

        $lastid = input("post.lastid");
        $type = input("post.type","");
        if($type){
            $map[] = ["type","=",$type];
        }
        if($lastid){
            $map[] = ["id","<",$lastid];
        }
        $count =   \app\api\model\v2\Announcement::where("status",1)
            ->where($map)->count();
        if($count > 15){
            $start = $count - 15;
        }else{
            $start = 0;
        }
       $list =  \app\api\model\v2\Announcement::where("status",1)
           ->append(["icon","time"])
            ->where($map)
            ->order("id asc")
            ->limit($start,15)
            ->select();
        $this->success("success",$list);
    }
    public function detail($id)
    {
       $data = \app\api\model\v2\Announcement::where("id",$id)->find();
       $this->success("success",$data);
    }
}