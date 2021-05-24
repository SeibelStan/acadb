<?php

require('core.php');

rm('static');

$index = explode("\n", file_get_contents('build-index.txt'));

foreach ($index as $row) {
    $url = siteurl() . ROOT . $row;
    file_get_contents($url);
}

$map = '<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

foreach ($index as $row) {
    $url = siteurl() . ROOT . $row;
    $map .= '
    <url>
        <loc>' . trim($url) . '</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>daily</changefreq>
    </url>';
}

$map .= '
</urlset>';

$f = fopen('sitemap.xml', 'w+');
fwrite($f, $map);
fclose($f);

echo $map;