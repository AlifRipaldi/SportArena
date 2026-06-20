<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(24));
}
$adminToken = $_SESSION['admin_csrf'];
$adminFlash = isset($_SESSION['admin_flash']) && is_array($_SESSION['admin_flash']) ? $_SESSION['admin_flash'] : null;
unset($_SESSION['admin_flash']);
$adminNotificationCount = 0;
$adminNotifications = array();
try {
    $adminNotificationData = new \App\Models\ArenaData();
    $adminUserId = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';
    $adminNotificationCount = (int) $adminNotificationData->value('SELECT COUNT(*) value FROM notifikasi WHERE ID_User=? AND Dibaca_pada IS NULL', 's', array($adminUserId));
    $adminNotifications = $adminNotificationData->rows('SELECT Judul,Pesan,Tipe,Link,Dibaca_pada,created_at FROM notifikasi WHERE ID_User=? ORDER BY created_at DESC LIMIT 6', 's', array($adminUserId));
} catch (\Throwable $exception) {
    $adminNotificationCount = 0;
    $adminNotifications = array();
}
$adminMenus = array(
    array('key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-house', 'url' => app_url('admin/dashboard')),
    array('key' => 'lapangan', 'label' => 'Kelola Lapangan', 'icon' => 'fa-map-location-dot', 'url' => app_url('admin/lapangan')),
    array('key' => 'booking', 'label' => 'Kelola Booking', 'icon' => 'fa-calendar-check', 'url' => app_url('admin/booking')),
    array('key' => 'user', 'label' => 'Kelola Customer', 'icon' => 'fa-users', 'url' => app_url('admin/users')),
    array('key' => 'ulasan', 'label' => 'Ulasan & Rating', 'icon' => 'fa-star', 'url' => app_url('admin/ulasan')),
    array('key' => 'transaksi', 'label' => 'Transaksi', 'icon' => 'fa-receipt', 'url' => app_url('admin/transaksi')),
    array('key' => 'laporan', 'label' => 'Laporan', 'icon' => 'fa-chart-column', 'url' => app_url('admin/laporan')),
    array('key' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'fa-gear', 'url' => app_url('admin/pengaturan')),
);

