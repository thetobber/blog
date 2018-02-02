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
        <form method="post" action="/signin" autocomplete="off">
            <input name="token" type="hidden" value="<?= CsrfValidator::getToken() ?>">

            <div class="form-group">
                <label for="input-1">Username</label>
                <input id="input-1" class="form-control <?php _err($model, 'credentials') ?>" name="username" type="text" placeholder="Username" <?php _val($model, 'username') ?>>
            </div>

            <div class="form-group">
                <label for="input-2">Password</label>
                <input id="input-2" class="form-control <?php _err($model, 'credentials') ?>" name="password" type="password" placeholder="Password">
            </div>

            <div class="invalid-feedback <?php _err($model, 'credentials', 'd-block my-3') ?>">Invalid username or password.</div>

            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>
    </div>
</div>

<?php include VIEW_DIR.'/Shared/Footer.php' ?>
