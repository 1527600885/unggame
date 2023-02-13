<?php


namespace app\admin\model;


use think\Model;

class CapitalFlow extends Model
{
    public function searchKeywordAttr($query, $value, $array)
    {
        if (! empty($value)) {
            $query->where("admin_content",'like', '%' . $value . '%');
        }
    }
    public function adminAccount()
    {
        return $this->belongsTo(Admin::class, 'other_id')->bind(["account"]);
    }
    public function searchUidAttr($query, $value, $array)
    {
        if(!empty($value)){
            $query->where("uid",$value);
        }
    }
    public function searchCatalogAttr($query, $value, $array){
        if(! empty($value))
        {
            $query->where("type",$value);
        }
    }
    public function searchStatusAttr($query, $value, $array){
        if(isset($value) && $value!='')
        {
            $query->where("money_type",$value);
        }
    }
    public function getAddTimeAttr($value)
    {
        return date("Y-m-d H:i:s",$value);
    }
    public function getTypeAttr($value)
    {
        $data = [1=>'充值',2=>'提现',3=>'游戏',4=>'独角币股息',5=>'邀请注册奖励',6=>'好友充值奖励',7=>'安全中心绑定奖励',8=>"后台管理员添加"];
        return isset($data[$value]) ? $data[$value] : $value;
    }
    public function getMoneyTypeAttr($value)
    {
        $data = [0=>"没有变动",1=>"增加",2=>"减少"];
        return isset($data[$value]) ? $data[$value] : $value;
    }
}