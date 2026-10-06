<?php



if(!loggedIn()){
    header('Location: /?page=login');
    die('not logged in!');
}

// database 
include 'util/Database.php'; // by qll
$IDEADB = Database::buildSQLite('../IDeaShareIDeas.sqlite3');


$error = '';
$success = '';


$pad = getString($_GET, 'pad');
if(!intval($pad) || $pad >= 6){
    $pad = '1';
}

$owner = getString($_GET, 'owner');
$viewer = getString($_SESSION, 'id');

// check access
if(!intval($owner)){
    $error =  'Error: Owner not set correctly!';
}
// admin and owner can access
elseif($owner === $viewer || isAdmin() ){
    // fetch idea
    $currentIdea = $IDEADB->fetch(
        'SELECT id, text,shared FROM ideas WHERE owner=? and pad=? limit 1',
        $owner,
        $pad
        );

    $currenttext = '';
    $currentid = '';


    if($currentIdea){
        $currenttext = $currentIdea->text;
        $currentid = $currentIdea->id;
    }
    if(isset($_GET['raw'])){
        ob_clean();
        die($currenttext);
    
    }

}
else{
    $error =  'Error: You are not allowed to view this!';
}

    


?>
<div class="row">
    <div class="col-sm-9 mx-auto">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">IDeas Viewer</h5>
            </div>
            <div class="card-body">

                    <?php if($error){?>

                    <div class="alert alert-danger" role="alert">
                        <?=$error;?>
                    </div>
                    <?php } else{?>
                        <div class="form-group">
                        <textarea id="textbox" readonly class="col-md-12" name="idea" class="form-control"><?=ms($currenttext)?></textarea>
                    </div>
                    <a class="btn btn-primary" target="_blank" href="?page=view&raw&owner=<?=ms($owner)?>&pad=<?=ms($pad)?>" role="button">Raw</a>
                    <?php } ?>
               

                </form>
            </div>
        </div>
    </div>
</div>