<div class="dashboard-shell profile-dashboard history-dashboard">
    <aside class="dashboard-sidebar">
        <div class="dashboard-brand">
            <div class="dashboard-logo-mark">
                <img src="<?php echo e(app_asset('img/logo.png')); ?>" alt="Arena Sport Logo">
            </div>
            <div>
                <strong>Arena</strong>
                <span>Sport</span>
            </div>
        </div>

        <nav class="dashboard-menu" aria-label="Menu dashboard">
            <a href="<?php echo e(app_url('dashboard')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'dashboard' ? 'active' : ''; ?>"><span>&#8962;</span>Dashboard</a>
            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'lapangan' ? 'active' : ''; ?>"><span>&#128269;</span>Cari Lapangan</a>
            <a href="<?php echo e(app_url('dashboard/booking')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'booking' ? 'active' : ''; ?>"><span>&#128197;</span>Booking Saya</a>
            <a href="<?php echo e(app_url('dashboard/favorit')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'favorit' ? 'active' : ''; ?>"><span>&#9825;</span>Favorit</a>
            <a href="<?php echo e(app_url('dashboard/riwayat')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'riwayat' ? 'active' : ''; ?>"><span>&#9201;</span>Riwayat</a>
            <a href="<?php echo e(app_url('dashboard/ulasan')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'ulasan' ? 'active' : ''; ?>"><span>&#9734;</span>Ulasan Saya</a>
            <a href="<?php echo e(app_url('dashboard/profil')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'profil' ? 'active' : ''; ?>"><span>&#9786;</span>Profil</a>
            <a href="<?php echo e(app_url('settings')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'settings' ? 'active' : ''; ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo profile-promo">
            <p>Mainkan Game Terbaikmu</p>
            <small>Pesan lapangan favoritmu sekarang!</small>
            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main history-main">
        <section class="profile-page-head history-page-head">
            <div>
                <h1><?php echo e($pageHeading); ?></h1>
                <p><?php echo e($pageSubheading); ?></p>
            </div>
            <div class="profile-head-actions">
                <a href="<?php echo e(app_url('dashboard/booking')); ?>" class="profile-notification" aria-label="Lihat notifikasi booking">
                    <span>&#128276;</span>
                    <sup>1</sup>
                </a>
                <a href="<?php echo e(app_url('dashboard/profil')); ?>" class="profile-account-menu" aria-label="Buka profil">
                    <img src="<?php echo e($userAvatar); ?>" alt="Foto profil">
                    <span>&#8964;</span>
                </a>
            </div>
        </section>

        <section class="history-toolbar" aria-label="Filter riwayat">
            <nav class="history-filter-tabs" aria-label="Status riwayat">
                <button type="button" class="active" data-history-filter="all">Semua</button>
                <button type="button" data-history-filter="completed">Selesai</button>
                <button type="button" data-history-filter="cancelled">Dibatalkan</button>
            </nav>
            <label class="history-sort-control">
                <span>Urutkan:</span>
                <select id="historySortControl" aria-label="Urutkan riwayat">
                    <option value="newest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="price-high">Pembayaran Tertinggi</option>
                    <option value="price-low">Pembayaran Terendah</option>
                </select>
            </label>
        </section>

        <section class="history-info-banner">
            <span>&#128197;</span>
            <p>Berikut adalah riwayat semua booking lapangan kamu.</p>
        </section>

        <section class="history-match-list" id="booking-list" aria-label="Daftar riwayat booking">
            <?php foreach ($bookings as $booking): ?>
                <?php
                    $status = strtolower(isset($booking['status']) ? $booking['status'] : 'selesai');
                    $isCanceled = $status === 'dibatalkan';
                    $statusLabel = $isCanceled ? 'Dibatalkan' : 'Selesai';
                    $statusClass = $isCanceled ? 'canceled' : 'completed';
                ?>
                <article class="history-match-card" data-history-status="<?php echo $isCanceled ? 'cancelled' : 'completed'; ?>" data-history-date="<?php echo e(isset($booking['dateValue']) ? $booking['dateValue'] : ''); ?>" data-history-price="<?php echo e((int) preg_replace('/[^0-9]/', '', $booking['price'])); ?>">
                    <div class="history-match-media">
                        <img src="<?php echo e($booking['image']); ?>" alt="<?php echo e($booking['venue']); ?>">
                        <span><?php echo e($booking['type']); ?></span>
                    </div>

                    <div class="history-match-content">
                        <h2><?php echo e($booking['venue']); ?></h2>
                        <p class="history-match-location"><span>&#9906;</span><?php echo e($booking['location']); ?></p>
                        <div class="history-match-meta">
                            <span>&#128197; <?php echo e($booking['date']); ?></span>
                            <span>&#9201; <?php echo e($booking['time']); ?></span>
                            <span>&#9711; <?php echo e($booking['duration']); ?></span>
                        </div>
                        <div class="history-code-row">
                            <small>Kode Booking</small>
                            <strong><?php echo e($booking['code']); ?></strong>
                            <button type="button" data-history-copy="<?php echo e($booking['code']); ?>" aria-label="Salin kode booking">&#128203;</button>
                        </div>
                    </div>

                    <div class="history-payment-panel">
                        <span class="history-status-pill <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span>
                        <div>
                            <small>Total Pembayaran</small>
                            <strong class="<?php echo e($statusClass); ?>"><?php echo e($booking['price']); ?></strong>
                        </div>
                        <a href="<?php echo e(app_url('dashboard/booking?booking=' . rawurlencode($booking['code']))); ?>">Lihat Detail</a>
                    </div>

                    <a class="history-row-arrow" href="<?php echo e(app_url('dashboard/booking?booking=' . rawurlencode($booking['code']))); ?>" aria-label="Lihat detail <?php echo e($booking['venue']); ?>">&#8250;</a>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="history-empty-note">
            <p id="historyEmptyState" hidden>Tidak ada riwayat pada filter ini.</p>
            <p>Ingin bermain lagi? <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Cari lapangan</a>.</p>
        </section>
    </main>
