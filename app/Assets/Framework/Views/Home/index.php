<div class="jumbotron">
    <h1 class="display-4"><?= $t('welcome') ?></h1>
    <p class="lead">
        <?= isset($t) ? $t('home.intro') : 'Welcome' ?>
    </p>
    <hr class="my-4">
    <p><?= isset($t) ? $t('home.description') : 'Description' ?></p>
</div>

<?php if (isset($success_message)): ?>
    <?= $include('alert', [
        'type' => 'success',
        'message' => $success_message
    ]) ?>
<?php endif; ?>

<div class="row">
    <?php foreach ($features as $feature): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= $feature['title'] ?></h5>
                    <p class="card-text"><?= $feature['description'] ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
