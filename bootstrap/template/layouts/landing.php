<!DOCTYPE html>
<html lang="<?= $currentLanguage ?? 'en' ?>" data-bs-theme="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? (isset($t) ? $t('common.app.name') : 'Catalyst Framework') ?></title>
    <meta content="Catalyst PHP Framework" name="description"/>

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
</head>

<body class="landing-page">
<!-- Main content -->
<?= $viewContent ?>

<!-- Mainly Plugin Scripts -->
<script src="<?= isset($asset) ? $asset('inspinia/plugins/jquery/js/jquery.min.js') : '/assets/inspinia/plugins/jquery/js/jquery.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/bootstrap/js/bootstrap.bundle.min.js') : '/assets/inspinia/plugins/bootstrap/js/bootstrap.bundle.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/metismenu/js/metisMenu.min.js') : '/assets/inspinia/plugins/metismenu/js/metisMenu.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/pace-js/js/pace.min.js') : '/assets/inspinia/plugins/pace-js/js/pace.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/wow.js/js/wow.min.js') : '/assets/inspinia/plugins/wow.js/js/wow.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('inspinia/plugins/lucide/js/lucide.min.js') : '/assets/inspinia/plugins/lucide/js/lucide.min.js' ?>"></script>

<!-- Custom JavaScript for Landing Page - Using ES6+ module -->
<script type="module" src="<?= isset($asset) ? $asset('js/landing.js') : '/assets/js/landing.js' ?>"></script>

<!-- Additional scripts -->
<?= $scripts ?? '' ?>
</body>
</html>
