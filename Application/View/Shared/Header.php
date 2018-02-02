<?php
use Application\Security\Authenticator;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/Static/bootstrap.min.css">
    <title><?= $model['title'] ?? 'Spot' ?></title>
</head>
<body>

<nav class="navbar navbar-expand-sm navbar-dark bg-dark mb-5">
    <div class="container">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav w-100">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <?php if (Authenticator::isAuthenticated()): ?>
                    <li class="nav-item ml-sm-auto">
                        <a class="nav-link" href="/profile">Profile</a>
                    </li>
                    <li class="nav-item">
                        <form class="form-inline m-0" method="post" action="/signout">
                            <button class="btn btn-link nav-link border-0" type="submit">Sign out</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item ml-sm-auto">
                        <a class="nav-link" href="/signin">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/register">Register</a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container">
