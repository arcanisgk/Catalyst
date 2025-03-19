<div class="jumbotron">
    <h1 class="display-4"><?= $t('welcome') ?></h1>
    <p class="lead">
        <?= $t('home_intro', ['version' => '1.0']) ?>
    </p>
    <hr class="my-4">
    <p><?= $t('home_description') ?></p>
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
