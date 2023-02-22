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
declare (strict_types = 1);

namespace onekey;

use phpmailer\phpmailer;
use think\facade\Validate;
use app\admin\model\Config;

class Email
{
    /**
    * @param 收件人邮箱 
    * @param 邮件标题
    * @param 邮件内容
    */
    public static function send(string $email, string $title, string $body): array
    {
        $email_data=[
                // [
                //     "email"  =>  "service@unicgm.com",
                //     "password" => "Pf2utX1ErLZwZtwd",
                //     "sender"   => "UNG GAME"
                // ],
                // [
                //     "email"  =>  "service@unggame.com",
                //     "password" => "BBurNSkt9zXSYNdOuA01lTSp56PYxaO7BTB/HW3jOiVx",
                //     "sender"   => "UNG GAME"
                // ],
                [
                    "email"  =>  "admin@unggame.com",
                    "password" => "fvboigixgjgedvcm",
                    "sender"   => "UNG GAME"
                ],
                // [
                //     "email"  =>  "service3@unicgm.com",
                //     "password" => "DKeBGnVrqjnK4v3r",
                //     "sender"   => "UNG GAME"
                // ],
                // [
                //     "email"  =>  "service4@unicgm.com",
                //     "password" => "cNYt6wLXG7zB3B1z",
                //     "sender"   => "UNG GAME"
                // ],
                // [
                //     "email"  =>  "service5@unicgm.com",
                //     "password" => "jwG17MqEMWNwxwwA",
                //     "sender"   => "UNG GAME"
                // ],
            ];
        $config = Config::getVal('email');
        // $rule = [];
        // $rule['smtp']      = 'require';
        // $rule['email']     = 'require|email';
        // $rule['password']  = 'require';
        // $rule['sender']    = 'require';
        // $rule['sendstyle'] = 'require';
        // $validate = Validate::rule($rule);
        // if (!$validate->check($config)) {
        //     return ['status' => 'error', 'message' => lang('system.email_configure_error')];
        // }
        // $num = rand(0,3);
        $num =0;
        // var_dump($email_data[$num]);
        // die;
        // $host       = $config['smtp'];      // 发送方的SMTP服务器地址 
        // $username   = $config['email'];     // 发送方的邮箱用户名
        // $password   = $config['password'];  // 发送方的邮箱客户端授权密码
        // $from       = $config['email'];     // 发件人邮箱
        // $fromTitle  = $config['sender'];    // 发件人名字  如：(xxxx@qq.com）
        // $replyTo    = $config['email'];     // 回复人邮箱
        // $replyTitle = $config['sender'];    // 回复人名字
        // $smtpSecure = $config['sendstyle']; // 使用的协议方式，如ssl/tls
        $host       = $config['smtp'];      // 发送方的SMTP服务器地址 
        $username   = $email_data[$num]['email'];     // 发送方的邮箱用户名
        $password   = $email_data[$num]['password'];  // 发送方的邮箱客户端授权密码
        $from       = $email_data[$num]['email'];     // 发件人邮箱
        $fromTitle  = $email_data[$num]['sender'];    // 发件人名字  如：(xxxx@qq.com）
        $replyTo    = $email_data[$num]['email'];     // 回复人邮箱
        $replyTitle = $email_data[$num]['sender'];    // 回复人名字
        $smtpSecure = $config['sendstyle']; // 使用的协议方式，如ssl/tls
        $port       = $config['sendstyle'] === 'ssl' ? 465 : 25;
        $body       = preg_replace('/\\\\/','', $body);
        $mail       = new PHPMailer();  
        $mail->CharSet    = "utf8"; 
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = "admin@unggame.com";
        $mail->Password   = "fvboigixgjgedvcm";
        $mail->SMTPSecure  ="tls";;
        $mail->Priority    = 1;
        $mail->Port        = 587;  
        $mail->Subject    = $title;
        $mail->isSMTP();
        $mail->setLanguage('zh_cn');
        $mail->setFrom($from,$fromTitle);
        $mail->addAddress($email);
        $mail->addReplyTo($replyTo,$replyTitle);
        // $mail->Encoding    = '8bit';
        $mail->MsgHTML($body);
        $mail->IsHTML(true);
        // $mail->SMTPDebug = 2; 
        // $mail->SMTPDebug = true;
        $message = $mail->send();
        // echo "mail::send()";
        // print_r($message); die;
        return $message === true ? ['status' => 'success', 'message' => '获取成功'] : ['status' => 'error', 'message' => $message];
    }
}