<?php

function render($rp) {
    if (preg_match('/^static/', $rp)) {
        require($rp);
        return true;
    }

    $frp = preg_replace('/^(static|source)\//', '', $rp);

    if (preg_match('/\.md$/', $frp)) {
        $c = markdown($rp);
    }
    else {
        $c = preproc($rp);
    }

    $trp = preg_replace('/\//', '.', $frp);
    $f = fopen("temp/$trp", 'w+');
    fwrite($f, $c);
    
    $statgen = !$_GET && !$_POST;
    
    if ($statgen) {
        ob_start();
    }

    require("temp/$trp");

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
    $c = preg_replace('/\{\{(.+?\.md)\}\}/', "<?php render('source/markdown/$1') ?>", $c);
    $c = preg_replace('/\{\{(.+?)\}\}/', "<?php render('source/modules/$1.php') ?>", $c);
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
    
    $c = preg_replace("/^ +(\S.+?)(\s*)$/m", "<p>$1</p>$2", $c);
    return $c;
}