<?php
/**
 * Created by PhpStorm.
 * User: Bohdan
 * Date: 05.05.14
 * Time: 13:14
 */

class projectView{
    public $tpl;

    function __construct(){
        require_once('./lib/project.php');
        require_once('./lib/user.php');
        require_once('./lib/tag.php');
    }

    function getDisplay(){
        $task = $_REQUEST['task'];

        switch($task){
            case "new":
            case "edit":
                $this->tpl = "./component/project/tmpl/edit.phtml";
                break;
            case "save":

                $project = new Project();
                if($_REQUEST['project']['id']){
                    $project->updateProject($_REQUEST['project']);
                }
                else{
                    $project->addProject($_REQUEST['project']);
                }
                $this->tpl = "./component/project/tmpl/edit.phtml";
                header ("Location: /index2.php?type=project&task=list");
                break;
            case "list";
                $this->tpl = "./component/project/tmpl/list.phtml";
                break;
            case "loadlist";
                $this->tpl = "./component/project/tmpl/loadlist.phtml";
                break;
            case "view":
                $this->tpl = "./component/project/tmpl/view.phtml";
            default:
                break;
        }
    }
}