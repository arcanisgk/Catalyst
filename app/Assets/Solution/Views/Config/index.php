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

<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Environment Selector -->
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <div class="ibox collapsed">
                <div class="ibox-title">
                    <h5>Environment Settings</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="d-flex align-items-center mb-3">
                        <h4 class="no-margins">Current Environment:
                            <span class="label label-primary fs-6"><?= $currentEnvironment ?></span>
                        </h4>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> To change the environment:
                        <ol class="pl-3 mb-0">
                            <li>Edit your <code>.env</code> file in the root directory</li>
                            <li>Set <code>APP_ENV</code> to either <code>development</code> or <code>production</code></li>
                            <li>Example: <code>APP_ENV=production</code></li>
                        </ol>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                            <i class="fa fa-refresh"></i> Refresh Page
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Cards -->
    <div class="row">
        <?php
        // Create a mapping of section names to display names
        $sectionDisplayNames = [
            'app' => 'Application',
            'db' => 'Database',
            'ftp' => 'FTP',
            'mail' => 'Mail',
            'session' => 'Session',
            'tools' => 'Developer Tools'
        ];

        foreach ($sections as $section):
            // Get display name from the mapping, or use capitalized section name as fallback
            $displayName = $sectionDisplayNames[$section] ?? ucfirst($section);
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= $displayName ?> Configuration</h5>
                        <div class="ibox-tools">
                            <?php if (isset($customConfigs[$section]) && $customConfigs[$section]): ?>
                                <span class="label label-primary">Custom</span>
                            <?php else: ?>
                                <span class="label label-default">Default</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="text-center m-t-md">
                            <div class="mb-3">
                                <?php switch ($section):
                                    case 'app': ?>
                                        <i class="fa fa-cogs fa-4x text-primary"></i>
                                        <?php break; ?>
                                    <?php case 'session': ?>
                                        <i class="fa-solid fa-shield-halved fa-4x text-success"></i>
                                        <?php break; ?>
                                    <?php case 'db': ?>
                                        <i class="fa fa-database fa-4x text-info"></i>
                                        <?php break; ?>
                                    <?php case 'ftp': ?>
                                        <i class="fa fa-server fa-4x text-warning"></i>
                                        <?php break; ?>
                                    <?php case 'mail': ?>
                                        <i class="fa fa-envelope fa-4x text-danger"></i>
                                        <?php break; ?>
                                    <?php case 'tools': ?>
                                        <i class="fa fa-wrench fa-4x"></i>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <i class="fa fa-question-circle fa-4x"></i>
                                    <?php endswitch; ?>
                            </div>

                            <p>
                                <?php switch ($section):
                                    case 'app': ?>
                                        Configure general application and company data
                                        <?php break; ?>
                                    <?php case 'session': ?>
                                        Manage session parameters and login settings
                                        <?php break; ?>
                                    <?php case 'db': ?>
                                        Set up database connection credentials
                                        <?php break; ?>
                                    <?php case 'ftp': ?>
                                        Configure FTP connection settings
                                        <?php break; ?>
                                    <?php case 'mail': ?>
                                        Manage email server configurations
                                        <?php break; ?>
                                    <?php case 'tools': ?>
                                        Set up development tools and utilities
                                        <?php break; ?>
                                    <?php default: ?>
                                        Configure system settings
                                    <?php endswitch; ?>
                            </p>

                            <a href="/configure/<?= $section ?>" class="btn btn-primary">Configure</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
