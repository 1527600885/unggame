<?php
namespace app\common\pay;
use app\api\model\Order as Ordermodel;
use app\api\model\CurrencyAll as CurrencyAllmodel;
use app\api\model\CapitalFlow as CapitalFlowmodel;
use app\api\model\User as UserModel;
use think\facade\Db;
class PayService {
	
	//下单
	public function place($paymentinfo,$data){
		$data['sign']=$this->sign($paymentinfo,$data);
		$data['version']='1.0';
		$data['subject']='充值游戏币';
		$ret  = $this->http_post_json(json_encode($data),$paymentinfo['url']);
		if(is_array($ret)){
			// $code=$ret[0];
			if($ret[0]==200){
				$ret  = json_decode($ret[1],true); 
				// $ret['code']=$code;
			}
		}else{
			$ret  = json_decode($ret,true); 
		}
		return $ret;
	}
	//回调处理
	public function notify($postdata){
		if(!is_array($postdata)){
			$postdata  = json_decode($postdata,true); 
		}
		if($postdata['status']=='SUCCESS'){
			$orderinfo=Ordermodel::where(['mer_order_no'=>$postdata['merchantOrderId'],'status'=>0])->find();
			if($orderinfo){
				$userinfo=UserModel::where('id',$orderinfo->uid)->find();
				Db::transaction(function () use ($postdata,$orderinfo,$userinfo) {
				    Ordermodel::where('mer_order_no',$postdata['merchantOrderId'])->update(['time2'=>strtotime($postdata['successTime']),'status'=>1,'sign'=>$postdata['sign']]);
				    UserModel::where('id',$userinfo->id)->inc('balance',$orderinfo->money)->update();
				});
				$content='{capital.content}'.$orderinfo->money.'{capital.money}';
				$admin_content='用户'.$userinfo->nickname.'通过在线充值获得'.$orderinfo->money.'美元';
				capital_flow($userinfo->id,$orderinfo->id,1,1,$orderinfo->money,bcadd($userinfo->balance,$orderinfo->money,2),$content,$admin_content);
				return 'success';
			}else{
				return 'success';
			}
		}else{
		}
		// if(array_key_exists('status',$postdata)){
		// 	if($postdata['status']=='SUCCESS'){
		// 		$postdata['tradeResult']=1;
		// 		$postdata['mchOrderNo']=$postdata['mer_order_no'];
		// 		$postdata['orderDate']=$postdata['pay_time'];
		// 	}
		// }
		// if($postdata['tradeResult']==1){
		// 	$orderinfo=Db::name('mk_order')->where(['mer_order_no'=>$postdata['mchOrderNo'],'status'=>0])->find();
		// 	if($orderinfo){
		// 		Db::name('mk_order')->where('mer_order_no',$postdata['mchOrderNo'])->update(['time2'=>strtotime($postdata['orderDate']),'status'=>1]);
		// 		return 'success';
		// 	}else{
		// 		return 'fail';
		// 	}
		// }
	}
	//签名
	public function sign($paymentinfo,$data){
		ksort($data);
		$str = '';
		foreach ($data as $k => $v){
		  if(!empty($v)){
			$str .=(string) $k.'='.$v.'&';
		  }
		}  
		if($paymentinfo['md5key']){
			$key=$paymentinfo['md5key'];
			$str = rtrim($str,'&')."&key=".$key;
			return MD5($str);
		}else{
			$key=$paymentinfo['hashkey'];
			$str = rtrim($str,'&')."&key=".$key;
			return hash_hmac('sha256',$str,$key);
		}
	}
	//hash签名
	public function hashsign($paymentinfo,$data){
		ksort($data);
		$str = '';
		foreach ($data as $k => $v){
		  if(!empty($v)){
			$str .=(string) $k.'='.$v.'&';
		  }
		}    
		$str = rtrim($str,'&')."&key=".$paymentinfo['md5key'];
		return hash_hmac('sha256',$str,$paymentinfo['md5key']);
	}
	//签名
	public function othersign($paymentinfo,$data){
		ksort($data);
		$str = '';
		foreach ($data as $k => $v){
		  if(!empty($v)){
			$str .=(string) $k.'='.$v.'&';
		  }
		}    
		$str = rtrim($str,'&');
		$encrypted = '';
		//替换成自己的私钥
		$pem = chunk_split($paymentinfo['privatekey'], 64, "\n");
		$pem = "-----BEGIN PRIVATE KEY-----\n" . $pem . "-----END PRIVATE KEY-----\n";
		$private_key = openssl_pkey_get_private($pem);
		$crypto = '';
		foreach (str_split($str, 117) as $chunk) {
		  openssl_private_encrypt($chunk, $encryptData, $private_key);
		  $crypto .= $encryptData;
		}
		$encrypted = base64_encode($crypto);
		$encrypted = str_replace(array('+','/','='),array('-','_',''),$encrypted);
		
		$sign=$encrypted;
		return $sign;
	}
	/**
	 * PHP发送Json对象数据
	 *
	 * @param $url 请求url
	 * @param $jsonStr 发送的json字符串
	 * @return array
	 */
	function http_post_json($jsonStr,$url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json; charset=utf-8',
				'Content-Length: ' . strlen($jsonStr)
			)
		);
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	 
		return array($httpCode, $response);
	}

	//curl提交
	public function httpPost($postData,$url,$headers=null){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		if(!$data){
			$err_str=curl_error($ch);
			curl_close($ch);
			return $err_str;
		}else{
			curl_close($ch);
			return $data;
		}
	}
	//form表单提交
	public function httpform($postData,$url){
		$headers = array('Content-Type: application/x-www-form-urlencoded');
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData)); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($curl); // 执行操作
		if (curl_errno($curl)) {
		    echo 'Errno'.curl_error($curl);//捕抓异常
		}
		curl_close($curl); // 关闭CURL会话
		return $result;
	}
}
?>