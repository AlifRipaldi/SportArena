<?php
$ownerMenus = array(
    array('key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house', 'url' => app_url('pemilik/dashboard')),
    array('key' => 'lapangan', 'label' => 'Lapangan Saya', 'icon' => 'fa-map-location-dot', 'url' => app_url('pemilik/lapangan')),
    array('key' => 'jadwal', 'label' => 'Jadwal Booking', 'icon' => 'fa-calendar-check', 'url' => app_url('pemilik/jadwal')),
    array('key' => 'pendapatan', 'label' => 'Pendapatan', 'icon' => 'fa-coins', 'url' => app_url('pemilik/pendapatan')),
    array('key' => 'ulasan', 'label' => 'Ulasan & Rating', 'icon' => 'fa-star', 'url' => app_url('pemilik/ulasan')),
    array('key' => 'profil', 'label' => 'Profil', 'icon' => 'fa-circle-user', 'url' => app_url('pemilik/profil')),
    array('key' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'fa-gear', 'url' => app_url('pemilik/pengaturan')),
);

$currentMenu = isset($activeMenu) ? $activeMenu : 'dashboard';
$displayName = isset($userName) ? $userName : 'Pemilik Arena';
$displayRole = 'Pemilik Lapangan';
$topbarSearchPlaceholder = isset($ownerTopbarSearchPlaceholder) ? $ownerTopbarSearchPlaceholder : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'Dashboard Pemilik | Arena Sport'); ?></title>
    <link rel="stylesheet" href="<?php echo e(app_asset('css/style.css?v=60')); ?>">
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

            <a class="admin-logout owner-logout" href="<?php echo e(app_url('public/logout.php')); ?>">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </aside>

        <div class="admin-wrapper owner-wrapper">
            <header class="admin-topbar owner-topbar">
                <?php if ($topbarSearchPlaceholder !== ''): ?>
                    <label class="admin-search owner-topbar-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="search" placeholder="<?php echo e($topbarSearchPlaceholder); ?>" aria-label="<?php echo e($topbarSearchPlaceholder); ?>">
                    </label>
                <?php else: ?>
                    <div class="owner-topbar-space"></div>
                <?php endif; ?>

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
