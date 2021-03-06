<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!file_exists('source')) { mkdir('source'); }
if (!file_exists('static')) { mkdir('static'); }

require('core.php');

$URI = $_SERVER['REQUEST_URI'];
$URI = preg_replace('/_$/', '', $URI);

$path = preg_replace('/^\//', '', $URI);
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