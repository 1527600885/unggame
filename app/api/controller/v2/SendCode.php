<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use app\api\model\User as UserModel;
use think\facade\Validate;

class SendCode extends BaseController
{
    public function index()
    {
        $account = input("post.account","");
        $type = input("post.type","email");
        $is_fill = input("post.is_fill",1);
        $is_exit = input("post.is_exit",0);
        if($type == "email")
        {
            if($is_fill)
            {
                if(!Validate::is($account,"email"))  $this->error(lang("user.email"));
                if($is_exit &&  UserModel::where("email",$account)->find())  $this->error(lang("user.emailoccupy"));

            }else{
                $account = UserModel::where("id",$this->request->userInfo['id'])->value($type);
            }
            $this->success("success",\app\api\addons\sendCode::email($account, 'index_bind_email_code', lang('user.bindemail')));
        }else if($type == "mobile")
        {
            if($is_fill)
            {
                if(!Validate::is($account,"number") || Validate::length($account,"4,16"))
                {
                    $this->error(lang("user.mobileError"));
                }
                if($is_exit && UserModel::where("mobile",$account)->find())
                {
                    $this->error(lang("user.mobileexistence"));
                }
                $phone = '+'.input('uncode').$account;
            }else{
                $phone = '+'.$this->request->userInfo['uncode'].UserModel::where("id",$this->request->userInfo['id'])->value($type);;
            }
            $this->success("success",\app\api\addons\sendCode::singleSend($phone));
        }
    }
}