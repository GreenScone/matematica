<?php
/**
 * Created by PhpStorm.
 * User: Bohdan
 * Date: 28.05.14
 * Time: 13:40
 */

class Lang{

    public static function text($str){
        if($_COOKIE['lang'] == Config::$default_lang){
            return $str;
        }
        else{
            $lang = $_COOKIE['lang'];
            global $words;
            if(isset($words[$str])){
                return $words[$str]->$lang;
            }
            else{
                return $str;
            }
        }
    }

    public static function getWords(){
        global $db;
        $list = array();
        $key = Config::$default_lang;
        $result = $db->Select('languages');
        foreach($result as $row){
            $list[$row->$key] = $row;
        }
        return $list;
    }

}

global $words;
$words = Lang::getWords();