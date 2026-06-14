<?php
$adminMenus = array(
    array('key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house', 'url' => app_url('admin/dashboard')),
    array('key' => 'lapangan', 'label' => 'Kelola Lapangan', 'icon' => 'fa-map-location-dot', 'url' => app_url('admin/lapangan')),
    array('key' => 'booking', 'label' => 'Kelola Booking', 'icon' => 'fa-calendar-check', 'url' => app_url('admin/booking')),
    array('key' => 'user', 'label' => 'Kelola User', 'icon' => 'fa-users', 'url' => app_url('admin/users')),
    array('key' => 'ulasan', 'label' => 'Ulasan & Rating', 'icon' => 'fa-star', 'url' => app_url('admin/ulasan')),
    array('key' => 'transaksi', 'label' => 'Transaksi', 'icon' => 'fa-receipt', 'url' => app_url('admin/transaksi')),
    array('key' => 'laporan', 'label' => 'Laporan', 'icon' => 'fa-chart-column', 'url' => app_url('admin/laporan')),
    array('key' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'fa-gear', 'url' => app_url('admin/pengaturan')),
);

$currentMenu = isset($activeMenu) ? $activeMenu : 'dashboard';
$displayName = isset($userName) ? $userName : 'Admin Arena';
$displayRole = isset($userRole) ? $userRole : 'administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'Dashboard Admin | Arena Sport'); ?></title>
    <link rel="stylesheet" href="<?php echo e(app_asset('css/style.css?v=12')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-mode">
    <div class="admin-layout">
        <aside class="admin-sidebar" aria-label="Navigasi admin">
            <div class="admin-sidebar-header">
                <a class="admin-brand" href="<?php echo e(app_url('admin/dashboard')); ?>">
                    <img src="<?php echo e(app_asset('img/logo.png')); ?>" alt="Arena Sport">
                </a>
            </div>

            <nav class="admin-sidebar-menu">
                <?php foreach ($adminMenus as $menu): ?>
                    <a href="<?php echo e($menu['url']); ?>" class="<?php echo $currentMenu === $menu['key'] ? 'active' : ''; ?>">
                        <i class="fa-solid <?php echo e($menu['icon']); ?>"></i>
                        <span><?php echo e($menu['label']); ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <a class="admin-logout" href="<?php echo e(app_url('public/logout.php')); ?>">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </aside>

        <div class="admin-wrapper">
            <header class="admin-topbar">
                <form class="admin-search" action="#" method="get">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" name="q" placeholder="Cari apa saja..." aria-label="Cari data admin">
                </form>

                <div class="admin-profile">
                    <button class="admin-notification" type="button" aria-label="Notifikasi">
                        <i class="fa-regular fa-bell"></i>
                        <span>3</span>
                    </button>

                    <button class="admin-user-button" type="button">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($displayName); ?>&background=121a28&color=ffffff" alt="Foto profil">
                        <span>
                            <strong><?php echo e($displayName); ?></strong>
                            <small><?php echo e(ucfirst($displayRole)); ?></small>
                        </span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                </div>
            </header>

            <main class="admin-main-content">
                <?php echo $content; ?>
            </main>
        </div>
    </div>
</body>
</html>
