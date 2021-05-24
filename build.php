<?php

require('core.php');

rm('static');

$index = explode("\n", file_get_contents('sitemap.txt'));

foreach ($index as $row) {
    $url = siteurl() . ROOT . $row;
    file_get_contents($url);
    echo '<a href="' . $url . '">' . $url . '</a><br>';
}