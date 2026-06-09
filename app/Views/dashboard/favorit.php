<div class="dashboard-shell">
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
            <a href="#"><span>&#9201;</span>Riwayat</a>
            <a href="<?php echo e(app_url('dashboard/ulasan')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'ulasan' ? 'active' : ''; ?>"><span>&#9734;</span>Ulasan Saya</a>
            <a href="#"><span>&#9786;</span>Profil</a>
            <a href="<?php echo e(app_url('settings')); ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo">
            <p>Mainkan Game Terbaikmu</p>
            <small>Pesan lapangan favoritmu sekarang!</small>
            <a href="#favorite-list">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main">
        <section class="dashboard-topbar search-topbar">
            <div>
                <p><?php echo e($pageHeading); ?></p>
                <h1><?php echo e($pageSubheading); ?></h1>
            </div>
            <div class="dashboard-actions">
                <button type="button" class="icon-button" aria-label="Notifikasi">&#128276;</button>
                <div class="dashboard-user">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=120&auto=format&fit=crop" alt="Foto profil">
                    <span>&#8964;</span>
                </div>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="dashboard-section-heading">
                <h2>Filter Favorit</h2>
                <a href="#">Urutkan: Terbaru Ditambahkan</a>
            </div>

            <div class="favorites-tabs">
                <button class="active" type="button">Semua</button>
                <button type="button">Futsal</button>
                <button type="button">Badminton</button>
                <button type="button">Mini Soccer</button>
                <button type="button">Basketball</button>
            </div>
        </section>

        <section class="favorite-summary-card">
            <div>
                <h2>4 Lapangan Favorit</h2>
                <p>Temukan dan booking lapangan favoritmu kapan saja dengan mudah.</p>
            </div>
            <div class="favorite-summary-tag">Hapus Semua</div>
        </section>

        <section id="favorite-list" class="favorite-list">
            <?php foreach ($favorites as $favorite): ?>
                <article class="favorite-card">
                    <div class="favorite-card-image" style="background-image: url('<?php echo e($favorite['image']); ?>');">
                        <span class="favorite-label"><?php echo e($favorite['type']); ?></span>
                        <button type="button" class="favorite-heart" aria-label="Hapus favorit">&#10084;</button>
                    </div>
                    <div class="favorite-card-content">
                        <div class="favorite-meta">
                            <h3><?php echo e($favorite['venue']); ?></h3>
                            <p><?php echo e($favorite['location']); ?></p>
                        </div>

                        <div class="favorite-tags">
                            <?php foreach ($favorite['features'] as $feature): ?>
                                <span><?php echo e($feature); ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="favorite-rating">
                            <span>&#9733; <?php echo e($favorite['rating']); ?> (<?php echo e($favorite['reviews']); ?>)</span>
                            <span>&#128205; <?php echo e($favorite['distance']); ?></span>
                        </div>

                        <div class="favorite-action-row">
                            <strong><?php echo e($favorite['price']); ?> <small>/jam</small></strong>
                            <div class="favorite-buttons">
                                <a href="<?php echo e(app_url('dashboard/lapangan')); ?>" class="btn-secondary">Lihat Detail</a>
                                <a href="<?php echo e(app_url('dashboard/booking')); ?>" class="btn-primary small">Booking Lagi</a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</div>
