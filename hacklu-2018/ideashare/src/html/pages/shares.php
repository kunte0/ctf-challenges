<?php

if(!loggedIn()){
    header('Location: /?page=login');
    die('not logged in!');
}
if(isAdmin()){
    die('nono dont go here');
}

// database 
include 'util/Database.php'; // by qll
$IDEADB = Database::buildSQLite('../IDeaShareIDeas.sqlite3');

$owner = getString($_SESSION, 'id');



if(isset($_POST['submit']) && isset($_POST['pad'])){
    $pad = getString($_POST, 'pad');
    if(!intval($pad) || $pad >= 6){
        $pad = '1';
    }
    
    $IDEADB->put(
            'UPDATE ideas SET shared = 0 WHERE pad = ? AND owner = ?',
            $pad,
            $owner
    );
}
// fetch ideas
$ideas = $IDEADB->fetchAll(
    'SELECT id, pad, shared FROM ideas WHERE owner=? and shared != 0',
        $owner
    );

?>





<div class="row">
    <div class="col-sm-9 mx-auto">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Shares</h5>
                <h6 class="card-subtitle text-muted">Your shared IDeas</h6>
            </div>
            <div class="card-body">
                <?php if($ideas) { ?>
                    <ul class="list-group">
                    <?php foreach($ideas as $idea){ 
                        $pad = $idea->pad;
                        $shared = $idea->shared;
                        if($shared){?>
                            <li class="list-group-item d-flex flex-row">
                                <div class="mr-auto">
                                    <a href=?page=view&owner=<?=ms($owner)?>&pad=<?=ms($pad)?>>IDea Number <?=ms($pad) ?></a>
                                </div>
                                <div>
                                    <?php if($shared == 1){ ?>
                                        <button disabled class="btn-primary btn-sm">Not checked yet</button>
                                    <?php } ?>
                                    <?php if($shared == 2){ ?>
                                        <button disabled class="btn-danger btn-sm">Bad IDea</button>
                                    <?php } ?>
                                    <?php if($shared == 3){ ?>
                                        <button disabled class="btn-success btn-sm">Good IDea</button>
                                    <?php } ?>
                                </div>
                                <div>
                                    <form action="?page=shares" method="post">
                                        <input type="hidden" class="btn-secondary btn-sm" name="pad" value="<?=ms($pad)?>"/>
                                        <input type="submit" class="btn-secondary btn-sm" name="submit" value="Unshare"/>
                                    </form>
                                </div>
                                
                            </li>
                        <?php } 
                    } ?>
                    </ul>
                <?php }else { ?>
                    <p> non yet </p>
                <?php } ?>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted">Shares for <?=ms(getString($_SESSION, 'username'))?></h6>
            </div>
            <div class="card-body">
                <p> non yet </p>
                <hr>
                <a class="btn btn-primary" href="?page=idea" role="button">My IDeas</a>
            </div>
        </div>
    </div>
</div>