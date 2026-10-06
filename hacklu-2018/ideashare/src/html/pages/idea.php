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


$pad = getString($_GET, 'pad');
if(!intval($pad) || $pad >= 6){
    $pad = '1';
}

$owner = getString($_SESSION, 'id');

$error = '';
$success = '';

// fetch idea
$currentIdea = $IDEADB->fetch(
    'SELECT id, text, shared FROM ideas WHERE owner=? and pad=? limit 1',
    $owner,
    $pad
    );

$currenttext = '';
$currentid = '';
$currentshare = '';

if($currentIdea){
    $currenttext = $currentIdea->text;
    $currentid = $currentIdea->id;
    $currentshare = $currentIdea->shared;
}


// save idea
if(isset($_POST['idea'])){
    $idea = getString($_POST, 'idea');
    if(strlen($idea) > 3000){
        $error = 'Error: Idea too long.';
    }
    else{
        if($currentid){
            $IDEADB->put(
                'UPDATE ideas SET text = ?, shared = 0 WHERE id = ?',
                $idea,
                $currentid
            );
        }
        else{
            $IDEADB->put(
                'INSERT into ideas (owner, text, pad, shared) values (?,?,?,?)',
                $owner,
                $idea,
                $pad,
                0
            );
        }
        $currenttext = $idea;
        $currentshare = 0;
    }
}

// from https://github.com/osirislab/CSAW-CTF-2018-Quals/blob/master/web/nvs/src/nvs/register.php

function chrome($url, $timeout=15) {
    $cmd = array(
        "/usr/bin/timeout",
        escapeshellarg(strval($timeout)),
        "/usr/bin/google-chrome-stable",
        "--disable-gpu",
        "--headless",
	    "--dump-dom",
        "--",
        escapeshellarg($url),
    );
    exec(implode(' ', $cmd));
}


if(isset($_POST['share']) && isset($_POST["g-recaptcha-response"]) && $currentid){
    $response = getString($_POST, "g-recaptcha-response");
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
    	'secret' => '<redacted: reCAPTCHA secret key>',
    	'response' => $response,
    );
    $options = array(
    	'http' => array (
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded",
    		'content' => http_build_query($data),
    	)
    );
    $context = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success = json_decode($verify);

    if ($captcha_success->success === false) {
        $error = "Bad bot.";
    }
    else{
        $IDEADB->put(
                'UPDATE ideas SET shared = 1 WHERE id = ?',
                $currentid
        );
        // call bot with $currentid
        chrome("http://127.0.0.1/?page=review&id=$currentid");
        $success = "Your Idea was successfully shared!";
        $currentshare = 1;
    }
}

?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
   function SubmitIt(token) {
     	document.getElementById("share").submit();
   }
</script>
<div class="row">
    <div class="col-sm-9 mx-auto">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">IDeas</h5>
            </div>
            <div class="card-body mx-auto">
                    <ul class="pagination">
                        <li class="page-item <?=$pad==1 ? 'active' : '' ?>">
                            <a class="page-link" href="?page=idea&pad=1">1</a>
                        </li>
                        <li class="page-item <?=$pad==2 ? 'active' : '' ?>">
                            <a class="page-link" href="?page=idea&pad=2">2</a>
                        </li>
                        <li class="page-item <?=$pad==3 ? 'active' : '' ?>">
                            <a class="page-link" href="?page=idea&pad=3">3</a>
                        </li>
                        <li class="page-item <?=$pad==4 ? 'active' : '' ?>">
                            <a class="page-link" href="?page=idea&pad=4">4</a>
                        </li>
                        <li class="page-item <?=$pad==5 ? 'active' : '' ?>">
                            <a class="page-link" href="?page=idea&pad=5">5</a>
                        </li>
                    </ul>
            </div>
            <div class="card-body">
                <form method="POST" action="?page=idea&pad=<?=$pad?>">
                    <div class="form-group">
                        <textarea id="textbox" class="col-md-12" name="idea" class="form-control"><?=ms($currenttext)?></textarea>
                    </div>
                    <?php if($error){?>

                    <div class="alert alert-danger" role="alert">
                        <?=$error;?>
                    </div>
                    <?php }?>

                    <?php if($success){?>
                    <div class="alert alert-success" role="alert">
                        <?=$success;?>
                    </div>
                    <?php }?>

                    <button type="submit" class="btn btn-primary">Save</button>
                    <a class="btn btn-primary" href="?page=view&owner=<?=ms($owner)?>&pad=<?=ms($pad)?>" role="button">Show in Viewer</a>
                </form>
                <hr>
                <?php if($currentshare == 0){ ?>

                    <form id="share" method="POST" action="?page=idea&pad=<?=$pad?>">
                    <input type="hidden" name="share">
                    <button
                        class="g-recaptcha btn btn-primary"
                        data-sitekey="<redacted: reCAPTCHA site key>"
                        data-callback="SubmitIt">
                        Share
                    </button>
                </form>

                <?php }
                else{ ?>

                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="You already shared this, try changing it.">
                        <button class="btn btn-primary" style="pointer-events: none;" type="button" disabled>Share</button>
                    </span>
                

                <?php }?>

                              
            </div>
        </div>
    </div>
</div>
