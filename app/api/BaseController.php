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

namespace app\api;

use app\common\lib\Redis;
use think\App;
use think\exception\ValidateException;
use think\Response;
use think\exception\HttpResponseException;
use app\api\model\User;
use app\api\model\UserToken;
use think\facade\Cache;
// use GeoIp2\WebService\Client;
/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;
	/**
	 * 系统默认语言
	 * @var lang
	 */
	protected $lang;
	/**
	 * 游戏语言
	 * @var gamelang
	 */
	protected $gamelang;
	/**
	 * 系统域名
	 * @var host
	 */
	protected $host;
	/**
	 * 无需登录的方法
	 * @var array
	 */
	protected $noNeedLogin = [];
	/**
	 * 用户鉴权
	 */
	protected $middleware = [];
	/**
	 * 不强制鉴权，获取用户资料
	 */
	protected $nologuserinfo;

	protected $noNeedCheckIp = [];
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
		$this->host = $this->request->domain();
		$method=$this->request->method();
		$this->lang = $this->request->header('Accept-Language');
		// dump(config('lang.extend_list'));exit;
		if(!in_array($this->lang, config('lang.allow_lang_list'))){
			//当系统不存在的语言包是默认使用英文
			$this->lang = 'en-us';
		}
		$this->gamelang=$this->gameslang($this->lang);
		$action = strtolower($this->request->action());
		$module = app('http')->getName();
		//只限制接口模块验证
		if($module=="api"){
		    if(!in_array($action,$this->noNeedCheckIp) || in_array("*",$this->noNeedCheckIp))
		    {
		        $ip = $this->request->ip();
		        $iswhite = (new Redis())->getRedis()->get("ip_white_{$ip}");
                //获取访问的目标地区
                $country=getipcountry($ip);
                if(!$iswhite && in_array($country['country'],["中国","香港","澳门"])){
                    $this->error(lang('system.iperror'),$country,407);
                }
            }
			if (in_array($action,$this->noNeedLogin)||in_array('*',$this->noNeedLogin)){
				  if($this->request->header('accept-token')){
					  $this->nologuserinfo=$this->getuserinfo($this->request->header('accept-token'));
				  }
				  return true;
			}else{
				// 用户鉴权中间件
				$this->middleware[]='\app\api\middleware\AuthCheck::class';
			}
		}
	}
	/**
	  * 对接收数据进行处理
	  * @param mixed  $type   需要处理的类型
	  * @param int    $code   错误码，默认为1
	  * @param string $type   输出类型
	  * @param array  $header 发送的 Header 信息
	  */
	protected function process(){
		$key=cache('key');
		$param=input('');
		if($key){
			foreach($param as $k=>$v){
				$param[$k]=authcode($v,$key['public'],$key['private']);
			}
		}
		return $param;
	}
	/**
	  * 操作成功返回的数据layui使用
	  * @param string $msg    提示信息
	  * @param mixed  $data   要返回的数据
	  * @param int    $code   错误码，默认为1
	  * @param string $type   输出类型
	  * @param array  $header 发送的 Header 信息
	  */
	 protected function laysuccess($msg = '', $data = [], $code = 0)
	 {
	
		 $result = [
			 'code' => $code,
			 'msg'  => $msg,
		 ];
		 $result=array_merge($result,$data);        
		 $response = Response::create($result,'json',200)->header([]);
		 throw new HttpResponseException($response);
	 }
	/**
	 * 操作成功返回的数据
	 * @param string $msg    提示信息
	 * @param mixed  $data   要返回的数据
	 * @param int    $code   错误码，默认为1
	 * @param string $type   输出类型
	 * @param array  $header 发送的 Header 信息
	 */
	protected function success($msg = '', $data = null, $code = 1, $type = null, array $header = [])
	{
		$this->result($msg, $data, $code, $type, $header);
	}
	
	
	/**
	 * 操作失败返回的数据
	 * @param string $msg    提示信息
	 * @param mixed  $data   要返回的数据
	 * @param int    $code   错误码，默认为0
	 * @param string $type   输出类型
	 * @param array  $header 发送的 Header 信息
	 */
	protected function error($msg = '', $data = null, $code = 0, $type = null, array $header = [])
	{
		$this->result($msg, $data, $code, $type, $header);
	}
	
	/**
	 * 返回封装后的 API 数据到客户端
	 * @access protected
	 * @param mixed  $msg    提示信息
	 * @param mixed  $data   要返回的数据
	 * @param int    $code   错误码，默认为0
	 * @param string $type   输出类型，支持json/xml/jsonp
	 * @param array  $header 发送的 Header 信息
	 * @return void
	 * @throws HttpResponseException
	 */
	protected function result($msg, $data = null, $code = 0, $type = null, array $header = [])
	{
		$result = [
			'status' => 1,//LayUI接口返回值
			'code' => $code,
			'msg'  => $msg,
			'data' => $data,
		];
		// 如果未设置类型则自动判断
		$type = 'json';
		if (isset($header['statuscode'])){
			$code = $header['statuscode'];
			unset($header['statuscode']);
		} else {
			//未设置状态码,根据code值判断
			$code = 200;
		}
		$response = Response::create($result, $type, $code)->header($header);
		throw new HttpResponseException($response);
	}
	/**
	 * 系统语言转换成游戏对应的语言
	 * @access protected
	 * @param mixed  $lang   系统语言
	 * @return string $gamelang  返回游戏对应的语言
	 */
	protected function gameslang($lang){
		$config_game_lang=config('lang.game_lang_list');
		$games_lang=$config_game_lang[$lang];
		return $games_lang;
	}
	/**
	 * 不强制登录获取用户的信息
	 * @access protected
	 * @param mixed  $token   登录凭证
	 */
    protected function getuserinfo($token){
        $time=14*24;
        $nologuserinfo=null;
        $id = UserToken::where("token", $token)->whereTime("create_time","-$time hours")->value('user_id');
        //$password = User::where('id', $id)->value('password');
        $ip  = Cache::get("user_ip_{$id}");
        if(!$ip){
            $ip = $this->request->ip();
            Cache::set("user_ip_{$id}",$this->request->ip(),14*24*3600);
        }
        if($ip == $this->request->ip()){
            $nologuserinfo = User::with(['group'])->where('id', $id)->where('status', 1)->find();
        }
// 		if (password_verify($id . $this->request->ip() . $password, $token)) {
        //	$nologuserinfo = User::with(['group'])->where('id', $id)->where('status', 1)->find();
// 		}
        return $nologuserinfo;
    }
}
