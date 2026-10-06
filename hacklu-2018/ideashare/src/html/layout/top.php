<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/static/style.css">
    <link rel="stylesheet" type="text/css" href="/static/bootstrap.min.css">
    <title>IDeaShare</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-9 mx-auto">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
              <a class="navbar-brand" href="/">IDeaShare</a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor03" aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarColor03">
                <ul class="navbar-nav mr-auto">
              <?php if (loggedIn()){ ?>

                  <li class="nav-item active">
                    <a class="nav-link" href="?page=profile">Profile</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="?page=idea">IDeas</a>
                  </li>
                  <li class="nav-item">
                  <a class="nav-link" href="?page=shares">Shares</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="?logout">Logout</a>
                  </li>

              <?php } else{ ?>

                <li class="nav-item">
                    <a class="nav-link" href="?page=login">Login</a>
                </li>

             <?php  } ?>
                <li class="nav-item">
                    <a class="nav-link" href="?page=about">About</a>
                  </li>
                </ul>

                <form class="form-inline my-2 my-lg-0" method="POST" action="?page=search">
                  <input class="form-control mr-sm-2" name="search" type="text" placeholder="Search">
                  <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
                </form>
              </div>
            </nav>
        </div>
    </div>