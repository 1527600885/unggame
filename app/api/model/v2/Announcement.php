<?php


namespace app\api\model\v2;


use think\Model;

class Announcement extends Model
{
    public function getTimeAttr($value,$data)
    {
        return date("H:i",$data['create_time']);
    }
    public function getIconAttr($value,$data)
    {
        switch ($data['type']){
            case 1:
                $value = "/static/images/tips/notes.png";
                break;
            case 2:
                $value = "/static/images/tips/notesq.png";
                break;
        }
        return $value;
    }
    public function getTitleAttr($value,$data)
    {
        switch ($data['type']){
            case 1:
                $value = "System notification";
                break;
            case 2:
                $value = "Game notification";
                break;
        }
        return $value;
    }
}