<?php

if(isset($_GET['logout'])){
    session_destroy();
    header('Location: /');
    die('Logout');

}



?>


<div class="row">
    <div class="col-sm-9 mx-auto">
       <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">IDeaShare</h5>
                <div class="mx-auto text-center">
                    <img src="static/sponge.webp" class="img-fluid" alt="Sponge">
                </div>
                <?php if(!loggedIn()){?>

                <div class="alert alert-danger" role="alert">
                        Access Denied. Please login first.
                </div>
                <hr>
                <a class="btn btn-primary" href="?page=login" role="button">Login</a>
                <?php }else{?>

                <div class="alert alert-success" role="alert">
                        Logged in.
                </div>
                <hr>
                <a class="btn btn-primary" href="?logout" role="button">Logout</a>
                <?php }?>
                <a class="btn btn-primary" href="?page=about" role="button">About</a>

            </div>
        </div>
    </div>
</div>




