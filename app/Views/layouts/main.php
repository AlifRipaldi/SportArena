<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'Arena Sport'); ?></title>
    <link rel="stylesheet" href="<?php echo e(app_asset('css/style.css?v=7')); ?>">
</head>
<body>
    <header>
        <div class="container nav-container">
            <div class="logo">Arena<span>Sport</span></div>
            <nav>
                <ul>
                    <li><a href="<?php echo e(app_url('/')); ?>">Beranda</a></li>
                    <li><a href="<?php echo e(app_url('#lapangan')); ?>">Lapangan</a></li>
                    <li><a href="<?php echo e(app_url('#cara-kerja')); ?>">Cara Kerja</a></li>
                    <li><a href="<?php echo e(app_url('#tentang-kami')); ?>">Tentang Kami</a></li>
                    <li><a href="<?php echo e(app_url('#kontak')); ?>">Kontak</a></li>
                </ul>
            </nav>
            <div class="nav-actions">
                <div class="location-select">Kota Parepare</div>
                <a href="<?php echo e(app_url('public/login.php')); ?>" class="btn-login">Login / Daftar</a>
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
