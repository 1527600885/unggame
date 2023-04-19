<?php


namespace app\api\controller\notify;


class Oepay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/1.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);

    }
}