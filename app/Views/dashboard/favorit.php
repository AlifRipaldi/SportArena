<div class="dashboard-shell profile-dashboard favorite-dashboard">
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
            <a href="#favorite-list">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main favorite-main">
        <section class="profile-page-head favorite-page-head">
            <div>
                <h1><?php echo e($pageHeading); ?></h1>
                <p><?php echo e($pageSubheading); ?></p>
            </div>
            <div class="profile-head-actions">
                <button type="button" class="profile-notification" aria-label="Notifikasi">
                    <span>&#128276;</span>
                    <sup>1</sup>
                </button>
                <div class="profile-account-menu">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=120&auto=format&fit=crop" alt="Foto profil">
                    <span>&#8964;</span>
                </div>
            </div>
        </section>

        <section class="favorite-toolbar" aria-label="Filter favorit">
            <nav class="favorite-filter-tabs" aria-label="Kategori favorit">
                <a href="#" class="active">Semua</a>
                <a href="#">Futsal</a>
                <a href="#">Badminton</a>
                <a href="#">Mini Soccer</a>
                <a href="#">Basketball</a>
            </nav>
            <label class="favorite-sort-control">
                <span>Urutkan:</span>
                <select aria-label="Urutkan favorit">
                    <option>Terbaru Ditambahkan</option>
                    <option>Rating Tertinggi</option>
                    <option>Harga Terendah</option>
                    <option>Jarak Terdekat</option>
                </select>
            </label>
        </section>

        <section class="favorite-summary-panel">
            <span class="favorite-summary-icon">&#9825;</span>
            <div>
                <h2><?php echo e(count($favorites)); ?> Lapangan Favorit</h2>
                <p>Temukan dan booking lapangan favoritmu kapan saja dengan mudah.</p>
            </div>
            <form method="post" action="<?php echo e(app_url('dashboard/favorit/hapus-semua')); ?>">
                <button type="submit" class="favorite-clear-button"><span>&#128465;</span>Hapus Semua</button>
            </form>
        </section>

        <section id="favorite-list" class="favorite-match-list" aria-label="Daftar lapangan favorit">
            <?php foreach ($favorites as $favorite): ?>
                <article class="favorite-match-card">
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
                            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>" class="favorite-detail-button">Lihat Detail</a>
                            <a href="<?php echo e(app_url('dashboard/booking')); ?>" class="favorite-book-button">Booking Lagi</a>
                        </div>
                    </div>

                    <div class="favorite-card-actions">
                        <form method="post" action="<?php echo e(app_url('dashboard/favorit/toggle')); ?>">
                            <input type="hidden" name="id_lapangan" value="<?php echo e($favorite['id']); ?>">
                            <input type="hidden" name="return_to" value="favorit">
                            <button type="submit" class="favorite-heart-button" aria-label="Hapus favorit <?php echo e($favorite['venue']); ?>">&#10084;</button>
                        </form>
                        <button type="button" class="favorite-menu-button" aria-label="Menu <?php echo e($favorite['venue']); ?>">&#8942;</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="favorite-footnote">
            <p><span>&#9825;</span>Kamu bisa menambahkan lapangan ke favorit dari halaman detail lapangan.</p>
        </section>
    </main>
</div>
