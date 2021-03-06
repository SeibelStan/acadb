<?php

require('core.php');

rm('static');

$index = explode("\n", file_get_contents('build-index.txt'));

foreach ($index as $row) {
    if (preg_match('/\*$/', $row)) {
        $dir = preg_replace('/\*$/', '', $row);
        $scan = scandir('source/' . $dir);
        foreach ($scan as $file) {
            if (!in_array($file, ['.', '..'])) {
                $file = preg_replace('/\.\w+$/', '', $file);
                $url = siteurl() . ROOT . $dir . $file;
                file_get_contents($url);
            }
        }
        continue;
    }

    $url = siteurl() . ROOT . $row;
    file_get_contents($url);
}

$map = '<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

function smnode($url) {
    return '
    <url>
        <loc>' . trim($url) . '</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>daily</changefreq>
    </url>';
}

foreach ($index as $row) {
    if (preg_match('/\*$/', $row)) {
        $dir = preg_replace('/\*$/', '', $row);
        $scan = scandir('source/' . $dir);

        foreach ($scan as $file) {
            if (!in_array($file, ['.', '..'])) {
                $file = preg_replace('/\.\w+$/', '', $file);
                $map .= smnode(siteurl() . ROOT . $dir . $file);
            }
        }
        continue;
    }

    $map .= smnode(siteurl() . ROOT . $row);
}

$map .= '
</urlset>';

$f = fopen('sitemap.xml', 'w+');
fwrite($f, $map);
fclose($f);

echo $map;