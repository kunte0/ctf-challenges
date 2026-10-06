<?php

$error = '';
if(isset($_POST['search'])){
    // $search = getString($_POST, 'search');
    $error = 'Not found!';

}



?>


<div class="row">
    <div class="col-sm-9 mx-auto">
       <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Search</h5>
                <?php if($error){?>
                    <p><?=ms($error)?></p>   

                <?php } else { ?>

                <form class="form-inline my-2 my-lg-0" method="POST" action="?page=search">
                  <input class="form-control mr-sm-2" name="search" type="text" placeholder="Search">
                  <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
                </form>

                <?php } ?>

                <hr>
                <a class="btn btn-primary" href="/" role="button">Back</a>
            </div>
        </div>
    </div>
</div>




