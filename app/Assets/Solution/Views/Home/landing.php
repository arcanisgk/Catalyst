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

<!-- Header / Hero Section -->
<div id="inSlider" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
        <div class="item active">
            <div class="container">
                <div class="carousel-caption">
                    <div class="framework-logo">
                        <img src="<?= isset($asset) ? $asset('img/landing/catalyst.png') : '/assets/img/landing/catalyst.png' ?>" alt="Catalyst Framework" class="img-fluid">
                    </div>

                    <h1>Modern PHP<br/>
                        Framework for Efficient Development</h1>
                    <p>Build robust applications faster with Catalyst's powerful, lightweight architecture.</p>
                    <p>
                        <a class="btn btn-lg btn-success" href="/configure" role="button">Configure</a>
                        <a class="btn btn-lg btn-primary" href="#features" role="button">Learn More</a>
                        <a class="btn btn-lg btn-info" href="https://github.com/arcanisgk/catalyst" role="button">GitHub</a>
                    </p>
                </div>
            </div>
            <div class="header-back one"></div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section id="features" class="container services">
    <div class="row">
        <div class="col-sm-12">
            <h2 class="text-center m-t-lg">Framework Features</h2>
            <p class="text-center lead">Catalyst provides everything you need to build modern PHP applications</p>
        </div>
    </div>
    <div class="row text-center">
        <?php foreach ($features as $feature): ?>
            <div class="col-sm-4 wow fadeInUp">
                <div class="m">
                    <i class="fa fa-<?= $feature['icon'] ?? 'code' ?> features-icon"></i>
                    <h2><?= $feature['title'] ?></h2>
                    <p><?= $feature['description'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Documentation, Team and other sections as shown in the previous response -->
<!-- The rest of the landing.php content remains the same as in my previous response -->
