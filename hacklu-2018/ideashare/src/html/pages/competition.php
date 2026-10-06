<?php 


if(!loggedIn()){
    header('Location: /?page=login');
    die('not logged in!');
}

include 'util/Database.php'; // by qll
$USERDB = Database::buildSQLite('../IDeaShareUsers.sqlite3');
$owner = getString($_SESSION, 'id');
$error = '';
$success = '';
$winner =  $USERDB->fetch('SELECT winner from users where id = ?',
        $owner
        );

$winner = $winner->winner;

if($winner === "1"){
    $FLAGDB = Database::buildSQLite('../IDeaShareFlags.sqlite3');
    $flag = $FLAGDB->fetch('SELECT flag from flag');
    $flag = $flag->flag;


    ?>

<div class="row padme">
    <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Congratulations you won!</h5>
                <p><?=ms($flag)?></p>
            </div>
        </div>
    </div>
</div>
<?php
}
else{ ?>
<div class="row padme">
    <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Sorry, you are not a Winner (yet)</h5>
                    <p>Try submitting better IDeas </p>
            </div>
        </div>
    </div>
</div>
<?php
}
?>

