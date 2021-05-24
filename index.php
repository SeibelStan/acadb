<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT', 'acadb/');
define('DEV', 1);

require('core.php');

$path = preg_replace('/^\//', '', $_SERVER['REQUEST_URI']);
$path = explode('?', preg_replace('#' . ROOT . '#', '', $path))[0] ?: 'index';

$sp = "static/$path.html";
$dp = "source/$path.php";

$rp = 'source/default.php';
if (!DEV && file_exists($sp) && file_exists($dp)) {
    $rp = filemtime($sp) >= filemtime($dp) ? $sp : $dp;
}
elseif (!DEV && file_exists($sp)) {
    $rp = $sp;
}
elseif (file_exists($dp)) {
    $rp = $dp;
}

render($rp);