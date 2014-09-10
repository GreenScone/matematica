<?php
/**
 * Created by PhpStorm.
 * User: Bohdan
 * Date: 02.05.14
 * Time: 13:16
 */


class Controller{

    function __construct(){
        require_once('connect.php');
        require_once('./config.php');
        require_once('./lib/lang.php');

    }

    function getComponent($component){
        require_once("./component/$component/view.php");

        $component_view_class = $component."View";

        $component_view = new $component_view_class();

        $component_view->getDisplay();

        if($component_view->tpl){
            return $component_view->tpl;
        }
        return false;

    }
}