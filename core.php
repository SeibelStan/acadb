<?php

define('ROOT', 'acadb/');
define('DEV', 1);

function render($rp) {
    if (preg_match('/^static/', $rp)) {
        require($rp);
        return true;
    }

    $frp = preg_replace('/^(static|source)\//', '', $rp);

    if (preg_match('/\.md$/', $frp)) {
        $c = markdown($rp);
    }
    elseif (preg_match('/\.txt$/', $frp)) {
        $c = text($rp);
    }
    else {
        $c = preproc($rp);
    }

    if (!file_exists('temp')) { mkdir('temp'); }

    $trp = preg_replace('/\//', '.', $frp);
    $f = fopen("temp/$trp", 'w+');
    fwrite($f, $c);
    
    $statgen = !$_GET && !$_POST;
    
    if ($statgen) {
        ob_start();
    }

    require("temp/$trp");

    rm('temp');

    if ($statgen) {
        $brp = basename($frp);
        $rpd = preg_replace("/$brp/", '', $frp);
        if (!file_exists("static/$rpd")) {
            mkdir("static/$rpd", 0777, true);
        }

        $c = ob_get_clean();
        $hrp = preg_replace('/\.(\w+)$/', '.html', $frp);
        $f = fopen("static/$hrp", 'w+');
        fwrite($f, $c);
        echo $c;
    }
}

function preproc($rp) {
    $c = file_get_contents($rp);

    $c = preg_replace('/\{\{(.+?\.php)\}\}/', "<?php require('source/php/$1') ?>", $c);
    $c = preg_replace('/\{\{(.+?\.md)\}\}/', "<?php render('source/md/$1') ?>", $c);
    $c = preg_replace('/\{\{(.+?\.txt)\}\}/', "<?php render('source/txt/$1') ?>", $c);
    $c = preg_replace('/\{\{(.+?)\}\}/', "<?php render('source/php/$1.php') ?>", $c);
    $c = preg_replace('/@else/', "<?php else : ?>", $c);
    $c = preg_replace('/@e(\w+)(.+)/', "<?php end$1; ?>", $c);
    $c = preg_replace('/@(\w+)(.+)/', "<?php $1 ($2) : ?>", $c);
    $c = preg_replace('/\{(.+?)=(.+?)\}/', "<?php \$$1 = $2; ?>", $c);
    $c = preg_replace('/\{(.+?)\}/', "<?= \$$1 ?>", $c);
    return $c;
}

function markdown($rp) {
    $c = file_get_contents($rp);

    $c = preg_replace('/^### (.+?)(\s*)$/m', "<h3>$1</h3>$2", $c);
    $c = preg_replace('/^## (.+?)(\s*)$/m', "<h2>$1</h2>$2", $c);
    $c = preg_replace('/^# (.+?)(\s*)$/m', "<h1>$1</h1>$2", $c);
    
    $c = preg_replace("/^>+ (.+?)(\s*)$/m", "<blockquote>$1</blockquote>$2", $c);

    $c = preg_replace('/!\[(.+?):(\d+)\]\((.+?)\)/', '<img alt="$1" width="$2" src="$3">', $c);
    $c = preg_replace('/!\[(.+?)\]\((.+?)\)/', '<img alt="$1" src="$2">', $c);
    $c = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $c);

    $c = preg_replace("/^[*+-] (.+?)(\s*)$/m", '<dli>$1</dli>$2', $c);
    $c = preg_replace("/(\s{2,})(<dli>[\s\S]+<\/dli>)(\s{2,})/m", "$1<ul>\n$2\n</ul>$3", $c);
    $c = preg_replace("/(dli>)/", "li>", $c);

    $c = preg_replace("/^\d+\. (.+?)(\s*)$/m", '<dli>$1</dli>$2', $c);
    $c = preg_replace("/(\s{2,})(<dli>[\s\S]+<\/dli>)(\s{2,})/m", "$1<ol>\n$2\n</ol>$3", $c);
    $c = preg_replace("/(dli>)/", "li>", $c);
    
    $c = preg_replace('/\*\*(\S.*?\S*)\*\*/', "<strong>$1</strong>", $c);
    $c = preg_replace('/\*(\S.*?\S*)\*/', "<em>$1</em>", $c);

    $t = preg_match_all("/```([\s\S]+?)```/m", $c, $matches);
    if ($matches) {
        foreach ($matches[1] as $m) {
            $m = trim($m);
            $m = preg_replace('/ /', '&nbsp;', $m);
            $m = preg_replace('/\n/', '<br>', $m);
            $c = preg_replace("/```([\s\S]+?)```/m", "```$m```", $c, 1);
        }
    }

    $c = preg_replace("/```([\s\S]+?)```/m", "<code>$1</code>$2", $c);
    $c = preg_replace("/`(.+?)`/", "<code>$1</code>$2", $c);
    
    $c = preg_replace("/^(<img |<a |<span|<strong|<em)*([^<\s].*?)(\s*)$/m", "<p>$1$2</p>$3", $c);
    return $c;
}

function text($rp) {
    $c = file_get_contents($rp);
    $c = nl2br($c);
    return $c;
}

function rm($dir) {
    if (!file_exists($dir)) {
        return false;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $path) {
        if ($path->isDir()) {
            @rmdir($path);
        }
        else {
            unlink($path);
        }
    }
    rmdir($dir);
}

function siteurl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'] . '/';
    return $protocol . $domainName;
}