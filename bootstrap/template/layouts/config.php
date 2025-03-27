<!DOCTYPE html>
<html lang="<?= $currentLanguage ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $title ?? 'Configuration - Catalyst Framework' ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= isset($asset) ? $asset('inspinia/img/favicon.ico') : '/assets/inspinia/img/favicon.ico' ?>">

    <!-- Bootstrap css -->
    <link href="<?= isset($asset) ? $asset('inspinia/css/bootstrap.min.css') : '/assets/inspinia/css/bootstrap.min.css' ?>" rel="stylesheet" type="text/css">

    <!-- Icons css -->
    <link href="<?= isset($asset) ? $asset('inspinia/plugins/fontawesome/css/all.min.css') : '/assets/inspinia/plugins/fontawesome/css/all.min.css' ?>" rel="stylesheet" type="text/css">

    <!-- Animate.css -->
    <link href="<?= isset($asset) ? $asset('inspinia/plugins/animate/css/animate.min.css') : '/assets/inspinia/plugins/animate/css/animate.min.css' ?>" rel="stylesheet">

    <!-- Style css -->
    <link href="<?= isset($asset) ? $asset('inspinia/css/style.min.css') : '/assets/inspinia/css/style.min.css' ?>" rel="stylesheet" type="text/css">

    <!-- Custom Landing CSS -->
    <link href="<?= isset($asset) ? $asset('css/landing.css') : '/assets/css/landing.css' ?>" rel="stylesheet" type="text/css">

    <!-- Head.js - Theme management -->
    <script src="<?= isset($asset) ? $asset('inspinia/js/head.js') : '/assets/inspinia/js/head.js' ?>"></script>

    <!-- Additional head content -->
    <?= $headContent ?? '' ?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Config CSS -->
    <link href="<?= isset($asset) ? $asset('css/config.css') : '/assets/css/config.css' ?>" rel="stylesheet" type="text/css">

    <!-- Additional head content -->
    <?= $headContent ?? '' ?>
</head>

<body>

<!-- Header -->
<div class="config-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <span class="config-title">
                        Catalyst Framework Configuration
                        <?php if (isset($currentEnvironment)): ?>
                            <span class="badge bg-primary env-badge text-white"><?= ucfirst($currentEnvironment ?? 'development') ?></span>
                            <?php if (isset($section)): ?>
                                <div class="col-md-12 d-flex align-items-center">
                                    <a href="/configure" class="btn btn-info">
                                        <i class="fa fa-arrow-left"></i> Back to Configuration
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </span>
                <p class="config-subtitle">Configure your application settings and services</p>
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<div class="container config-container">
    <?php if (isset($section)): ?>
        <?php
        $sectionDisplayNames = [
            'app' => 'Application',
            'db' => 'Database',
            'ftp' => 'FTP',
            'mail' => 'Mail',
            'session' => 'Session',
            'tools' => 'Developer Tools'
        ];


        // Get the current section from the URL or controller
        $currentSection = 'db'; // This would be dynamically determined in actual implementation

        // Get display name for current section
        $displayName = $sectionDisplayNames[$section];

        ?>
        <div class="row wrapper border-bottom white-bg page-heading mb-2">
            <div class="col-lg-12">
                <h2><?= $displayName ?> Configuration</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <span>Configuration</span>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/configure">Menu</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <strong><?= $displayName ?></strong>
                    </li>
                </ol>
            </div>
        </div>
    <?php else: ?>
        <div class="row wrapper border-bottom white-bg page-heading mb-2">
            <div class="col-lg-12">
                <h2>Catalyst Framework Configuration</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <span>Configuration</span>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="fw-bold" href="/configure">Menu</a>
                    </li>
                </ol>
            </div>
        </div>
    <?php endif; ?>

    <!-- Insert flash messages here -->
    <?php include implode(DS, [PD, 'bootstrap', 'template', 'partials', 'flash-messages.php']); ?>

    <!-- View content -->
    <?= $viewContent ?>
</div>

<!-- Footer correctamente posicionado -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-right">
                    <?= isset($t) ? $t('common.footer.powered_by') : 'Powered by' ?> <strong><?= isset($t) ? $t('common.app.name') : 'Catalyst Framework' ?></strong>
                </div>
                <div>
                    <strong><?= isset($t) ? $t('common.footer.copyright', ['year' => date('Y')]) : 'Copyright © ' . date('Y') ?></strong>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Mainly Plugin Scripts -->
<script src="<?= isset($asset) ? $asset('inspinia/plugins/jquery/js/jquery.min.js') : '/assets/inspinia/plugins/jquery/js/jquery.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/bootstrap/js/bootstrap.bundle.min.js') : '/assets/inspinia/plugins/bootstrap/js/bootstrap.bundle.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/metismenu/js/metisMenu.min.js') : '/assets/inspinia/plugins/metismenu/js/metisMenu.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/pace-js/js/pace.min.js') : '/assets/inspinia/plugins/pace-js/js/pace.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/wow.js/js/wow.min.js') : '/assets/inspinia/plugins/wow.js/js/wow.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/lucide/js/lucide.min.js') : '/assets/inspinia/plugins/lucide/js/lucide.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/js/inspinia.js') : 'assets/inspinia/js/inspinia.js' ?>"></script>

<script src="<?= isset($asset) ? $asset('js/toasts.js') : 'assets/js/toasts.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('js/main.js') : 'assets/js/main.js' ?>"></script>


<!-- Page-specific scripts -->
<?= $scripts ?? '' ?>
</body>
</html>