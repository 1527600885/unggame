<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use app\api\model\User as UserModel;
use app\api\model\UserSign;
use app\api\model\v2\AccountType;
use app\api\model\v2\ChatRecord;
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
            if($this->request->userInfo["is_check"] !=1){
                $amount = 20;
                $content='{user.addmobile}'.$amount.'{capital.money}';
                $admin_content='用户'.$this->request->userInfo->nickname.'添加校验手机资金增加'.$amount.'美元';
                $save['balance'] = bcadd($this->request->userInfo->balance,$amount,4);
                $save['is_check'] = 1;
                capital_flow($this->request->userInfo->id,$this->request->userInfo->id,7,1,$amount,$save['balance'],$content,$admin_content);
                $amount=5;
                if($invite_one_uid= $this->request->userInfo['invite_one_uid']){
                    //更新一级邀请人数
//                    $one_count = UserModel::where("id",$invite_one_uid)->where("is_check",1)->count() + 1;
//                    UserModel::where('id',$invite_one_uid)->inc('balance',$amount)->update(['invite_one_num'=>$one_count]);
                    $invite_one_list = \app\api\model\User::where("id",$invite_one_uid)->find();
                    //添加资金列表
                    $userbalance=$invite_one_list->balance;
                    $content='{user.inviteusers} '.$this->request->userInfo['nickname'].' {user.inviteregister} $'.$amount.' reward';
                    $admin_content='用户'.$invite_one_list->nickname.'邀请用户'.$this->request->userInfo['nickname'].'注册获得'.$amount.'美金';
                    capital_flow($invite_one_uid,$this->request->userInfo['id'],5,1,$amount,$userbalance,$content,$admin_content);
                }

//                //更新二级邀请人数
//                if($invite_two_uid= $this->request->userInfo['invite_two_uid']){
//                    $two_count = UserModel::where("id",$invite_two_uid)->where("is_check",1)->count()+1;
//                    UserModel::where("id",$invite_two_uid)->update(["invite_two_num"=>$two_count]);
//                }
//                //更新三级邀请人数
//                if($invite_three_uid= $this->request->userInfo['invite_three_uid']){
//                    $three_count = UserModel::where("id",$invite_three_uid)->where("is_check",1)->count()+1;
//                    UserModel::where("id",$invite_three_uid)->update(["invite_three_num"=>$three_count]);
//                }

            }
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
      $lists =  Order::where("uid",$this->request->userInfo['id'])->append(["type_text","status_text"])->order("id desc")->paginate(10);
      $this->success("获取成功",$lists);
    }
    public function getSignData(){
        $last = UserSign::where("user_id",$this->request->userInfo['id'])
            ->order("id desc")
            ->find();
        if(!$last || (($last['last_sign_time']+3600) < time())){
            $signData = ["canSign"=>1];
        }else{
            $signData = ["canSign"=>0,["signTime"=>($last['last_sign_time']+3600 - time())]];
        }
        $message_num = ChatRecord::where("ftoid",$this->request->userInfo['game_account'])->where("state",0)->count();
        $signData['message_num'] = $message_num;
        $this->success("success",$signData);
    }
    public function selfdelete()
    {
        $this->request->userInfo->is_deleted = 1;
        $this->request->userInfo->save();
        $this->success("success");
    }
}