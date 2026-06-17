<div class="dashboard-shell profile-dashboard review-dashboard">
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

    <main class="dashboard-main profile-main review-main">
        <section class="profile-page-head review-page-head">
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

        <section class="review-toolbar" aria-label="Filter ulasan">
            <nav class="review-tabs" aria-label="Kategori ulasan">
                <a href="#" class="active">Semua Ulasan</a>
                <a href="#">Ulasan Tertinggi</a>
            </nav>
            <label class="review-sort">
                <span>Urutkan:</span>
                <select aria-label="Urutkan ulasan">
                    <option>Terbaru</option>
                    <option>Rating Tertinggi</option>
                    <option>Rating Terendah</option>
                </select>
            </label>
        </section>

        <section class="review-summary-panel" aria-label="Ringkasan ulasan">
            <article class="review-summary-card average">
                <span class="review-summary-icon">&#9734;</span>
                <div>
                    <p>Rata-rata Ulasan Saya</p>
                    <strong><?php echo e($reviewSummary['average']); ?></strong>
                    <span class="review-summary-stars">
                        &#9733;&#9733;&#9733;&#9733;<i>&#9733;</i>
                    </span>
                    <small><?php echo e($reviewSummary['total']); ?> ulasan</small>
                </div>
            </article>
            <article class="review-summary-card total">
                <span class="review-summary-icon">&#128197;</span>
                <div>
                    <p>Total Ulasan</p>
                    <strong><?php echo e($reviewSummary['total']); ?></strong>
                    <small>Dari semua booking</small>
                </div>
            </article>
            <article class="review-summary-card positive">
                <span class="review-summary-icon">&#128077;</span>
                <div>
                    <p>Ulasan Positif</p>
                    <strong><?php echo e($reviewSummary['positive']); ?></strong>
                    <small><?php echo e($reviewSummary['positivePercent']); ?> dari semua ulasan</small>
                </div>
            </article>
            <article class="review-summary-card negative">
                <span class="review-summary-icon">&#128078;</span>
                <div>
                    <p>Ulasan Negatif</p>
                    <strong><?php echo e($reviewSummary['negative']); ?></strong>
                    <small><?php echo e($reviewSummary['negativePercent']); ?> dari semua ulasan</small>
                </div>
            </article>
        </section>

        <section class="review-list" aria-label="Daftar ulasan">
            <?php foreach ($reviews as $review): ?>
                <?php
                    $rating = (float) $review['rating'];
                    $filledStars = (int) floor($rating);
                    $emptyStars = max(0, 5 - $filledStars);
                ?>
                <article class="review-match-card">
                    <div class="review-match-media">
                        <img src="<?php echo e($review['image']); ?>" alt="<?php echo e($review['venue']); ?>">
                        <span><?php echo e($review['type']); ?></span>
                    </div>
                    <div class="review-match-content">
                        <h2><?php echo e($review['venue']); ?></h2>
                        <p class="review-location"><span>&#9906;</span><?php echo e($review['location']); ?></p>
                        <div class="review-rating-row" aria-label="Rating <?php echo e(number_format($rating, 1)); ?>">
                            <span class="review-stars">
                                <?php echo str_repeat('&#9733;', $filledStars); ?><i><?php echo str_repeat('&#9733;', $emptyStars); ?></i>
                            </span>
                            <strong><?php echo e(number_format($rating, 1)); ?></strong>
                        </div>
                        <p class="review-comment"><?php echo e($review['comment']); ?></p>
                        <small>Diulas pada <?php echo e($review['date']); ?></small>
                    </div>
                    <div class="review-booking-meta">
                        <div>
                            <span>Tanggal Booking</span>
                            <strong><?php echo e($review['date']); ?></strong>
                        </div>
                        <div>
                            <span>Kode Booking</span>
                            <strong class="code"><?php echo e($review['code']); ?></strong>
                        </div>
                    </div>
                    <div class="review-card-actions">
                        <button type="button" aria-label="Menu ulasan">&#8943;</button>
                        <a href="#">Lihat Detail</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="review-footnote">
            <p><span>&#9432;</span> Ulasanmu membantu pengguna lain dalam memilih lapangan terbaik.</p>
        </section>
    </main>
</div>
