<?php

if(!loggedIn()){
    header('Location: /?page=login');
    die('not logged in!');
}
?>





<div class="row">
    <div class="col-sm-9 mx-auto">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Profile</h5>
                <h6 class="card-subtitle text-muted"><?=ms(getString($_SESSION, 'username'))?></h6>
            </div>
            <div class="card-body">
                <div class=row>
                    <div class="col-sm-3">
                        <img style="width:60%" src="/static/profile.png" alt="profile image">
                    </div>
                    <div class="col-sm-6 mx-auto">
                        <p class="card-text">
                            Hello <?=ms(getString($_SESSION, 'username'))?>.
                        </p>
                        <p class="card-text">
                            This is your Profile, now create some IDeas!</p>
                        <a data-toggle="collapse" class="btn btn-primary" href="#userdata" role="button">Toggle Userdata</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="userdata" class="table table-hover collapse">
                    <tbody>
                        <tr class="table-active">
                            <th scope="row">User ID</th>
                            <td><?=ms(getString($_SESSION, 'id'))?></td>
                        </tr>
                        <tr class="table-active">
                            <th scope="row">User Agent</th>
                            <td><?=ms(getString($_SESSION, 'agent'))?></td>
                        </tr>
                        <tr class="table-active">
                            <th scope="row">User IP</th>
                            <td><?=ms(getString($_SESSION, 'ip'))?></td>
                        </tr>
                    </tbody>
                </table>
                <a class="btn btn-primary" href="?page=idea" role="button">My IDeas</a>
                <a class="btn btn-primary" href="?page=shares" role="button">My Shares</a>
            </div>
            <div class="card-footer text-muted">
                created today
            </div>
        </div>
    </div>
</div>
