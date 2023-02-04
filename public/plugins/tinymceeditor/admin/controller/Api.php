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
namespace plugins\tinymceeditor\admin\controller;

use onekey\File;
use think\facade\View;
use app\admin\BaseController;
/**
 * 微信订阅文章
 */
class Api extends BaseController
{
    protected function initialize()
    {
        parent::initialize();
        $this->path = public_path()."upload/image/wechat/";
        $this->host = 'mp.weixin.qq.com';
        $this->head = stream_context_create(
            [
                'http' => [
                    'method'  => "GET",
                    'timeout' => 3,
                    'header'  => 
                    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9\r\n".
                    "Accept-Language: zh-CN,zh;q=0.9,en;q=0.8\r\n".
                    "Cache-Control: no-cache\r\n".
                    "Connection: keep-alive\r\n".
                    "Host: mp.weixin.qq.com\r\n".
                    "Pragma: no-cache\r\n".
                    "Referer: https://www.toutiao.com/a7030658725887214084/?log_from=4af3e99f261d1_1636962477354&wid=1636964173045\r\n".
                    "sec-ch-ua: 'Google Chrome';v='95', 'Chromium';v='95', ';Not A Brand';v='99'\r\n".
                    "sec-ch-ua-mobile: ?0\r\n".
                    "sec-ch-ua-platform: 'Windows'\r\n".
                    "Sec-Fetch-Dest: document\r\n".
                    "Sec-Fetch-Mode: navigate\r\n".
                    "Sec-Fetch-Site: same-origin\r\n".
                    "Sec-Fetch-User: ?1\r\n".
                    "Upgrade-Insecure-Requests: 1\r\n".
                    "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36\r\n"
                ]
            ]
        );
        $this->headImg = stream_context_create(
            [
                'http' => [
                    'method'  => "GET",
                    'timeout' => 3,
                    'header'  => 
                    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9\r\n"
                ]
            ]
        );
    }

    /**
     * 开始采集
     */
    public function index()
    {
        set_time_limit(0);
        $input = input();
        $parse = parse_url($input['url']);
        if ($parse['host'] ===  $this->host) {
            $html = file_get_contents($input['url'], 0, $this->head);
            $document = new \DOMDocument();
            @$document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES','UTF-8'));
            $xpath = new \DOMXPath($document);
            $query = $xpath->query('//div[@id="js_content"]');
            if ($query->length > 0) {
                $html = $document->saveHTML($query->item(0));
                $pre  = ["/\t/", "/\r\n/", "/\r/", "/\n/", "/  /", "/&amp;/", "/visibility: hidden;/", "/data-src/"];
                $rep  = ["", "", "", "", "", "&", "visibility: visible;", "src"];
                $html = preg_replace($pre, $rep, $html);
                // 远程背景图保存为本地
                foreach($xpath->query('//*/@style') as $style){
                    preg_match('/background\s*-\s*+image\s*:\s*url\s*\("*([^"]*)"*\)/i', $style->nodeValue, $match);
                    if (isset($match[1])) {
                        if (! empty($match[1]) && ! strstr($match[1], 'data:image')) {
                            $html = $this->save_image($match[1], $html);
                        }
                    }
                }
                // 远程图片保存为本地
                foreach($query->item(0)->getElementsByTagName('img') as $img){
                    $src = $img->getAttribute('data-src') === '' ? $img->getAttribute('data-croporisrc') : $img->getAttribute('data-src');
                    if (! empty($src) && ! strstr($src, 'data:image')) {
                        $html = $this->save_image($src, $html);
                    }
                }
                return json(['status' => 'success', 'message' => '获取成功', 'content' => $html]);
            } else {
                return json(['status' => 'error', 'message' => '微信文章规则改变']);
            }
        } else {
            return json(['status' => 'error', 'message' => '链接错误，正确域名为：'.$this->host]);
        }
    }

    /**
     * 图片采集
     */
    public function save_image($url, $html) {
        $type = explode('/', $this->online_filetype($url));
        if ($type[0] === 'image' && ! strstr($type[1], 'svg+xml')) {
            $filePath = $this->path . pathinfo(parse_url($url)['path'])['basename'] . '.' . $type[1];
            $urlPath  = str_replace(public_path(), '', $filePath);
            $urlPath  = request()->domain() . request()->root() . '/' . $urlPath;
            // 防止重复抓取
            if (! is_file($filePath)) {
                $contents = file_get_contents($url, 0, $this->headImg);
                File::create($filePath, $contents);
            }
            $html = str_replace($url, $urlPath, $html);
        }
        return $html;
    }

    /**
     * 获取远程文件类型(比如：www.xxxx.com/photo)
     */
    function online_filetype(string $url)
    {
        $url = parse_url($url);
        if ($fp = @fsockopen($url['host'], empty($url['port']) ? 80 : $url['port'], $error)) {
            fputs($fp, "GET " . (empty($url['path']) ? '/' : $url['path']) . " HTTP/1.1\r\n");
            fputs($fp, "Host: {$url['host']}\r\n\r\n");
            while (!feof($fp)) {
                $tmp = fgets($fp);
                if (trim($tmp) == '') {
                    break;
                } else if (preg_match('/Content-Type:(.*)/si', $tmp, $arr)) {
                    return trim((string)$arr[1]);
                }
            }
            return null;
        } else {
            return null;
        }
    }
}