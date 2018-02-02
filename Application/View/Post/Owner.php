<?php
use Application\Security\Authenticator;
use Application\Security\CsrfValidator;
include VIEW_DIR.'/Shared/Header.php';
?>

<?php if (Authenticator::isAuthenticated()): ?>
    <?php if (isset($model['errors']['database'])): ?>
        <div class="alert alert-danger">
            <?= $model['errors']['database'] ?>
        </div>
    <?php endif ?>
    <?php if (isset($model['errors']['token'])): ?>
        <div class="alert alert-warning">Invalid token was passed. Please re-open the page.</div>
    <?php endif ?>

    <div class="card mb-5">
        <div class="card-body">
            <form method="post" action="/person/<?= urlencode($model['owner']) ?>" autocomplete="off">
                <input name="token" type="hidden" value="<?= CsrfValidator::getToken() ?>">

                <div class="form-group">
                    <label for="input-1">Title</label>
                    <input id="input-1" class="form-control <?php _err($model, 'title') ?>" name="title" type="text" placeholder="Title" <?php _val($model, 'username') ?>>
                    <div class="invalid-feedback">Must be between 1 and 191 characters.</div>
                </div>

                <div class="form-group">
                    <label for="input-2">Content</label>
                    <textarea id="input-2" class="form-control <?php _err($model, 'content') ?>" name="content" rows="3" placeholder="content"></textarea>
                    <div class="invalid-feedback">Must be between 1 and 1000 characters.</div>
                </div>

                <button type="submit" class="btn btn-primary">Send post</button>
            </form>
        </div>
    </div>
<?php endif ?>

<?php if (!empty($model['content'])): ?>
    <?php foreach ($model['content'] as $post): ?>
        <div id="<?= $post['id'] ?>" class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <?= _e($post['title']) ?>
                </h5>
                <p class="card-text">
                    <?= _e($post['content']) ?>
                </p>
                <blockquote class="blockquote mb-0">
                    <footer class="blockquote-footer">
                        <?= _e($post['author']) ?>
                    </footer>
                </blockquote>
            </div>
        </div>
    <?php endforeach ?>
<?php else: ?>
    <div class="d-flex justify-content-center">
        <h5>Be the first to write 1 post!</h5>
    </div>
<?php endif ?>

<?php include VIEW_DIR.'/Shared/Footer.php' ?>
