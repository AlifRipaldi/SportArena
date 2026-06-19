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
    <link rel="stylesheet" href="<?php echo e(app_asset('css/style.css?v=71')); ?>">
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
                    <div class="owner-notification-wrap" data-owner-notification>
                        <button class="admin-notification owner-notification" type="button" aria-label="Notifikasi" aria-expanded="false" aria-controls="ownerNotificationPanel" data-owner-notification-toggle>
                            <i class="fa-regular fa-bell"></i>
                            <span data-owner-notification-count>3</span>
                        </button>

                        <section class="owner-notification-panel" id="ownerNotificationPanel" aria-label="Notifikasi pemilik" data-owner-notification-panel hidden>
                            <header class="owner-notification-head">
                                <h2>Notifikasi</h2>
                                <button type="button" aria-label="Tutup notifikasi" data-owner-notification-close>
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </header>

                            <div class="owner-notification-tabs" role="tablist" aria-label="Filter notifikasi">
                                <button class="active" type="button" data-owner-notification-tab="all">Semua <span>3</span></button>
                                <button type="button" data-owner-notification-tab="booking">Booking</button>
                                <button type="button" data-owner-notification-tab="payment">Pembayaran</button>
                                <button type="button" data-owner-notification-tab="system">Sistem</button>
                            </div>

                            <div class="owner-notification-list">
                                <article class="owner-notification-item" data-owner-notification-item data-category="booking">
                                    <span class="owner-notification-item-icon blue"><i class="fa-regular fa-calendar-days"></i></span>
                                    <div>
                                        <h3>Booking Baru <i></i></h3>
                                        <p>Booking baru untuk 22 Mei 2024, 20:00 - 21:00 oleh Budi Santoso.</p>
                                    </div>
                                    <time>5 menit yang lalu</time>
                                </article>

                                <article class="owner-notification-item" data-owner-notification-item data-category="payment">
                                    <span class="owner-notification-item-icon green"><i class="fa-solid fa-money-bill-wave"></i></span>
                                    <div>
                                        <h3>Pembayaran Diterima <i></i></h3>
                                        <p>Pembayaran sebesar Rp 80.000 untuk booking BK-240522-001 telah diterima.</p>
                                    </div>
                                    <time>12 menit yang lalu</time>
                                </article>

                                <article class="owner-notification-item" data-owner-notification-item data-category="system">
                                    <span class="owner-notification-item-icon purple"><i class="fa-regular fa-star"></i></span>
                                    <div>
                                        <h3>Ulasan Baru <i></i></h3>
                                        <p>Anda mendapatkan ulasan baru dengan rating 5 bintang dari Rizky Maulana.</p>
                                    </div>
                                    <time>1 jam yang lalu</time>
                                </article>

                                <article class="owner-notification-item" data-owner-notification-item data-category="booking">
                                    <span class="owner-notification-item-icon gold"><i class="fa-regular fa-clock"></i></span>
                                    <div>
                                        <h3>Booking Menunggu</h3>
                                        <p>Terdapat 3 booking yang masih menunggu konfirmasi.</p>
                                    </div>
                                    <time>2 jam yang lalu</time>
                                </article>

                                <article class="owner-notification-item" data-owner-notification-item data-category="system">
                                    <span class="owner-notification-item-icon blue"><i class="fa-solid fa-info"></i></span>
                                    <div>
                                        <h3>Laporan Bulanan Tersedia</h3>
                                        <p>Laporan pendapatan bulan Mei 2024 sudah tersedia. Klik untuk melihat laporan.</p>
                                    </div>
                                    <time>5 jam yang lalu</time>
                                </article>
                            </div>

                            <button class="owner-notification-read" type="button" data-owner-notification-read>Tandai Semua sebagai Dibaca</button>
                            <button class="owner-notification-all" type="button" data-owner-notification-all>Lihat Semua Notifikasi</button>
                        </section>
                    </div>

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
    <script>
        (function () {
            var root = document.querySelector('[data-owner-notification]');

            if (!root) {
                return;
            }

            var toggle = root.querySelector('[data-owner-notification-toggle]');
            var panel = root.querySelector('[data-owner-notification-panel]');
            var close = root.querySelector('[data-owner-notification-close]');
            var readButton = root.querySelector('[data-owner-notification-read]');
            var allButton = root.querySelector('[data-owner-notification-all]');
            var count = root.querySelector('[data-owner-notification-count]');
            var tabCount = root.querySelector('[data-owner-notification-tab="all"] span');
            var list = root.querySelector('.owner-notification-list');
            var tabs = root.querySelectorAll('[data-owner-notification-tab]');
            var items = root.querySelectorAll('[data-owner-notification-item]');
            var readStorageKey = 'sportArenaOwnerNotificationsRead';

            if (window.location.hash === '#ownerNotificationPanel' && window.history && window.history.replaceState) {
                window.history.replaceState(null, document.title, window.location.pathname + window.location.search);
            }

            function setOpen(open) {
                panel.hidden = !open;
                root.classList.toggle('is-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            }

            function setUnreadCount(total) {
                var hasUnread = total > 0;

                root.classList.toggle('has-unread', hasUnread);

                if (count) {
                    count.textContent = hasUnread ? String(total) : '';
                    count.hidden = !hasUnread;
                }

                if (tabCount) {
                    tabCount.textContent = hasUnread ? String(total) : '';
                    tabCount.hidden = !hasUnread;
                }
            }

            function markAllNotificationsRead() {
                root.querySelectorAll('.owner-notification-item h3 i').forEach(function (dot) {
                    dot.remove();
                });

                try {
                    window.localStorage.setItem(readStorageKey, '1');
                } catch (error) {
                    // localStorage can be unavailable in private or restricted browser modes.
                }

                setUnreadCount(0);
            }

            function filterNotifications(category) {
                tabs.forEach(function (tab) {
                    tab.classList.toggle('active', tab.dataset.ownerNotificationTab === category);
                });

                items.forEach(function (item) {
                    item.hidden = category !== 'all' && item.dataset.category !== category;
                });

                if (list) {
                    list.scrollTop = 0;
                }
            }

            toggle.addEventListener('click', function () {
                setOpen(panel.hidden);
            });

            close.addEventListener('click', function () {
                setOpen(false);
                toggle.focus();
            });

            try {
                if (window.localStorage.getItem(readStorageKey) === '1') {
                    markAllNotificationsRead();
                } else {
                    setUnreadCount(3);
                }
            } catch (error) {
                setUnreadCount(3);
            }

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    filterNotifications(tab.dataset.ownerNotificationTab);
                });
            });

            if (allButton) {
                allButton.addEventListener('click', function () {
                    filterNotifications('all');

                    if (list) {
                        list.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            }

            readButton.addEventListener('click', function () {
                markAllNotificationsRead();
                setOpen(false);
            });

            document.addEventListener('click', function (event) {
                if (!panel.hidden && !root.contains(event.target)) {
                    setOpen(false);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !panel.hidden) {
                    setOpen(false);
                    toggle.focus();
                }
            });

        })();
    </script>
</body>
</html>
