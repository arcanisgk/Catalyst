<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Assets
 * @see       https://github.com/arcanisgk/catalyst
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2023 - 2025
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *
 * @note      This program is distributed in the hope that it will be useful
 *            WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *            or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @category  Framework
 * @filesource
 *
 * @link      https://catalyst.dock Local development URL
 *
 */

?>
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
