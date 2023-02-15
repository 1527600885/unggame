<?php
namespace app\common\game;
class FtpGame
{
	public function __construct(){
		if(!function_exists('ftp_connect')){
			echo "ftp未安装";
		}
		$this->conn_id = ftp_connect('123.51.167.66');
		$this->login_result = ftp_login($this->conn_id, 'nicedoidrk', 'a123456');
		if(!$this->login_result){
			echo "登录失败！";
		}
	}
	// 获取FTP主目录列表
	public function nlist(){
		$contents = ftp_nlist($this->conn_id, ".");
		ftp_close($this->conn_id);
	}
	//测试
	public function getjson(){
		// $remote_file = '/ELOTTO/SETTLED/20210823/202108231835_0001.json';   ### 遠端檔案productpath
		// $local_file = '202108231835_0001.json';   ### 本機儲存檔案名稱//batchnamejson
		 
		// $handle = fopen($local_file, 'w');
		 
		### 連接的 FTP 伺服器
		$conn_id = ftp_connect('123.51.167.66');
		 
		### 登入 FTP, 帳號是 USERNAME, 密碼是 PASSWORD
		$login_result = ftp_login($conn_id, 'nicedoidrk', 'a123456');
		 
		 
		// 主目录列表
		$contents = ftp_nlist($conn_id, ".");
		var_dump($contents);
		echo "<br>";
		
		
		 
		 if (ftp_fget($conn_id, $handle, $remote_file, FTP_ASCII, 0)) {
		     echo "下載成功, 並儲存到 $local_file\n";
		 } else {
		     echo "下載 $remote_file 到 $local_file 失敗\n";
		 }
		 
		ftp_close($conn_id);
		// fclose($handle);
	}
}
?>