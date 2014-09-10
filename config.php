<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Булко
 * Date: 14.04.13
 * Time: 19:16
 * To change this template use File | Settings | File Templates.
 */


class Config{
    public $host = "new.matematica.com.ua";
    public $db_host = "localhost";
    public $db_name = "new_matematica";
    public $db_user = "hbo";
    public $db_password = "sxD_1557";
    public static $default_lang = "ru";
}
global $db;
$db = new db();