</div>

<script>
    (function () {
        var list = document.getElementById('booking-list');
        var cards = list ? Array.prototype.slice.call(list.querySelectorAll('.history-match-card')) : [];
        var filters = Array.prototype.slice.call(document.querySelectorAll('[data-history-filter]'));
        var sort = document.getElementById('historySortControl');
        var empty = document.getElementById('historyEmptyState');
        var activeFilter = 'all';

        if (!list || !sort) { return; }
        function applyHistoryView() {
            cards.sort(function (a, b) {
                if (sort.value === 'oldest') { return a.dataset.historyDate.localeCompare(b.dataset.historyDate); }
                if (sort.value === 'price-high') { return Number(b.dataset.historyPrice) - Number(a.dataset.historyPrice); }
                if (sort.value === 'price-low') { return Number(a.dataset.historyPrice) - Number(b.dataset.historyPrice); }
                return b.dataset.historyDate.localeCompare(a.dataset.historyDate);
            });
            var visible = 0;
            cards.forEach(function (card) {
                list.appendChild(card);
                card.hidden = activeFilter !== 'all' && card.dataset.historyStatus !== activeFilter;
                if (!card.hidden) { visible++; }
            });
            empty.hidden = visible !== 0;
        }
        filters.forEach(function (button) {
            button.addEventListener('click', function () {
                activeFilter = button.dataset.historyFilter;
                filters.forEach(function (item) { item.classList.toggle('active', item === button); });
                applyHistoryView();
            });
        });
        sort.addEventListener('change', applyHistoryView);
        list.addEventListener('click', function (event) {
            var button = event.target.closest('[data-history-copy]');
            if (!button) { return; }
            var value = button.dataset.historyCopy;
            if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(value); return; }
            var input = document.createElement('textarea');
            input.value = value; document.body.appendChild(input); input.select(); document.execCommand('copy'); input.remove();
        });
        applyHistoryView();
    }());
</script>
