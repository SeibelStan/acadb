<?php

require('core.php');

rm('static');

$source = scandir('source');

foreach ($source as $unit) {
    if ($unit == '.' || $unit == '..' || is_dir("source/$unit")) {
        continue;
    }
    $url = siteurl() . ROOT . preg_replace('/\.\w+$/', '', $unit);
    file_get_contents($url);
}