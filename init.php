<?php

    // error reporting

    ini_set('display_errors','On');
    error_reporting(E_ALL);
    include "admin/connect.php";

    $sessionUser = '';
    if(isset($_SESSION['user'])){
        $sessionUser = $_SESSION['user'];
    }
    //routes
    $tpl  = 'include/templates/'; //template directory
    $lang = 'include/languages/';//languages directory
    $func = 'include/functions/';
    $css  = 'layout/css/';//css directory
    $js   = 'layout/js/';//js directory
    //include important files
    include $func .'function.php';
    include $lang .'english.php';
    include $tpl .'header.php';

