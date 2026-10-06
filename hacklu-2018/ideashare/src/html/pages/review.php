<?php

if (!($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1')) {
    header('HTTP/1.1 403 Forbidden');
    ?>
    <div class="row">
    <div class="col-sm-9 col-md-7 col-lg-5 mx-auto text-center">
        <h1>Admin Only.</h1>
        <a class="btn btn-primary" href="/" role="button">Back to Safety</a>
    </div>
    </div>
    <?php die();
}


$_SESSION['username'] = 'admin';
$_SESSION['id'] = '1';
$_SESSION['agent'] = getString($_SERVER, 'HTTP_USER_AGENT');
$_SESSION['ip'] = getIp();


if (!isset($_GET['id'])) {
	die("No id passed");
}

$id = getString($_GET, 'id');

// database 
include 'util/Database.php'; // by qll
$IDEADB = Database::buildSQLite('../IDeaShareIDeas.sqlite3');


// fetch idea
$idea = $IDEADB->fetch('SELECT pad, owner FROM ideas WHERE id == ?',
    $id
);

if($idea){
    $pad = $idea->pad;
    $owner = $idea->owner;
    if(rand(0, 100)%3 !== 0){
        $IDEADB->put(
            'UPDATE ideas SET shared = 2 WHERE id = ?',
            $id
        );
    }
    else{
        $IDEADB->put(
            'UPDATE ideas SET shared = 3 WHERE id = ?',
            $id
        );
    }

    ?>
    <a id="click" rel="noreferrer" href=?page=view&owner=<?=ms($owner)?>&pad=<?=ms($pad)?>>IDea Number <?=ms($pad) ?></a>
    <script>
        var redirect = document.getElementById("click");
        redirect.click();
    </script>
<?php
}
else{
    echo 'non Idea';
}

?>




