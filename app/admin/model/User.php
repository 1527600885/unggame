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
namespace app\admin\model;

use think\Model;

class User extends Model
{
    // 设置json类型字段
    protected $json = ['field'];

    protected $jsonAssoc = true;
    
    // 关联模型
    public function group()
    {
        return $this->hasOne(UserGroup::class, 'id', 'group_id')->bind([
            'group_title' => 'title'
        ]);
    }
    public function inviteName()
    {
        return $this->belongsTo(User::class, 'invite_one_uid')->bind([
            'invite_name' => 'nickname'
        ]);
    }
    public function idcard()
    {
        return $this->belongsTo(UserIdcard::class,"user_id")->bind(["idCard_image"=>"idCard_image","idCard_image_with_hand"=>"idCard_image_with_hand"]);
    }
    // 搜索器
    public function setPasswordAttr($value, $array)
    {
        if (! empty($value)) {
            $password = password_hash($value, PASSWORD_BCRYPT, ['cost' => 12]); 
            $this->set('password', $password);
        }
    }
    public function searchInviteUidAttr($query,$value,$array)
    {
        if(!empty($value)){
            $query->where("id",$value);
        }
    }
    public function searchInviteOneUidAttr($query, $value, $array){
        if (! empty($value)) {
            $query->where("invite_one_uid",$value);
        }
    }
    public function searchInviteTwoUidAttr($query, $value, $array){
        if (! empty($value)) {
            $query->where("invite_two_uid",$value);
        }
    }
    public function searchInviteThreeUidAttr($query, $value, $array){
        if (! empty($value)) {
            $query->where("invite_three_uid",$value);
        }
    }
    public function searchKeywordAttr($query, $value, $array)
    {
        if (! empty($value)) {
            $query->where("nickname|email|mobile|game_account|id",'like', '%' . $value . '%');
        }
    }

    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) { 
            $query->whereBetweenTime('create_time', $value[0], $value[1]);
        }
    }

    public function searchStatusAttr($query, $value, $array)
    {
        if ($value !== '') {
            $query->where("status", '=', $value);
        }
    }
    public function getOtherAccountsAttr($value)
    {
        if(!empty($value)){
            $data = json_decode($value,true);
            return $data['type'].":".$data['account'];
        }
    }
    // 获取器
    public function getUrlAttr($value, $array)
    {
        return index_url('user/info', ['id' => $array['id']]);
    }

    public function getPasswordAttr($value)
    {
        return "";
    }

    public function getCStatusAttr($value, $array)
    {
        return $array['status'] === 1 ? '正常' : '屏蔽';
    }

    // 修改器
    public function setFieldAttr($value, $array)
    {
        $field = [];
        foreach ($value as $key => $val) {
            $field[$val['field']] = $val['value'];
        }
        return $field;
    }
}