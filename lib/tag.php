<?php
/**
 * Created by PhpStorm.
 * User: Bohdan
 * Date: 05.05.14
 * Time: 18:05
 */

class Tag{

    private static $table = "tags";
    public $id;
    public $name_ua;
    public $name_ru;
    public $name_en;
    public $code;


    public static function getTagList($type){
        global $db;
        $list = array();

        $result = $db->Select(self::$table,"*");
        switch($type){
            case "array":
                foreach($result as $row){
                    $list[$row->id] = $row->name_ru;
                }
                break;
            case "objects":
            default:
                foreach($result as $row){
                    $tag = new Tag();
                    foreach($row as $key=>$value){
                        if(is_numeric($key)) continue;
                        $tag->$key = $value;
                    }
                    $list[] = $tag;
                }
                break;
        }

        return $list;
    }

    public static function getCodesArray(){
        global $db;
        $list = array();
        $result = $db->Select(self::$table,"id,code");
        foreach($result as $row){
            $list[$row->id] = $row->code;
        }
        return $list;
    }

    function getName(){
        $field = "name_".$_COOKIE['lang'];
        return $this->$field;

    }

}