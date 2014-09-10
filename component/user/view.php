<?php
/**
 * Created by PhpStorm.
 * User: Bohdan
 * Date: 02.05.14
 * Time: 13:14
 */

class userView{
    public $tpl;

    function __construct(){
        require_once('./lib/user.php');
    }

    function getDisplay(){
        $task = $_REQUEST['task'];

        switch($task){
            case "new":
            case "edit":

                $this->tpl = "./component/user/tmpl/edit.phtml";
                break;
            case "save":

                $user = new User();
                if($_REQUEST['user']['id']){
                    $user->updateUser($_REQUEST['user']);
                }
                else{
                    $user->addUser($_REQUEST['user']);
                }
                $this->tpl = "./component/user/tmpl/edit.phtml";
                header ("Location: /index2.php?type=user&task=list");
                break;
            case "list";
                $this->tpl = "./component/user/tmpl/list.phtml";
                break;
            default:
                break;
        }
    }
}