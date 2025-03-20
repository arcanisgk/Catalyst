<!DOCTYPE html>
<html lang="<?= $currentLanguage ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $title ?? (isset($t) ? $t('common.app.name') : 'Catalyst Framework') ?></title>

    <!-- icons -->
    <link rel="shortcut icon" href="<?= isset($asset) ? $asset('assets/inspinia/img/favicon/favicon.ico') : 'assets/inspinia/img/favicon/favicon.ico' ?>">

    <!-- manifest -->


    <!-- css -->

    <link href="<?= isset($asset) ? $asset('assets/inspinia/css/bootstrap.min.css') : 'assets/inspinia/css/bootstrap.min.css' ?>" rel="stylesheet">
    <link href="<?= isset($asset) ? $asset('assets/inspinia/plugins/fontawesome/css/all.min.css') : 'assets/inspinia/plugins/fontawesome/css/all.min.css' ?>" rel="stylesheet">
    <link href="<?= isset($asset) ? $asset('assets/inspinia/plugins/animate/css/animate.min.css') : 'assets/inspinia/plugins/animate/css/animate.min.css' ?>" rel="stylesheet">
    <link href="<?= isset($asset) ? $asset('assets/inspinia/css/style.min.css') : 'assets/inspinia/css/style.min.css' ?>" rel="stylesheet">

    <!-- Additional head content -->
    <?= $headContent ?? '' ?>
</head>

<body class="<?= $bodyClass ?? '' ?>">
<div id="wrapper">
    <!-- Sidebar Navigation -->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <img alt="image" class="rounded-circle" src="<?= isset($asset) ? $asset('img/profile_small.jpg') : '/assets/img/profile_small.jpg' ?>" width="48">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="block m-t-xs font-bold"><?= $userName ?? (isset($t) ? $t('common.messages.welcome') : 'Guest User') ?></span>
                            <span class="text-muted text-xs block">
                                    <?= $userRole ?? 'User' ?> <b class="caret"></b>
                                </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a class="dropdown-item" href="profile.html"><?= isset($t) ? $t('common.navigation.profile') : 'Profile' ?></a></li>
                            <li><a class="dropdown-item" href="contacts.html"><?= isset($t) ? $t('contact.title') : 'Contacts' ?></a></li>
                            <li><a class="dropdown-item" href="mailbox.html"><?= isset($t) ? $t('common.navigation.messages') ?? 'Mailbox' : 'Mailbox' ?></a></li>
                            <li class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="login.html"><?= isset($t) ? $t('common.navigation.logout') : 'Logout' ?></a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        CF+
                    </div>
                </li>

                <!-- Navigation items -->
                <li class="<?= isset($activeMenu) && $activeMenu == 'home' ? 'active' : '' ?>">
                    <a href="/">
                        <i class="fa fa-home"></i>
                        <span class="nav-label"><?= isset($t) ? $t('common.navigation.home') : 'Home' ?></span>
                    </a>
                </li>
                <li class="<?= isset($activeMenu) && $activeMenu == 'about' ? 'active' : '' ?>">
                    <a href="/about">
                        <i class="fa fa-info-circle"></i>
                        <span class="nav-label"><?= isset($t) ? $t('common.navigation.about') : 'About' ?></span>
                    </a>
                </li>
                <li class="<?= isset($activeMenu) && $activeMenu == 'contact' ? 'active' : '' ?>">
                    <a href="/contact">
                        <i class="fa fa-envelope"></i>
                        <span class="nav-label"><?= isset($t) ? $t('common.navigation.contact') : 'Contact' ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Page Container -->
    <div id="page-wrapper" class="gray-bg">
        <!-- Top Navigation -->
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#"><i class="fa fa-bars"></i></a>
                    <form role="search" class="navbar-form-custom">
                        <div class="form-group">
                            <input type="text" placeholder="<?= isset($t) ? $t('common.messages.search') ?? 'Search for something...' : 'Search for something...' ?>" class="form-control"
                                   name="top-search" id="top-search">
                        </div>
                    </form>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <!-- Language Selector -->
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                            <i class="fa fa-globe"></i> <?= strtoupper($currentLanguage ?? 'en') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-messages">
                            <li>
                                <a href="?lang=en" class="dropdown-item">
                                    English
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <a href="?lang=es" class="dropdown-item">
                                    Español
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Example notification dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell"></i> <span class="label label-primary">8</span>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts">
                            <li>
                                <a href="#" class="dropdown-item">
                                    <div>
                                        <i class="fa fa-envelope fa-fw"></i> <?= isset($t) ? $t('common.messages.notification_count') ?? 'You have 16 messages' : 'You have 16 messages' ?>
                                        <span class="float-right text-muted small">
                                            <?= isset($t) ? $t('common.dates.minutes_ago', ['count' => 4]) : '4 minutes ago' ?>
                                        </span>
                                    </div>
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <a href="#" class="dropdown-item text-center">
                                    <strong><?= isset($t) ? $t('common.buttons.see_all') ?? 'See All Alerts' : 'See All Alerts' ?></strong>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="login.html">
                            <i class="fa fa-sign-out"></i> <?= isset($t) ? $t('common.navigation.logout') : 'Log out' ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Page Header -->
        <?php if (isset($pageTitle) || isset($pageSubtitle)): ?>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2><?= $pageTitle ?? $title ?? (isset($t) ? $t('common.app.name') : 'Catalyst Framework') ?></h2>
                    <?php if (isset($pageSubtitle)): ?>
                        <ol class="breadcrumb">
                            <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                                <?php foreach ($breadcrumbs as $idx => $crumb): ?>
                                    <?php if ($idx === array_key_last($breadcrumbs)): ?>
                                        <li class="breadcrumb-item active">
                                            <strong><?= $crumb['label'] ?></strong>
                                        </li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item">
                                            <a href="<?= $crumb['url'] ?>"><?= $crumb['label'] ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="breadcrumb-item">
                                    <a href="/"><?= isset($t) ? $t('common.navigation.home') : 'Home' ?></a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <strong><?= $pageSubtitle ?></strong>
                                </li>
                            <?php endif; ?>
                        </ol>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Content Wrapper -->
        <div class="wrapper wrapper-content animated fadeInRight">
            <!-- Main View Content -->
            <?= $viewContent ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="float-right">
                <?= isset($t) ? $t('common.footer.powered_by') : 'Powered by' ?> <strong><?= isset($t) ? $t('common.app.name') : 'Catalyst Framework' ?></strong>
            </div>
            <div>
                <strong>
                    <?= isset($t) ? $t('common.footer.copyright', ['year' => date('Y')]) : 'Copyright © ' . date('Y') ?>
                </strong>
            </div>
        </div>
    </div>
