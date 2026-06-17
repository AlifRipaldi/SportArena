<?php
$ownerMenus = array(
    array('key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house', 'url' => app_url('pemilik/dashboard')),
    array('key' => 'lapangan', 'label' => 'Lapangan Saya', 'icon' => 'fa-map-location-dot', 'url' => app_url('pemilik/lapangan')),
    array('key' => 'jadwal', 'label' => 'Jadwal Booking', 'icon' => 'fa-calendar-check', 'url' => app_url('pemilik/jadwal')),
    array('key' => 'booking', 'label' => 'Booking', 'icon' => 'fa-calendar-days', 'url' => app_url('pemilik/booking')),
    array('key' => 'pendapatan', 'label' => 'Pendapatan', 'icon' => 'fa-coins', 'url' => app_url('pemilik/dashboard#pendapatan')),
    array('key' => 'ulasan', 'label' => 'Ulasan & Rating', 'icon' => 'fa-star', 'url' => app_url('pemilik/dashboard#ulasan')),
    array('key' => 'statistik', 'label' => 'Statistik', 'icon' => 'fa-chart-column', 'url' => app_url('pemilik/dashboard#statistik')),
    array('key' => 'profil', 'label' => 'Profil', 'icon' => 'fa-circle-user', 'url' => app_url('pemilik/dashboard#profil')),
    array('key' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'fa-gear', 'url' => app_url('pemilik/dashboard#pengaturan')),
);

$currentMenu = isset($activeMenu) ? $activeMenu : 'dashboard';
$displayName = isset($userName) ? $userName : 'Pemilik Arena';
$displayRole = 'Pemilik Lapangan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'Dashboard Pemilik | Arena Sport'); ?></title>
    <link rel="stylesheet" href="<?php echo e(app_asset('css/style.css?v=46')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-mode owner-mode">
    <div class="admin-layout owner-layout">
        <aside class="admin-sidebar owner-sidebar" aria-label="Navigasi pemilik lapangan">
            <div class="admin-sidebar-header owner-sidebar-header">
                <a class="admin-brand owner-brand" href="<?php echo e(app_url('pemilik/dashboard')); ?>">
                    <img src="<?php echo e(app_asset('img/logo.png')); ?>" alt="Arena Sport">
                </a>
            </div>

            <nav class="admin-sidebar-menu owner-sidebar-menu">
                <?php foreach ($ownerMenus as $menu): ?>
                    <a href="<?php echo e($menu['url']); ?>" class="<?php echo $currentMenu === $menu['key'] ? 'active' : ''; ?>">
                        <i class="fa-solid <?php echo e($menu['icon']); ?>"></i>
                        <span><?php echo e($menu['label']); ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="owner-sidebar-card" aria-label="Ringkasan operasional">
                <div class="owner-sidebar-visual" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <strong>Kelola lapangan Anda dengan mudah</strong>
                <p>Pantau booking, pendapatan, dan tingkatkan pelayanan.</p>
            </div>

            <a class="admin-logout owner-logout" href="<?php echo e(app_url('public/logout.php')); ?>">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </aside>

        <div class="admin-wrapper owner-wrapper">
            <header class="admin-topbar owner-topbar">
                <div class="owner-topbar-space"></div>

                <div class="admin-profile owner-profile">
                    <button class="admin-notification owner-notification" type="button" aria-label="Notifikasi">
                        <i class="fa-regular fa-bell"></i>
                        <span>3</span>
                    </button>

                    <button class="admin-user-button owner-user-button" type="button">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($displayName); ?>&background=20314a&color=ffffff" alt="Foto profil">
                        <span>
                            <strong><?php echo e($displayName); ?></strong>
                            <small><?php echo e($displayRole); ?></small>
                        </span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                </div>
            </header>

            <main class="admin-main-content owner-main-content">
                <?php echo $content; ?>
            </main>
        </div>
    </div>
</body>
</html>
