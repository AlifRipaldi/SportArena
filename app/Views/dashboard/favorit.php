<div class="dashboard-shell profile-dashboard favorite-dashboard">
    <aside class="dashboard-sidebar">
        <div class="dashboard-brand">
            <div class="dashboard-logo-mark">
                <img src="<?php echo e(app_asset('img/logo-mark.png')); ?>" alt="Arena Sport Logo">
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
            <a href="#favorite-list">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main favorite-main">
        <?php if (!empty($bookingMessage)): ?><section class="settings-alert success-message" role="status"><?php echo e($bookingMessage); ?></section><?php endif; ?>
        <?php if (!empty($bookingError)): ?><section class="settings-alert error-message" role="alert"><?php echo e($bookingError); ?></section><?php endif; ?>
        <section class="profile-page-head favorite-page-head">
            <div>
                <h1><?php echo e($pageHeading); ?></h1>
                <p><?php echo e($pageSubheading); ?></p>
            </div>
            <div class="profile-head-actions">
                <?php require __DIR__ . '/partials/customer_notifications.php'; ?>
                <a href="<?php echo e(app_url('dashboard/profil')); ?>" class="profile-account-menu" aria-label="Buka profil">
                    <img src="<?php echo e($userAvatar); ?>" alt="Foto profil">
                    <span>&#8964;</span>
                </a>
            </div>
        </section>

        <section class="favorite-toolbar" aria-label="Filter favorit">
            <nav class="favorite-filter-tabs" aria-label="Kategori favorit">
                <button type="button" class="active" data-favorite-filter="all">Semua</button>
                <?php foreach (array_values(array_unique(array_column($favorites, 'type'))) as $favoriteType): ?>
                    <button type="button" data-favorite-filter="<?php echo e(strtolower($favoriteType)); ?>"><?php echo e($favoriteType); ?></button>
                <?php endforeach; ?>
            </nav>
            <label class="favorite-sort-control">
                <span>Urutkan:</span>
                <select id="favoriteSortControl" aria-label="Urutkan favorit">
                    <option value="newest">Terbaru Ditambahkan</option>
                    <option value="rating">Rating Tertinggi</option>
                    <option value="price">Harga Terendah</option>
                </select>
            </label>
        </section>

        <section class="favorite-summary-panel">
            <span class="favorite-summary-icon">&#9825;</span>
            <div>
                <h2><?php echo e(count($favorites)); ?> Lapangan Favorit</h2>
                <p>Temukan dan booking lapangan favoritmu kapan saja dengan mudah.</p>
            </div>
            <?php if (!empty($favorites)): ?>
                <form method="post" action="<?php echo e(app_url('dashboard/favorit/hapus-semua')); ?>" onsubmit="return window.confirm('Hapus semua lapangan favorit?');">
                    <input type="hidden" name="booking_token" value="<?php echo e(isset($bookingCsrfToken) ? $bookingCsrfToken : ''); ?>">
                    <button type="submit" class="favorite-clear-button"><span>&#128465;</span>Hapus Semua</button>
                </form>
            <?php endif; ?>
        </section>

        <section id="favorite-list" class="favorite-match-list" aria-label="Daftar lapangan favorit">
            <?php foreach ($favorites as $favorite): ?>
                <article class="favorite-match-card" data-favorite-type="<?php echo e(strtolower($favorite['type'])); ?>" data-favorite-rating="<?php echo e($favorite['rating']); ?>" data-favorite-price="<?php echo e((int) preg_replace('/[^0-9]/', '', $favorite['price'])); ?>">
                    <div class="favorite-match-media">
                        <img src="<?php echo e($favorite['image']); ?>" alt="<?php echo e($favorite['venue']); ?>">
                        <span><?php echo e($favorite['type']); ?></span>
                    </div>

                    <div class="favorite-match-content">
                        <h2><?php echo e($favorite['venue']); ?></h2>
                        <p class="favorite-location"><span>&#9906;</span><?php echo e($favorite['location']); ?></p>
                        <div class="favorite-tags">
                            <?php foreach ($favorite['features'] as $feature): ?>
                                <span><?php echo e($feature); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="favorite-rating">
                            <span class="favorite-star">&#9733;</span>
                            <span><?php echo e($favorite['rating']); ?> (<?php echo e($favorite['reviews']); ?>)</span>
                            <i></i>
                            <span>&#9201; <?php echo e($favorite['distance']); ?></span>
                        </div>
                    </div>

                    <div class="favorite-price-panel">
                        <div>
                            <small>Harga Mulai Dari</small>
                            <strong><?php echo e($favorite['price']); ?> <span>/jam</span></strong>
                        </div>
                        <div class="favorite-match-buttons">
                            <a href="<?php echo e(app_url('dashboard/lapangan/' . rawurlencode($favorite['id']))); ?>" class="favorite-detail-button">Lihat Lapangan</a>
                            <a href="<?php echo e(app_url('dashboard/lapangan/' . rawurlencode($favorite['id']) . '#customerFieldBooking')); ?>" class="favorite-book-button">Pilih Jadwal</a>
                        </div>
                    </div>

                    <div class="favorite-card-actions">
                        <form method="post" action="<?php echo e(app_url('dashboard/favorit/toggle')); ?>">
                            <input type="hidden" name="id_lapangan" value="<?php echo e($favorite['id']); ?>">
                            <input type="hidden" name="return_to" value="favorit">
                            <input type="hidden" name="booking_token" value="<?php echo e(isset($bookingCsrfToken) ? $bookingCsrfToken : ''); ?>">
                            <button type="submit" class="favorite-heart-button" aria-label="Hapus favorit <?php echo e($favorite['venue']); ?>">&#10084;</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
            <article class="favorite-empty-state" id="favoriteEmptyState" hidden>
                <span>&#9825;</span>
                <strong>Tidak ada lapangan favorit pada kategori ini</strong>
                <p>Kamu bisa menambahkan lapangan ke favorit dari halaman detail lapangan.</p>
                <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Cari Lapangan</a>
            </article>
        </section>

        <section class="favorite-footnote">
            <p><span>&#9825;</span>Kamu bisa menambahkan lapangan ke favorit dari halaman detail lapangan.</p>
        </section>
    </main>
</div>

<script>
    (function () {
        var list = document.getElementById('favorite-list');
        var cards = list ? Array.prototype.slice.call(list.querySelectorAll('.favorite-match-card')) : [];
        var filters = Array.prototype.slice.call(document.querySelectorAll('[data-favorite-filter]'));
        var sort = document.getElementById('favoriteSortControl');
        var empty = document.getElementById('favoriteEmptyState');
        var activeFilter = 'all';

        if (!list || !sort) { return; }

        function applyFavoriteView() {
            cards.sort(function (a, b) {
                if (sort.value === 'rating') { return Number(b.dataset.favoriteRating) - Number(a.dataset.favoriteRating); }
                if (sort.value === 'price') { return Number(a.dataset.favoritePrice) - Number(b.dataset.favoritePrice); }
                return 0;
            });
            var visible = 0;
            cards.forEach(function (card) {
                list.insertBefore(card, empty);
                card.hidden = activeFilter !== 'all' && card.dataset.favoriteType !== activeFilter;
                if (!card.hidden) { visible++; }
            });
            empty.hidden = visible !== 0;
        }

        filters.forEach(function (button) {
            button.addEventListener('click', function () {
                activeFilter = button.dataset.favoriteFilter;
                filters.forEach(function (item) { item.classList.toggle('active', item === button); });
                applyFavoriteView();
            });
        });
        sort.addEventListener('change', applyFavoriteView);
        applyFavoriteView();
    }());
</script>