</div>

<!-- Mainly Plugin Scripts -->

<script src="<?= isset($asset) ? $asset('assets/inspinia/plugins/jquery/js/jquery.min.js') : 'assets/inspinia/plugins/jquery/js/jquery.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('assets/inspinia/plugins/bootstrap/js/bootstrap.bundle.min.js') : 'assets/inspinia/plugins/bootstrap/js/bootstrap.bundle.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('assets/inspinia/plugins/metismenu/js/metisMenu.min.js') : 'assets/inspinia/plugins/metismenu/js/metisMenu.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('assets/inspinia/plugins/pace-js/js/pace.min.js') : 'assets/inspinia/plugins/pace-js/js/pace.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('assets/inspinia/plugins/wow.js/js/wow.min.js') : 'assets/inspinia/plugins/wow.js/js/wow.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('assets/inspinia/plugins/lucide/js/lucide.min.js') : 'assets/inspinia/plugins/lucide/js/lucide.min.js' ?>"></script>
<script src="<?= isset($asset) ? $asset('assets/inspinia/plugins/simplebar/js/simplebar.min.js') : 'assets/inspinia/plugins/simplebar/js/simplebar.min.js' ?>"></script>

<!-- Custom and Plugin Javascript -->
<script src="<?= isset($asset) ? $asset('assets/inspinia/js/inspinia.js') : 'assets/inspinia/js/inspinia.js' ?>"></script>

<!-- Head CSS -->
<script src="<?= isset($asset) ? $asset('assets/inspinia/js/head.js') : 'assets/inspinia/js/head.js' ?>"></script>

<!-- Custom Scripts -->
<script src="<?= isset($asset) ? $asset('js/main.js') : '/assets/js/main.js' ?>"></script>

<!-- Page-specific scripts -->
<?= $scripts ?? '' ?>
</body>
</html>
