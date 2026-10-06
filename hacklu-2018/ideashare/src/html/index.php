<?php

// util
include 'util/util.php';

header('X-XSS-Protection: 0');
// session
ini_set('session.cookie_httponly', 1);
session_start();



// parse page
$path = 'pages/start.php';
if (isset($_GET['page'])) {
    $page = getString($_GET, 'page');
    $path = 'pages/' . $page . '.php';
    if (!preg_match('/^[\w\-]+$/', $page) || !file_exists($path)) {
        $path = 'pages/404.php';
    }
}


// ids
include 'util/ids.php';
$GLOBALS['tryharder'] = '';
$ids = getPhpIDSImpact();
if ($ids > 5){
    header('Location: /?page=weewoo');
    if($GLOBALS['tryharder'] !== ''){
        echo "<a href='/static/patrick.patch'>Patrick</a>\n";
    }
    die("impact = $ids" . $GLOBALS['tryharder'] );
}




// include it
include 'layout/top.php';
include $path;
include 'layout/bottom.php';






