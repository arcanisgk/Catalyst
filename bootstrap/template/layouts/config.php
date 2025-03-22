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

    <style>
        body {
            background-color: #f3f3f4;
            padding-top: 0;
        }

        .config-header {
            background-color: #2f4050;
            color: white;
            padding: 20px 0;
        }

        .config-title {
            margin: 0;
            font-size: 24px;
        }

        .config-subtitle {
            opacity: 0.8;
            margin-top: 5px;
            margin-bottom: 0;
        }

        .config-container {
            margin-top: 5px;
        }

        .section-head {
            margin-top: 10px;
        }

        .env-badge {
            font-size: 14px;
            vertical-align: middle;
            margin-left: 10px;
        }

        .back-link {
            color: #fff;
            opacity: 0.8;
            margin-right: 15px;
            transition: opacity 0.2s;
        }

        .back-link:hover {
            color: #fff;
            opacity: 1;
            text-decoration: none;
        }

        .footer {
            background-color: transparent;
            border-top: 1px solid #e7eaec;
            padding: 20px 0;
            /*margin-top: 30px;*/
            width: 100%;
            position: relative;
            bottom: 0;
        }

        /* Estilos para index.php */
        .env-selector {
            margin-bottom: 1rem;
        }

        .config-card {
            transition: all 0.3s ease;
            cursor: pointer;
            color: inherit;
            height: 100%;
        }

        .config-card:hover,
        .config-card:focus {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            color: inherit;
        }

        .config-card:focus {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .card-icon i {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Estilos para app.php */
        .app-section {
            margin-bottom: 30px;
            border: 1px solid #e7eaec;
            border-radius: 5px;
            padding: 20px;
            background-color: #fff;
        }

        .app-section-title {
            border-bottom: 1px solid #e7eaec;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .language-tag {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
            padding: 5px 10px;
            background-color: #f3f3f4;
            border: 1px solid #e7eaec;
            border-radius: 3px;
        }

        .add-language-btn {
            margin-top: 10px;
        }

        /* Estilos para db.php */
        .db-connection {
            border: 1px solid #e7eaec;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            position: relative;
        }

        .db-connection-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e7eaec;
        }

        .db-connection-actions {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .add-connection-container {
            margin-top: 30px;
        }

        /* Estilos para ftp.php */
        .ftp-connection {
            border: 1px solid #e7eaec;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            position: relative;
        }

        .ftp-connection-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e7eaec;
        }

        .ftp-connection-actions {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .add-ftp-container {
            margin-top: 30px;
        }

        /* Estilos para mail.php */
        .mail-connection {
            border: 1px solid #e7eaec;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            position: relative;
        }

        .mail-connection-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e7eaec;
        }

        .mail-connection-actions {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .add-mail-container {
            margin-top: 30px;
        }

        .mail-options-section {
            border-top: 1px solid #e7eaec;
            margin-top: 15px;
            padding-top: 15px;
        }

        /* Estilos para session.php */
        .session-section {
            border: 1px solid #e7eaec;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
        }

        .session-section-title {
            border-bottom: 1px solid #e7eaec;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .service-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .service-check {
            flex: 0 0 auto;
            width: calc(25% - 10px);
            margin-bottom: 10px;
        }

        /* Estilos para tools.php */
        .tool-section {
            margin-bottom: 30px;
        }

        .tool-card {
            background-color: #fff;
            border: 1px solid #e7eaec;
            border-radius: 5px;
            padding: 20px;
            height: 100%;
            transition: all 0.3s ease;
        }

        .tool-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .tool-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #1ab394;
        }

        .tool-status {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e7eaec;
        }

        .tool-actions {
            margin-top: 15px;
        }

    </style>

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
        <div class="row mb-2">
            <div class="col-md-12 d-flex align-items-center">
                <a href="/configure" class="btn btn-info">
                    <i class="fa fa-arrow-left"></i> Back to Configuration
                </a>
                <h2 class="d-inline-block ms-2 section-head"><?= ucfirst($section ?? '') ?> Configuration</h2>
            </div>
        </div>
    <?php endif; ?>

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

<!-- Page-specific scripts -->
<?= $scripts ?? '' ?>
</body>
</html>