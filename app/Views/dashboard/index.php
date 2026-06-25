<div class="dashboard-shell profile-dashboard home-dashboard">
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
            <a href="#lapangan-populer">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main home-main">
        <?php if (!empty($bookingMessage)): ?><section class="settings-alert success-message" role="status"><?php echo e($bookingMessage); ?></section><?php endif; ?>
        <?php if (!empty($bookingError)): ?><section class="settings-alert error-message" role="alert"><?php echo e($bookingError); ?></section><?php endif; ?>
        <section class="home-topbar">
            <div>
                <p>Selamat datang kembali,</p>
                <h1><?php echo e($userName); ?> <span>&#128075;</span></h1>
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

        <form class="home-search-row" method="get" action="<?php echo e(app_url('dashboard/lapangan')); ?>" aria-label="Pencarian lapangan">
            <label class="home-search-box">
                <span aria-hidden="true">&#128269;</span>
                <input type="search" name="q" placeholder="Cari lapangan, lokasi, atau jenis olahraga..." aria-label="Cari lapangan, lokasi, atau jenis olahraga">
            </label>
            <button type="submit" class="home-filter-button"><span aria-hidden="true">&#128269;</span>Cari</button>
        </form>

        <section class="home-stat-grid" aria-label="Ringkasan dashboard">
            <?php foreach ($stats as $stat): ?>
                <?php
                    $statUrl = app_url('dashboard/booking');
                    if ($stat['label'] === 'Selesai') { $statUrl = app_url('dashboard/riwayat'); }
                    if ($stat['label'] === 'Favorit') { $statUrl = app_url('dashboard/favorit'); }
                    if ($stat['label'] === 'Rating Anda') { $statUrl = app_url('dashboard/ulasan'); }
                ?>
                <article class="home-stat-card <?php echo e($stat['accent']); ?>">
                    <span class="home-stat-icon"><?php echo $stat['icon']; ?></span>
                    <strong><?php echo e($stat['value']); ?></strong>
                    <p><?php echo e($stat['label']); ?></p>
                    <a href="<?php echo e($statUrl); ?>">Lihat detail &#8594;</a>
                </article>
            <?php endforeach; ?>
        </section>

        <section id="lapangan-populer" class="home-section">
            <div class="home-section-head">
                <h2>Lapangan Populer</h2>
                <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Lihat semua &#8594;</a>
            </div>

            <div class="home-venue-grid">
                <?php foreach ($venues as $venue): ?>
                    <?php $venueDetailUrl = app_url('dashboard/lapangan/' . rawurlencode(isset($venue['id']) ? $venue['id'] : '')); ?>
                    <article
                        class="home-venue-card"
                        role="link"
                        tabindex="0"
                        data-venue-detail-url="<?php echo e($venueDetailUrl); ?>"
                        aria-label="Lihat detail <?php echo e($venue['name']); ?>"
                    >
                        <div class="home-venue-media">
                            <img src="<?php echo e($venue['image']); ?>" alt="<?php echo e($venue['name']); ?>">
                            <span>Populer</span>
                            <form method="post" action="<?php echo e(app_url('dashboard/favorit/toggle')); ?>">
                                <input type="hidden" name="id_lapangan" value="<?php echo e(isset($venue['id']) ? $venue['id'] : ''); ?>">
                                <input type="hidden" name="return_to" value="dashboard">
                                <input type="hidden" name="booking_token" value="<?php echo e(isset($bookingCsrfToken) ? $bookingCsrfToken : ''); ?>">
                                <button type="submit" aria-label="Tambah favorit">&#9825;</button>
                            </form>
                        </div>
                        <div class="home-venue-body">
                            <h3><?php echo e($venue['name']); ?></h3>
                            <p><span>&#9906;</span><?php echo e($venue['location']); ?></p>
                            <div class="home-venue-rating">
                                <span>&#9733;</span>
                                <?php echo e($venue['rating']); ?> (<?php echo e($venue['reviews']); ?>)
                            </div>
                            <div class="home-venue-footer">
                                <strong><?php echo e($venue['price']); ?> <small>/jam</small></strong>
                                <a class="home-venue-schedule" href="<?php echo e($venueDetailUrl); ?>">
                                    <i class="fa-regular fa-calendar"></i>
                                    <span>Lihat Jadwal</span>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (!empty($nextBooking)): ?>
            <section class="home-section">
                <div class="home-section-head">
                    <h2>Booking Terdekat</h2>
                    <a href="<?php echo e(app_url('dashboard/booking')); ?>">Lihat semua &#8594;</a>
                </div>

                <article class="home-next-booking">
                    <img src="<?php echo e($nextBooking['image']); ?>" alt="<?php echo e($nextBooking['venue']); ?>">
                    <div>
                        <h3><?php echo e($nextBooking['venue']); ?></h3>
                        <p><span aria-hidden="true">&#128197;</span><?php echo e($nextBooking['date']); ?></p>
                        <p><span aria-hidden="true">&#9201;</span><?php echo e($nextBooking['time']); ?></p>
                        <p><span aria-hidden="true">&#9711;</span><?php echo e($nextBooking['duration']); ?></p>
                    </div>
                    <div class="home-next-actions">
                        <span><?php echo e($nextBooking['status']); ?></span>
                        <a href="<?php echo e(app_url('dashboard/booking?booking=' . rawurlencode($nextBooking['code']))); ?>">Lihat Detail</a>
                    </div>
                </article>
            </section>
        <?php endif; ?>
    </main>
</div>

<script>
    (function () {
        document.querySelectorAll('[data-venue-detail-url]').forEach(function (card) {
            function openDetail() {
                if (card.dataset.venueDetailUrl) {
                    window.location.href = card.dataset.venueDetailUrl;
                }
            }

            card.addEventListener('click', function (event) {
                if (event.target.closest('a, button, form, input')) { return; }
                openDetail();
            });

            card.addEventListener('keydown', function (event) {
                if ((event.key === 'Enter' || event.key === ' ') && event.target === card) {
                    event.preventDefault();
                    openDetail();
                }
            });
        });
    }());
</script>