$currentMenu = isset($activeMenu) ? $activeMenu : 'dashboard';
$displayName = isset($userName) ? $userName : 'Admin Arena';
$displayRole = isset($userRole) ? $userRole : 'administrator';
$topbarSearchPlaceholder = isset($searchPlaceholder) ? $searchPlaceholder : 'Cari apa saja...';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'Dashboard Admin | Arena Sport'); ?></title>
    <link rel="stylesheet" href="<?php echo e(app_asset_versioned('css/style.css')); ?>">
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
                <form class="admin-search" action="<?php echo e(app_url('admin/search')); ?>" method="get">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" name="q" placeholder="<?php echo e($topbarSearchPlaceholder); ?>" aria-label="Cari data admin">
                </form>

                <div class="admin-profile">
                    <div class="admin-notification-wrapper">
                        <button class="admin-notification" type="button" aria-label="Buka notifikasi" aria-expanded="false" aria-controls="adminNotificationPopup" data-notification-toggle>
                            <i class="fa-regular fa-bell"></i>
                            <?php if ($adminNotificationCount > 0): ?><span><?php echo e($adminNotificationCount); ?></span><?php endif; ?>
                        </button>
                        <section class="admin-notification-popup" id="adminNotificationPopup" aria-label="Daftar notifikasi" hidden>
                            <header>
                                <div><strong>Notifikasi</strong><small><?php echo e($adminNotificationCount); ?> belum dibaca</small></div>
                                <button type="button" aria-label="Tutup notifikasi" data-notification-close>&times;</button>
                            </header>
                            <div class="admin-notification-popup-list">
                                <?php if (empty($adminNotifications)): ?>
                                    <div class="admin-notification-empty"><i class="fa-regular fa-bell-slash"></i><span>Belum ada notifikasi.</span></div>
                                <?php endif; ?>
                                <?php foreach ($adminNotifications as $notification): ?>
                                    <article class="admin-notification-popup-item <?php echo empty($notification['Dibaca_pada']) ? 'unread' : ''; ?>">
                                        <span class="admin-notification-popup-icon"><i class="fa-solid <?php echo strtolower((string) $notification['Tipe']) === 'error' ? 'fa-circle-exclamation' : 'fa-bell'; ?>"></i></span>
                                        <div><strong><?php echo e($notification['Judul']); ?></strong><p><?php echo e($notification['Pesan']); ?></p><time><?php echo e(date('d/m/Y H:i', strtotime($notification['created_at']))); ?> WITA</time></div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    </div>

                    <a class="admin-user-button" href="<?php echo e(app_url('admin/pengaturan?tab=akun')); ?>">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($displayName); ?>&background=121a28&color=ffffff" alt="Foto profil">
                        <span>
                            <strong><?php echo e($displayName); ?></strong>
                            <small><?php echo e(ucfirst($displayRole)); ?></small>
                        </span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>
                </div>
            </header>

            <main class="admin-main-content">
                <?php if ($adminFlash): ?>
                    <div class="admin-flash <?php echo e($adminFlash['type']); ?>" role="status">
                        <i class="fa-solid <?php echo $adminFlash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                        <span><?php echo e($adminFlash['message']); ?></span>
                        <button type="button" aria-label="Tutup pesan">&times;</button>
                    </div>
                <?php endif; ?>
                <?php echo $content; ?>
            </main>
        </div>
    </div>
    <script>
    (function () {
        document.querySelectorAll('.admin-flash button').forEach(function (button) {
            button.addEventListener('click', function () { button.parentElement.remove(); });
        });
        window.setTimeout(function () {
            document.querySelectorAll('.admin-flash').forEach(function (item) { item.remove(); });
        }, 6000);

        document.querySelectorAll('[data-dialog-open]').forEach(function (button) {
            button.addEventListener('click', function () {
                var dialog = document.getElementById(button.getAttribute('data-dialog-open'));
                if (!dialog) return;
                var payload = {};
                try { payload = JSON.parse(button.getAttribute('data-payload') || '{}'); } catch (error) {}
                Object.keys(payload).forEach(function (key) {
                    var input = dialog.querySelector('[name="' + key + '"]');
                    if (input) input.value = payload[key] == null ? '' : payload[key];
                });
                if (typeof dialog.showModal === 'function') dialog.showModal();
            });
        });
        document.querySelectorAll('[data-dialog-close]').forEach(function (button) {
            button.addEventListener('click', function () { button.closest('dialog').close(); });
        });
        document.querySelectorAll('dialog').forEach(function (dialog) {
            dialog.addEventListener('click', function (event) {
                if (event.target === dialog) dialog.close();
            });
        });

        document.querySelectorAll('[data-admin-filter]').forEach(function (container) {
            var inputs = container.querySelectorAll('input[type="search"], select');
            var targetSelector = container.getAttribute('data-admin-filter');
            var targets = document.querySelectorAll(targetSelector);
            function applyFilter() {
                var terms = Array.prototype.map.call(inputs, function (input) {
                    return input.value.replace(/^(Status|Jenis|Tanggal|Role|Rating|Metode|Lapangan):?\s*/i, '').toLowerCase();
                }).filter(function (value) { return value && value !== 'semua' && value.indexOf('semua ') !== 0; });
                targets.forEach(function (target) {
                    var haystack = (target.getAttribute('data-filter-text') || target.textContent).toLowerCase();
                    target.hidden = !terms.every(function (term) { return haystack.indexOf(term) !== -1; });
                });
            }
            inputs.forEach(function (input) { input.addEventListener(input.tagName === 'SELECT' ? 'change' : 'input', applyFilter); });
        });

        document.querySelectorAll('[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!window.confirm(form.getAttribute('data-confirm'))) event.preventDefault();
            });
        });
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = button.parentElement.querySelector('input');
                if (input) input.type = input.type === 'password' ? 'text' : 'password';
            });
        });

        var notificationToggle = document.querySelector('[data-notification-toggle]');
        var notificationPopup = document.getElementById('adminNotificationPopup');
        function closeNotificationPopup() {
            if (!notificationPopup || !notificationToggle) return;
            notificationPopup.hidden = true;
            notificationToggle.setAttribute('aria-expanded', 'false');
        }
        if (notificationToggle && notificationPopup) {
            notificationToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                notificationPopup.hidden = !notificationPopup.hidden;
                notificationToggle.setAttribute('aria-expanded', notificationPopup.hidden ? 'false' : 'true');
            });
            notificationPopup.addEventListener('click', function (event) { event.stopPropagation(); });
            document.querySelectorAll('[data-notification-close]').forEach(function (button) { button.addEventListener('click', closeNotificationPopup); });
            document.addEventListener('click', closeNotificationPopup);
            document.addEventListener('keydown', function (event) { if (event.key === 'Escape') closeNotificationPopup(); });
        }
    }());
    </script>
</body>
</html>
