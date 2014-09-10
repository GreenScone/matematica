<?php
/**
 * Created by PhpStorm.
 * User: Bohdan
 * Date: 02.05.14
 * Time: 13:17
 */


require_once('lib/controller.php');

$controller = new Controller();
global $db;

$component_name = $_REQUEST['type'];

$component = $controller->getComponent($component_name);
if($component){
    require_once($component);
}
