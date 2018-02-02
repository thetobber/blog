<?php include VIEW_DIR.'/Shared/Header.php' ?>

<?php if (!empty($model['content'])): ?>
    <div class="list-group">
        <?php foreach ($model['content'] as $post): ?>
            <a href="/person/<?= $post['owner'].'#'.$post['id'] ?>" class="list-group-item list-group-item-action">
                <div class="d-flex">
                    <span><?= _e($post['title']) ?></span>
                    <span class="ml-auto">
                        <span>Sent to <b><?= _e($post['owner']) ?></b></span>
                        <span>on <?= $post['created'] ?></span>
                    </span>
                </div>
            </a>
        <?php endforeach ?>
    </div>
<?php else: ?>
    <div class="d-flex justify-content-center">
        <h5>You do not have any posts.</h5>
    </div>
<?php endif ?>

<?php include VIEW_DIR.'/Shared/Footer.php' ?>
