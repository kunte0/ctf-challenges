<?php



// database for later
include 'util/Database.php'; // by qll
$USERDB = Database::buildSQLite('../IDeaShareUsers.sqlite3');



// reset
$error = '';
if (isset($_POST['username'])) {
    $username = getString($_POST, 'username');

    if($username === 'admin'){
        $error = "Error: Username <b>admin</b> can not be reset.";
    }
    elseif($username === ''){
        $error = 'Error: Username is empty!';
    }
    else{
        $error = 'Error: Username <b>'. ms($username) . '</b> can not be reset.';
    }
}





?>

<div class="row padme">
    <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Reset Password</h5>
                <form method="POST" action="?page=reset">
                    <div class="form-group">
                        <label for="name">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="username">
                    </div>
                    <?php if($error){?>
                        <div class="alert alert-danger" role="alert">
                            <?=$error;?>
                        </div>
                <?php }?><button type="submit" class="btn btn-primary">Submit</button>
                    <a class="btn btn-primary" href="?page=login" role="button">Login</a>
                </form>
            </div>
        </div>
    </div>
</div>
