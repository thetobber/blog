<?php
use Application\Security\CsrfValidator;
include VIEW_DIR.'/Shared/Header.php';

$token = CsrfValidator::getToken();
?>

<?php if (isset($model['errors']['database'])): ?>
    <div class="alert alert-danger">
        <?= $model['errors']['database'] ?>
    </div>
<?php endif ?>

<?php if (isset($model['errors']['token'])): ?>
    <div class="alert alert-warning">Invalid token was passed. Please re-open the page.</div>
<?php endif ?>

<?php if (isset($model['password_success'])): ?>
    <div class="alert alert-success">Your password was successfully updated.</div>
<?php endif ?>

<?php if (isset($model['email_success'])): ?>
    <div class="alert alert-success">Your e-mail was successfully updated.</div>
<?php endif ?>

<div class="card mb-5">
    <div class="card-body">
        <form method="post" action="/update-password" autocomplete="off">
            <input name="token" type="hidden" value="<?= $token ?>">

            <div class="form-group">
                <label for="input-1">Old password</label>
                <input id="input-1" class="form-control <?php _err($model, 'old_password') ?>" name="old_password" type="password" placeholder="Password">
                <div class="invalid-feedback">You must provide your current password.</div>
            </div>

            <div class="form-group">
                <label for="input-2">Password</label>
                <input id="input-2" class="form-control <?php _err($model, 'password') ?>" name="password" type="password" placeholder="Password">
                <div class="invalid-feedback">Must be minimum 8 characters long and contain 1 upper and lowercase letter.</div>
            </div>

            <div class="form-group">
                <label for="input-3">Confirm password</label>
                <input id="input-3" class="form-control <?php _err($model, 'confirm') ?>" name="confirm" type="password" placeholder="Confirm password">
                <div class="invalid-feedback">Must be equal to password.</div>
            </div>

            <div class="invalid-feedback <?php _err($model, 'credentials', 'd-block my-3') ?>">Invalid username or password.</div>

            <button type="submit" class="btn btn-primary">Change password</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <p>Current e-mail: <b><?= _e($_SESSION['user']['email']) ?></b></p>
        <form method="post" action="/update-email" autocomplete="off">
            <input name="token" type="hidden" value="<?= $token ?>">

            <div class="form-group">
                <label for="input-4">E-mail</label>
                <input id="input-4" class="form-control <?php _err($model, 'email') ?>" name="email" type="email" placeholder="E-mail">
                <div class="invalid-feedback">Must be a valid e-mail address.</div>
            </div>

            <button type="submit" class="btn btn-primary">Change e-mail</button>
        </form>
    </div>
</div>

<?php include VIEW_DIR.'/Shared/Footer.php' ?>
