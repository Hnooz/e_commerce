<?php
include "connect.php";
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

//
if(!isset($noNavbar)){include $tpl .'navbar.php';}
