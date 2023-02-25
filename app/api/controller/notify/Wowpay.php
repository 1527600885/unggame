<?php


namespace app\api\controller\notify;


class Wowpay
{
    public function callBack()
    {
        $data = input("param.");
        $file = fopen(__DIR__."/1.txt","wr");
        fwrite($file,json_encode($data));
        $postData = input("param.");
        $file2 =  fopen(__DIR__."/2.txt","wr");
        fwrite($file2,json_encode($postData));
    }
}