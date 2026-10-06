<?php



// database 
include 'util/Database.php'; // by qll
$USERDB = Database::buildSQLite('../IDeaShareUsers.sqlite3');


// login 
$error = '';
$success = '';
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm'])){
    $username = getString($_POST, 'username');
    $password = getString($_POST, 'password');
    $confirm = getString($_POST, 'confirm');


    $testname = $USERDB->fetch(
        'SELECT id FROM users WHERE username=?',
        $username
    );

    if( $confirm  !== $password ){
        $error = 'Password dont match!';
    }
    elseif (strlen($username) > 10) {
        $error = 'Username too long!';
    }
    elseif (strlen($username) < 4) {
        $error = 'Username too short!';
    }
    elseif( preg_match('/\W/', $username)){
        $error = 'Username has evil chars!';
    }
    elseif($testname){
        $error = 'Username already taken!'; 
    }
    else{
        $account = $USERDB->fetch(
            'INSERT into users (username, password) VALUES (?,?)',
            $username,
            hash('sha256', $password)
        );
        $success = 'Success! You can log in now.';
    }
    
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
                <h5 class="card-title">Register</h5>
                <form method="POST" action="?page=register">
                    <div class="form-group">
                        <label for="name">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="password">
                    </div>
                    <div class="form-group">
                        <label for="confirm">Password</label>
                        <input type="password" name="confirm" class="form-control" placeholder="password">
                    </div>
                    <?php if($error){?>
                    <div class="alert alert-danger" role="alert">
                        <?=ms($error);?>
                    </div>
                    <?php } if($success) {?>
                    <div class="alert alert-success" role="alert">
                        <?=ms($success);?>
                    </div>
                <?php  }?><button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
