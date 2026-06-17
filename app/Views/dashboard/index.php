<div class="dashboard-shell profile-dashboard home-dashboard">
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
            <a href="#lapangan-populer">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main home-main">
        <section class="home-topbar">
            <div>
                <p>Selamat datang kembali,</p>
                <h1><?php echo e($userName); ?> <span>&#128075;</span></h1>
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

        <section class="home-search-row" aria-label="Pencarian lapangan">
            <label class="home-search-box">
                <span aria-hidden="true">&#128269;</span>
                <input type="text" placeholder="Cari lapangan, lokasi, atau jenis olahraga..." aria-label="Cari lapangan, lokasi, atau jenis olahraga">
            </label>
            <button type="button" class="home-filter-button"><span aria-hidden="true">&#9776;</span>Filter</button>
        </section>

        <section class="home-stat-grid" aria-label="Ringkasan dashboard">
            <?php foreach ($stats as $stat): ?>
                <article class="home-stat-card <?php echo e($stat['accent']); ?>">
                    <span class="home-stat-icon"><?php echo $stat['icon']; ?></span>
                    <strong><?php echo e($stat['value']); ?></strong>
                    <p><?php echo e($stat['label']); ?></p>
                    <a href="#">Lihat detail &#8594;</a>
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
                    <article class="home-venue-card">
                        <div class="home-venue-media">
                            <img src="<?php echo e($venue['image']); ?>" alt="<?php echo e($venue['name']); ?>">
                            <span>Populer</span>
                            <button type="button" aria-label="Tambah favorit">&#9825;</button>
                        </div>
                        <div class="home-venue-body">
                            <h3><?php echo e($venue['name']); ?></h3>
                            <p><span>&#9906;</span><?php echo e($venue['location']); ?></p>
                            <div class="home-venue-rating">
                                <span>&#9733;</span>
                                <?php echo e($venue['rating']); ?> (<?php echo e($venue['reviews']); ?>)
                            </div>
                            <strong><?php echo e($venue['price']); ?> <small>/jam</small></strong>
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
                        <a href="<?php echo e(app_url('dashboard/booking')); ?>">Lihat Detail</a>
                    </div>
                </article>
            </section>
        <?php endif; ?>
    </main>
</div>
