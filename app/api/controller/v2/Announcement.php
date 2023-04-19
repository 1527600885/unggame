<?php


namespace app\api\controller\v2;


use app\api\BaseController;

class Announcement extends BaseController
{
    public function news()
    {
        $map = [];
        if($this->request->userInfo){
            $map[] = ["user_id","in",[0,$this->request->userInfo->id]];
        }
       $list =  \app\api\model\v2\Announcement::where("status",1)->column("distinct type");
        $data = [];
        foreach ($list as $v)
        {
            $data[] = \app\api\model\v2\Announcement::where("status",1)->where("type",$v)->append(["icon","time","title","date_time"])->order("id desc")->find();
        }
        $this->success("success",["list"=>$data,"fist_id"=>1]);

    }
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
        if($count > 10){
            $start = $count - 10;
        }else{
            $start = 0;
        }
       $list =  \app\api\model\v2\Announcement::where("status",1)
            ->append(["icon","time","title","date_time"])
            ->where($map)
            ->order("id asc")
            ->limit($start,10)
            ->select()->toArray();
        $first = current($list);
        if(!$first){
            $data=[];
        }else{
            $data=["list"=>$list,"first_id"=>$first['id']];
        }
        $this->success("success",$data);
    }
    public function detail($id)
    {
       $data = \app\api\model\v2\Announcement::where("id",$id)->find();
       $this->success("success",$data);
    }
}