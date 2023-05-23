<?php
// +----------------------------------------------------------------------
// | OneKeyAdmin [ Believe that you can do better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2023 http://onekeyadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MUKE <513038996@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Announcement;
use app\api\model\UngUser;
use think\facade\View;
use think\exception\ValidateException;
use app\admin\BaseController;
use app\admin\model\Config;
use app\admin\model\UserGroup;
use app\admin\model\User as UserModel;
use app\admin\validate\User as UserValidate;
/**
 * 用户管理
 */
class User extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input  = input('post.');
            $input['invite_uid'] = input("param.invite_uid",0);
            $input['invite_one_uid'] = input("param.invite_one_uid",0);
            $input['invite_two_uid'] = input("param.invite_two_uid",0);
            $input['invite_three_uid'] = input("param.invite_three_uid",0);
            $search = ['keyword','date','status','invite_one_uid','invite_three_uid','invite_two_uid',"invite_uid"];;
            $append = ['url'];
            $order  = [$input['prop'] => $input['order']];
            $count  = UserModel::withSearch($search, $input)->count();
            $data   = UserModel::withSearch($search, $input)->append($append)->with(['group','inviteName'])->order($order)->page($input['page'], $input['pageSize'])->select()->each(function($item){
                $url = "/game_admin/#/user/edit.html?id=".$item['id'];
                $item['cover'] = "<a href='{$url}'><img src='{$item['cover']}' /></a>";
                $item['capital_log'] = "<a style='color: #0000FF' href='/game_admin/capitalFlow/index?uid={$item['id']}'>账单记录</a>";
                $address = getipcountry($item['login_ip']);
                $item['country'] = $address['country'];
                $item['province'] = $address['province'];
                $item['city'] = $address['city'];
                $item['invite_name'] = "<a  style='color: #0000FF' href='/game_admin/user/index?invite_uid={$item['invite_one_uid']}'>{$item['invite_name']}</a>";
                $item['invite_one_num'] = "<a  style='color: red' href='/game_admin/user/index?invite_one_uid={$item['id']}'>{$item['invite_one_num']}</a>";
                $item['invite_two_num'] = "<a   style='color: red' href='/game_admin/user/index?invite_two_uid={$item['id']}'>{$item['invite_two_num']}</a>";
                $item['invite_three_num'] = "<a  style='color: red' href='/game_admin/user/index?invite_three_uid={$item['id']}'>{$item['invite_three_num']}</a>";
                $item['balance'] = "<a style='color:darkgreen' onclick='app.showEditBalance({$item['id']})'>{$item['balance']}<a/>";
                $item['UNG'] = UngUser::where("uid",$item['id'])->value("num");
            });
			return json(['status' => 'success', 'message' => '获取成功', 'data' => $data, 'count' => $count]);
        } else {
            $group = UserGroup::where('status', 1)->order('integral', 'asc')->select();
            $invite_one_uid = input("param.invite_one_uid") ?? input("param.invite_two_uid") ??  input("param.invite_three_uid",0);
            $assign = ['group'=>$group,"invite_one_uid"=>$invite_one_uid];
            if(input("param.invite_uid")) $assign['invite_uid'] = input("param.invite_uid");
            if($invite_one_uid || input("param.invite_uid")){
                $invite_one_uid = $invite_one_uid ?: input("param.invite_uid");
                $invite_data  = UserModel::where("id",$invite_one_uid)->field('invite_one_num,invite_two_num,invite_three_num')->find();
                $assign['invite_data'] = $invite_data;
            }
            View::assign($assign);
            return View::fetch();
        }
    }
    /**
     * 保存新建的资源
     */
    public function save()
    {
        try {
            $input = input('post.');
            validate(UserValidate::class)->scene('save')->check($input);
            if (! empty($input['mobile'])) {
                if (UserModel::where('mobile', $input['mobile'])->value('id')) {
                    return json(['status' => 'error', 'message' => '手机号已经存在！']);
                }
            }
            if (! empty($input['email'])) {
                if (UserModel::where('email', $input['email'])->value('id')) {
                    return json(['status' => 'error', 'message' => '邮箱号已经存在！']);
                }
            }
            $integral = UserGroup::where('id', $input['group_id'])->value('integral');
            $date = date('Y-m-d H:i:s');
            $input['pay_paasword']     = '';
            $input['now_integral']     = $integral;
            $input['history_integral'] = $integral;
            $input['login_ip']         = '';
            $input['login_count']      = 0;
            $input['birthday']         = $input['birthday'] ? $input['birthday'] : date('Y-m-d');
            $input['login_time']       = $date;
            $input['update_time']      = $date;
            $input['create_time']      = $date;
            $input['hide']             = 1;
            UserModel::create($input);
            return json(['status' => 'success', 'message' => '新增成功']);
        } catch ( ValidateException $e ) {
            return json(['status' => 'error', 'message' => $e->getError()]);
        }
    }

    /**
     * 保存更新的资源
     */
    public function update()
    {
        try {
            $input = input('post.');
            validate(UserValidate::class)->check($input);
            $where[] = ['id', '<>', $input['id']];
            if (! empty($input['mobile'])) {
                if (UserModel::where('mobile', $input['mobile'])->where($where)->value('id')) {
                    return json(['status' => 'error', 'message' => '手机号已经存在！']);
                }
            }
            if (! empty($input['email'])) {
                if (UserModel::where('email', $input['email'])->where($where)->value('id')) {
                    return json(['status' => 'error', 'message' => '邮箱号已经存在！']);
                }
            }
            $save = UserModel::find($input['id']);
            $integral = UserGroup::where('id', $input['group_id'])->value('integral');
            if ($input['group_id'] != $save->group_id) {
                $save->history_integral = $integral;
            }
            if (! empty($input['password'])) {
                $save->password = $input['password'];
            }
            $save->group_id         = $input['group_id'];
            $save->nickname         = $input['nickname'];
            $save->sex              = $input['sex'];
            $save->email            = $input['email'];
            $save->mobile           = $input['mobile'];
            $save->cover            = $input['cover'];
            $save->describe         = $input['describe'];
            $save->birthday         = $input['birthday'];
            $save->now_integral     = $input['now_integral'];
            $save->balance          = $input['balance'];
            $save->create_time      = $input['create_time'];
            $save->status           = $input['status'];
            $save->save();
            return json(['status' => 'success', 'message' => '修改成功']);
        } catch ( ValidateException $e ) {
            return json(['status' => 'error', 'message' => $e->getError()]);
        }
    }

    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            UserModel::destroy(input('post.ids'));
            return json(['status' => 'success', 'message' => '删除成功']);
        }
    }
    public function edit()
    {
        if ($this->request->isPost()) {
            $post = input("post.");
            if($post['name'] == "balance_status"){
                if($post['value'] == 0){
                    Announcement::create(["type"=>2,"desc"=>"Account Frozen","content"=>"There is an abnormality in your account. In order to protect the safety of your funds, you are temporarily unable to withdraw cash, play games and invest. Please contact the official customer service of the platform to deal with it.","user_id"=>$post['id'],"create_time"=>time()]);
                }else{
                    Announcement::create(["type"=>2,"desc"=>"Account Unfreeze","content"=>"Please follow the platform rules and guidelines.","user_id"=>$post['id'],"create_time"=>time()]);
                }

            }
            UserModel::where("id",$post['id'])->update([$post['name']=>$post['value']]);
            return json(['status' => 'success', 'message' => '操作成功']);
        }else{
            $id = input("param.id");
            $userInfo = UserModel::where("id",$id)->find();
            $this->assign("userInfo",$userInfo);
            return View::fetch("personal");
        }
    }
    public function editMoney()
    {
        if($this->request->isPost())
        {
            $post = input("post.");
            if($post['money_type'] == "1")
            {
                UserModel::where("id",$post['id'])->inc("balance",$post['amount'])->update();

            }else{
                UserModel::where("id",$post['id'])->dec("balance",$post['amount'])->update();
            }
            $user = UserModel::where("id",$post['id'])->field("nickname,balance")->find();
            $balance = $user['balance'];
            switch ($post['type'])
            {
                case 5:
                    $content = "{user.inviteusers}{user.inviteregister}{capital.money}{$post['amount']}";
                    $admin_content = "邀请注册奖励{$post['amount']}美元";
                    break;
                case 6:
                    $content = "{Friend's recharge} reward {capital.money}{$post['amount']}";
                    $admin_content = "好友充值奖励{$post['amount']}美元";
                    break;
                case 7:
                    $content = "{admin's reward} {capital.money}{$post['amount']}";
                    $admin_content = "管理员后台添加 {$post['amount']}美元";
                    break;
                case 2:
                    $content = "{withdrawal.text}{capital.money}{$post['amount']}";
                    $admin_content = "提现{$post['amount']}美元";
                    break;
                case 3:
                    if($post['money_type'] == 1){
                        $content = "UNG Game reward {capital.money}{$post['amount']}";
                        $admin_content = "用户{$user['nickname']}玩游戏资金增加{capital.money}{$post['amount']}";
                    }else{
                        $content = "{capital.gamecontento}{$user['nickname']}{capital.gamecontenth}{capital.money}{$post['amount']}";
                        $admin_content = "用户{$user['nickname']}玩游戏资金减少{capital.money}{$post['amount']}";
                    }
                    break;
            }
            capital_flow($post['id'],$this->request->userInfo->id,8,$post['money_type'],$post['amount'],$balance,$content,$admin_content);
            return json(['status' => 'success', 'message' => '操作成功']);
        }
    }
    public function queryUserList($key ="")
    {
        $map = [];
        if($key){
            $map[] = ["nickname","like","%{$key}%"];
        }
        $data = UserModel::where($map)->field("id,nickname as value")->order("id desc")->limit(100)->select()->toArray();
        if(!$key){
            $data = array_merge([["id"=>0,"value"=>"所有人"]],$data);
        }
        return json(["code"=>0,"data"=>$data]);
    }
}
