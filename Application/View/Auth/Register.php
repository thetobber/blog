<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>

<body>

<form method="post" action="/register" autocomplete="off">
    <div>
        <label for="input-1">Username</label>
        <input id="input-1" name="username" type="text">
    </div>

    <div>
        <label for="input-2">E-mail</label>
        <input id="input-2" name="email" type="email">
    </div>

    <div>
        <label for="input-4">Password</label>
        <input id="input-4" name="password" type="password">
    </div>

    <div>
        <label for="input-5">Confirm password</label>
        <input id="input-5" name="confirm" type="password">
    </div>

    <button type="submit">Submit</button>
</form>

<?php
var_dump($model)
?>

</body>

</html>
