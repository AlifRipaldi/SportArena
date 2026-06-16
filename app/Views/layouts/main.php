<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'Arena Sport'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(app_asset('css/style.css?v=11')); ?>">
    <link rel="stylesheet" href="<?php echo e(app_asset('css/home.css?v=5')); ?>">
</head>
<body class="site-home">
    <header>
        <div class="container nav-container">
            <a href="<?php echo e(app_url('/')); ?>" class="logo">
                <img src="<?php echo e(app_asset('img/logo.png')); ?>" alt="Arena Sport Logo">
            </a>
            <nav>
                <ul>
                    <li><a class="active" href="<?php echo e(app_url('/')); ?>">Beranda</a></li>
                    <li><a href="<?php echo e(app_url('#lapangan')); ?>">Lapangan</a></li>
                    <li><a href="<?php echo e(app_url('#cara-kerja')); ?>">Cara Kerja</a></li>
                    <li><a href="<?php echo e(app_url('#tentang-kami')); ?>">Tentang Kami</a></li>
                    <li><a href="<?php echo e(app_url('#kontak')); ?>">Kontak</a></li>
                </ul>
            </nav>
            <div class="nav-actions">
                <div class="location-select">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Kota Parepare</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <a href="<?php echo e(app_url('public/login.php')); ?>" class="btn-login">
                    <i class="fa-regular fa-user"></i>
                    <span>Login / Daftar</span>
                </a>
            </div>
        </div>
    </header>

    <?php echo $content; ?>

    <footer>
        <div class="container">
            <p>&copy; 2026 Arena Sport Management System.</p>
        </div>
    </footer>
</body>
</html>
