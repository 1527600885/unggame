<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use app\api\model\User as UserModel;
use think\facade\Validate;

class User extends BaseController
{
    public function checkAccount()
    {
        $account = input("post.account","");
        $data = UserModel::where("id",$this->request->userInfo['id'])->where("email,mobile,is_check")->find();
        if($data['is_check'] == 0 && $account){
            $check_type = !empty($data['email']) ? "email" : "mobile";
            $check_account = $data[$check_type];
            if($account!=$check_account) $this->error("Please verify the {$check_type}  that you used during registration first.");
        }

        $code = input("post.code","");
        $is_fill = input("post.is_fill",1);
        $type = input("post.type");
        if(!$is_fill){
            $account = UserModel::where("id",$this->request->userInfo['id'])->value($type);
        }
        $cache = cache($account);
        if(!$cache || $code != $cache)
        {
            $this->error(lang("user.codeerror"));
        }
        if($is_fill && UserModel::where($type,$account)->find()){
            $this->error($type == 'email' ? lang("user.emailoccupy"): lang("user.mobileexistence"));
        }
        $save = [
            $type=>$account,
            "update_time"=>date("Y-m-d H:i:s")
        ];
        if(!$this->request->userInfo["is_check_{$type}"]){
            $amount = 10;
            $content='{user.addmobile}'.$amount.'{capital.money}';
            $admin_content='用户'.$this->request->userInfo->nickname.'添加校验手机资金增加'.$amount.'美元';
            $save['balance'] = bcadd($this->request->userInfo->balance,$amount,4);
            capital_flow($this->request->userInfo->id,$this->request->userInfo->id,7,1,$amount,$save['balance'],$content,$admin_content);
            $save["is_check_{$type}"] = 1;
        }
        $this->request->userInfo->save($save);
        $data = $this->request->userInfo->toArray();
        $data['pay_password'] = $this->request->userInfo['pay_password']==0?0:1;
        $data['password'] = '';
        $this->success("Operation successful.",$data);
    }

}