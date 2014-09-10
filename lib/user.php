<?php
/**
 * Created by PhpStorm.
 * User: Bohdan
 * Date: 02.05.14
 * Time: 13:28
 */


class User{

    private static $table = "users";
    public $id;
    public $login;
    public $name_ua;
    public $name_ru;
    public $name_en;
    public $photo;
    public $animatePhoto;
    public $in_office;

    function addUser($params){
        global $db;
        if($_FILES['user']['name']['photo']){
            $params['photo'] = date("Ymd").$_FILES['user']['name']['photo'];
            move_uploaded_file($_FILES['user']['tmp_name']['photo'] , $_SERVER['DOCUMENT_ROOT']."/images/users/".$params['photo']);
        }
        if($_FILES['user']['name']['animatePhoto']){
            $params['animatePhoto'] = date("Ymd").$_FILES['user']['name']['animatePhoto'];
            move_uploaded_file($_FILES['user']['tmp_name']['animatePhoto'] , $_SERVER['DOCUMENT_ROOT']."/images/users/".$params['animatePhoto']);
        }
        $params['password'] = md5(($params['password'])?$params['password']:rand(111111111,999999999));
        $db->Insert(self::$table,array_keys($params),array_values($params));

    }

    function updateUser($params){
        global $db;
        $this->id = $params['id'];
        unset($params['id']);
        if($_FILES['user']['name']['photo']){
            $params['photo'] = date("Ymd").$_FILES['user']['name']['photo'];
            move_uploaded_file($_FILES['user']['tmp_name']['photo'] , $_SERVER['DOCUMENT_ROOT']."/images/users/".$params['photo']);
        }
        if($_FILES['user']['name']['animatePhoto']){
            $params['animatePhoto'] = date("Ymd").$_FILES['user']['name']['animatePhoto'];
            move_uploaded_file($_FILES['user']['tmp_name']['animatePhoto'] , $_SERVER['DOCUMENT_ROOT']."/images/users/".$params['animatePhoto']);
        }
        if($params['password']){
            $params['password'] = md5($params['password']);
        }
        else{
            unset($params['password']);
        }
        if(!$params['in_office']) $params['in_office']= 0 ;
        $db->Update(self::$table,array_keys($params),array_values($params),"id=$this->id");

    }

    function getUser($user_id){
        global $db;

        $result = $db->Select(self::$table,"*","id=$user_id");
        if(!$result) return;
        foreach($result[0] as $key=>$value){
            if(is_numeric($key)) continue;
            $this->$key = $value;
        }
        return $this;
    }

    public static function getUsersList($type="objects"){
        global $db;


        $result = $db->Select(self::$table,"*");
        switch($type){
            case "array":
                foreach($result as $row){
                    $list[$row->id] = $row->name_ru;
                }
                break;
            case "objects":
            default:
                foreach($result as $user_fields){
                    $user = new User();
                    foreach($user_fields as $key=>$value){
                        if(is_numeric($key)) continue;
                        $user->$key = $value;
                    }
                    $list[] = $user;
                }
                break;
        }

        return $list;
    }

    function getName(){
        $field = "name_".$_COOKIE['lang'];
        return $this->$field;

    }

}