<?php
namespace plugins\statistics\addons;

class Request
{
    /**
     * 获取访问浏览器
     */
    public static function browser()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
            if( (false == strpos($agent,'MSIE')) && (strpos($agent, 'Trident')!==FALSE) ){
                return 'IE11.0';
            }
            if(false!==strpos($agent,'MSIE 10.0')){
                return 'IE10.0';
            }
            if(false!==strpos($agent,'MSIE 9.0')){
                return 'IE9.0';
            }
            if(false!==strpos($agent,'MSIE 8.0')){
                return 'IE8.0';
            }
            if(false!==strpos($agent,'MSIE 7.0')){
                return 'IE7.0';
            }
            if(false!==strpos($agent,'MSIE 6.0')){
                return 'IE6.0';
            }
            if(false!==strpos($agent,'Edge')){
                return 'Edge';
            }
            if(false!==strpos($agent,'Firefox')){
                return 'Firefox';
            }
            if(false!==strpos($agent,'Chrome')){
                return 'Chrome';
            }
            if(false!==strpos($agent,'Safari')){
                return 'Safari';
            }
            if(false!==strpos($agent,'Opera')){
                return 'Opera';
            }
            if(false!==strpos($agent,'360SE')){
                return '360SE';
            }
            if(false!==strpos($agent,'MicroMessage')){
                return 'weChat';
            }
            return '未知';
        } else {
            return '未知';
        }
    }

    /**
     * 获取来源网站、搜索词
     */
    public static function keyword()
    {
        $name ='';
        $from = '';
        if (isset($_SERVER["HTTP_REFERER"])) {
            $referer = $_SERVER["HTTP_REFERER"];
            if (strstr( $referer, 'baidu.com')) {
                $name = 'www.baidu.com';
                $from = '百度';
            } elseif(strstr( $referer, 'google.com') or strstr( $referer, 'google.cn')) {
                $name = 'www.google.com.hk';
                $from = '谷歌';
            } elseif(strstr( $referer, 'so.com')) {
                $name = 'www.so.com';
                $from = '360'; 
            } elseif(strstr( $referer, 'sogou.com')) {
                $name = 'www.sogou.com';
                $from = '搜狗';
            } elseif(strstr( $referer, 'facebook.com')) {
                $name = 'www.facebook.com';
                $from = 'facebook';
            } elseif(strstr( $referer, 'bing.com')) {
                $name = 'www.bing.com';
                $from = '必应';
            } elseif(strstr( $referer, 'yahoo.com')) {
                $name = 'www.yahoo.com';
                $from = '雅虎';
            } elseif(strstr( $referer, 'soso.com')) {
                $name = 'www.soso.com';
                $from = '搜搜';
            }
        }
        return array('name'=>$name, 'from'=>$from);
    }

    /**
     * 获取当前来路URL
     */
    public static function referer()
    {
        $referer = "";
        if (isset($_SERVER["HTTP_REFERER"])) {
            $referer = $_SERVER["HTTP_REFERER"];   
        }
        return $referer;  
    }

    /**
     * 获取当前操作系统
     */
    public static function os()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/win/i', $agent) && strpos($agent, '95')){
                return 'Windows 95';
            }
            if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')){
                return 'Windows ME';
            }
            if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)){
                return 'Windows 98';
            }
            if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)){
                return 'Windows NT';
            }
            if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)){
                return 'Windows Vista';
            }
            if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)){
                return 'Windows 7';
            }
            if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)){
                return 'Windows 8';
            }
            if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)){
                return 'Windows 10';
            }
            if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)){
                return 'Windows XP';
            }
            if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)){
                return 'Windows 2000';
            }
            if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)){
                return 'Windows 32';
            }
            if (preg_match('/linux/i', $agent)){
                return 'Linux';
            }
            if (preg_match('/unix/i', $agent)){
                return 'Unix';
            }
            if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)){
                return 'SunOS';
            }
            if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)){
                return 'IBM OS/2';
            }
            if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)){
                return 'Macintosh';
            }
            if (preg_match('/PowerPC/i', $agent)){
                return 'PowerPC';
            }
            if (preg_match('/AIX/i', $agent)){
                return 'AIX';
            }
            if (preg_match('/HPUX/i', $agent)){
                return 'HPUX';
            }
            if (preg_match('/NetBSD/i', $agent)){
                return 'NetBSD';
            }
            if (preg_match('/BSD/i', $agent)){
                return 'BSD';
            }
            if (preg_match('/OSF1/i', $agent)){
                return 'OSF1';
            }
            if (preg_match('/IRIX/i', $agent)){
                return 'IRIX';
            }
            if (preg_match('/FreeBSD/i', $agent)){
                return 'FreeBSD';
            }
            if (preg_match('/teleport/i', $agent)){
                return 'teleport';
            }
            if (preg_match('/flashget/i', $agent)){
                return 'flashget';
            }
            if (preg_match('/webzip/i', $agent)){
                return 'webzip';
            }
            if (preg_match('/offline/i', $agent)){
                return 'offline';
            }
            if(strpos($agent, 'iphone')){
                return 'iphone';
            }
            if(strpos($agent, 'ipad')){
                return 'ipad';
            }
            if(strpos($agent, 'android')){
                return 'android';
            }
            return '未知';
        } else {
            return '未知';
        }
    }
}