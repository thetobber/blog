<?php
use Application\Security\CsrfValidator;
include VIEW_DIR.'/Shared/Header.php'
?>

<?php if (isset($model['errors']['database'])): ?>
    <div class="alert alert-danger">
        <?= $model['errors']['database'] ?>
    </div>
<?php endif ?>
<?php if (isset($model['errors']['token'])): ?>
    <div class="alert alert-warning">Invalid token was passed. Please re-open the page.</div>
<?php endif ?>

<div class="card">
    <div class="card-body">
        <form method="post" action="/register" autocomplete="off">
            <input name="token" type="hidden" value="<?= CsrfValidator::getToken() ?>">

            <div class="form-group">
                <label for="input-1">Username</label>
                <input id="input-1" class="form-control <?php _err($model, 'username') ?>" name="username" type="text" placeholder="Username" <?php _val($model, 'username') ?>>
                <div class="invalid-feedback">Must be between 1 and 191 characters.</div>
            </div>

            <div class="form-group">
                <label for="input-2">E-mail</label>
                <input id="input-2" class="form-control <?php _err($model, 'email') ?>" name="email" type="email" placeholder="E-mail" <?php _val($model, 'email') ?>>
                <div class="invalid-feedback">Must be a valid e-mail address.</div>
            </div>

            <div class="form-group">
                <label for="input-4">Password</label>
                <input id="input-4" class="form-control <?php _err($model, 'password') ?>" name="password" type="password" placeholder="Password" <?php _val($model, 'password') ?>>
                <div class="invalid-feedback">Must be minimum 8 characters long and contain 1 upper and lowercase letter.</div>
            </div>

            <div class="form-group">
                <label for="input-5">Confirm password</label>
                <input id="input-5" class="form-control <?php _err($model, 'confirm') ?>" name="confirm" type="password" placeholder="Confirm password">
                <div class="invalid-feedback">Must be equal to password.</div>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</div>

<?php include VIEW_DIR.'/Shared/Footer.php' ?>
