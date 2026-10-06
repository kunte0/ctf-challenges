<?php

if (!($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1' || $_SERVER['REMOTE_ADDR'] === '10.0.2.2')) {
    header('HTTP/1.1 403 Forbidden');
    ?>
    <div class="row">
    <div class="col-sm-9 col-md-7 col-lg-5 mx-auto text-center">
        <h1>Admin Only.</h1>
        <a class="btn btn-primary" href="/" role="button">Back to Safety</a>
    </div>
    </div>
    <?php 
    die();
}


include 'util/Database.php'; // by qll
$USERDB = Database::buildSQLite('../IDeaShareUsers.sqlite3');

$error = '';
$success = '';
if(isset($_POST['userid'])){
    $userid = intval($_POST['userid']);
    if($userid !== 0){
        $USERDB->put(
            'UPDATE users SET winner = 1 WHERE id = ?',
            $userid
        );
        $success = 'A winner was chosen.';
    }
    else{
        $error = 'The admin can not win ;)' ;
    }

}


?>

<div class="row padme">
    <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Select a Winner</h5>
                <form method="POST" action="?page=admin">
                    <div class="form-group">
                        <label for="password">Winner ID</label>
                        <input type="text" name="userid" class="form-control">
                    </div>
                    <?php if($error){?>
                    <div class="alert alert-danger" role="alert">
                        <?=ms($error);?>
                    </div>
                    <?php } if($success) {?>
                    <div class="alert alert-success" role="alert">
                        <?=ms($success);?>
                    </div>
                <?php }?><button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>


