<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use app\api\model\User as UserModel;
use app\api\model\v2\AccountType;
use app\api\model\v2\Order;
use think\facade\Validate;
use think\response\Json;

class User extends BaseController
{
    public function checkAccount()
    {
        $key = $account = input("post.account","");
        $data = UserModel::where("id",$this->request->userInfo['id'])->field("email,mobile,is_check")->find();
        if($data['is_check'] == 0 && $account){
            $check_type = !empty($data['email']) ? "email" : "mobile";
            $check_account = $data[$check_type];
            if($account!=$check_account) $this->error("Please verify the {$check_type}  that you used during registration first.","",466);
        }
        $code = input("post.code","");
        $is_fill = input("post.is_fill",1);
        $type = input("post.type");
        if($type == 'mobile') $uncode = input("post.uncode","");
        if(!$is_fill){
            $key = $account = UserModel::where("id",$this->request->userInfo['id'])->value($type);
            if($type == "mobile"){
                $uncode = UserModel::where("id",$this->request->userInfo['id'])->value("uncode");
            }
        }
        $save = [
            $type=>$account,
            "update_time"=>date("Y-m-d H:i:s")
        ];
        if($type == "mobile")  {
            $save['uncode'] = $uncode;
            $key  = "+{$uncode}{$account}";
        }
        $cache = cache($key);
        if(!$cache || $code != $cache)
        {
            $this->error(lang("user.codeerror"));
        }
        if($is_fill && UserModel::where($type,$account)->find()){
            $this->error($type == 'email' ? lang("user.emailoccupy"): lang("user.mobileexistence"));
        }

        if(!$this->request->userInfo["is_check_{$type}"]){
            $amount = 10;
            $content='{user.addmobile}'.$amount.'{capital.money}';
            $admin_content='用户'.$this->request->userInfo->nickname.'添加校验手机资金增加'.$amount.'美元';
            $save['balance'] = bcadd($this->request->userInfo->balance,$amount,4);
            $save['is_check'] = 1;
            capital_flow($this->request->userInfo->id,$this->request->userInfo->id,7,1,$amount,$save['balance'],$content,$admin_content);
            $save["is_check_{$type}"] = 1;
        }
        $this->request->userInfo->save($save);
        $data = $this->request->userInfo->toArray();
        $data['pay_password'] = $this->request->userInfo['pay_password']==0?0:1;
        $data['password'] = '';
        $this->success("Operation successful.",$data);
    }
    public function modifyPassword()
    {
        $type = input("post.type");
        $account = UserModel::where("id",$this->request->userInfo['id'])->value($type);
        if($type == "mobile"){
            $account ='+'.$this->request->userInfo['uncode'].$account;
        }
        $cache = cache($account);
        $code = input("post.code","");
        if(!$cache || $code != $cache)
        {
            $this->error(lang("user.codeerror"));
        }
        $password = input("post.password/s");
        $this->request->userInfo->save(compact("password"));
        $this->success("Operation successful.");
    }
    public function modifyPayPassword()
    {
        $type = input("post.type");
        $account = UserModel::where("id",$this->request->userInfo['id'])->value($type);
        if($type == "mobile"){
            $account ='+'.$this->request->userInfo['uncode'].$account;
        }
        $cache = cache($account);
        $code = input("post.code","");
        if(!$cache || $code != $cache)
        {
            $this->error(lang("user.codeerror"));
        }
        $pay_password = input("post.pay_password/s");
        $this->request->userInfo->save(['pay_paasword'=>$pay_password]);
        $this->success("Operation successful.");
    }
    public function addOtherAccounts()
    {
        $post = input("post.");
        $names = AccountType::column("name");
        if(!in_array($post['type'],$names))
        {
            $this->error("Account type is wrong");
        }
        $this->request->userInfo->save(["other_accounts"=>$post]);
        $this->success("Operation successful.");
    }
    public function getOrderList()
    {
      $lists =  Order::where("uid",$this->request->userInfo['id'])->order("id desc")->paginate(10);
      $this->success("获取成功",$lists);
    }
}