<?php


// database 
include 'util/Database.php'; // by qll
$USERDB = Database::buildSQLite('../IDeaShareUsers.sqlite3');

// login 
$error = '';
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = getString($_POST, 'username');
    $password = getString($_POST, 'password');
    $account = $USERDB->fetch(
        'SELECT id FROM users WHERE username=? AND password=?',
        $username,
        hash('sha256', $password)
    );
    if ($account) {
        $_SESSION['username'] = $username;
        $_SESSION['id'] = $account->id;
        $_SESSION['agent'] = getString($_SERVER, 'HTTP_USER_AGENT');
        $_SESSION['ip'] = getIp();
    }
    $error = 'User not found or password wrong!';
}

if (loggedIn()) {
    header('Location: /');
    die();
}

?>

<div class="row padme">
    <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Login</h5>
                <form method="POST" action="?page=login">
                    <div class="form-group">
                        <label for="name">Username</label>
                        <input type="text" class="form-control" name="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <?php if($error){?>
                        <div class="alert alert-danger" role="alert">
                            <?=$error;?>
                        </div>
                <?php }?><button type="submit" class="btn btn-primary">Submit</button>
                    <a class="btn btn-primary" href="?page=reset" role="button">Reset Password</a>
                    <a class="btn btn-primary" href="?page=register" role="button">Register</a>
                </form>
            </div>
        </div>
    </div>
</div>
