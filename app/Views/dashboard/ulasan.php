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
            <a href="<?php echo e(app_url('dashboard/riwayat')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'riwayat' ? 'active' : ''; ?>"><span>&#9201;</span>Riwayat</a>
            <a href="<?php echo e(app_url('dashboard/ulasan')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'ulasan' ? 'active' : ''; ?>"><span>&#9734;</span>Ulasan Saya</a>
            <a href="<?php echo e(app_url('dashboard/profil')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'profil' ? 'active' : ''; ?>"><span>&#9786;</span>Profil</a>
            <a href="<?php echo e(app_url('settings')); ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo">
            <p>Ulasan Anda penting untuk komunitas.</p>
            <small>Bantu pengguna lain menemukan lapangan terbaik.</small>
            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Cari Lapangan &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main">
        <section class="dashboard-topbar">
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

        <section class="dashboard-section review-tabs">
            <div class="review-tabs-inner">
                <button class="tab active">Semua Ulasan</button>
                <button class="tab">Ulasan Tertinggi</button>
            </div>
        </section>

        <section class="dashboard-section review-summary-grid">
            <article class="stat-card green">
                <div class="stat-icon">&#9733;</div>
                <strong><?php echo e($reviewSummary['average']); ?></strong>
                <p>Rata-rata Ulasan Saya</p>
                <a href="#">Lihat detail &#8594;</a>
            </article>
            <article class="stat-card blue">
                <div class="stat-icon">&#128221;</div>
                <strong><?php echo e($reviewSummary['total']); ?></strong>
                <p>Total Ulasan</p>
                <a href="#">Dari semua booking</a>
            </article>
            <article class="stat-card green">
                <div class="stat-icon">&#128077;</div>
                <strong><?php echo e($reviewSummary['positive']); ?></strong>
                <p>Ulasan Positif</p>
                <a href="#"><?php echo e($reviewSummary['positivePercent']); ?> dari semua ulasan</a>
            </article>
            <article class="stat-card orange">
                <div class="stat-icon">&#128078;</div>
                <strong><?php echo e($reviewSummary['negative']); ?></strong>
                <p>Ulasan Negatif</p>
                <a href="#"><?php echo e($reviewSummary['negativePercent']); ?> dari semua ulasan</a>
            </article>
        </section>

        <section class="dashboard-section reviews-list">
            <?php foreach ($reviews as $review): ?>
                <article class="review-card">
                    <div class="review-media" style="background-image: url('<?php echo e($review['image']); ?>');">
                        <span class="tag"><?php echo e($review['type']); ?></span>
                    </div>
                    <div class="review-body">
                        <div class="review-header">
                            <div>
                                <h3><?php echo e($review['venue']); ?></h3>
                                <p><?php echo e($review['location']); ?></p>
                            </div>
                            <a href="#" class="btn-detail">Lihat Detail</a>
                        </div>
                        <div class="review-rating">
                            <span class="rating-stars"><?php echo str_repeat('&#9733;', floor($review['rating'])); ?></span>
                            <strong><?php echo e(number_format($review['rating'], 1)); ?></strong>
                            <span>(<?php echo e($review['reviews']); ?> ulasan)</span>
                        </div>
                        <p class="review-text"><?php echo e($review['comment']); ?></p>
                        <div class="review-meta-row">
                            <div><span>&#128197;</span> <?php echo e($review['date']); ?></div>
                            <div><span>&#128188;</span> Kode Booking <?php echo e($review['code']); ?></div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="dashboard-section review-note">
            <p>Ulasanmu membantu pengguna lain dalam memilih lapangan terbaik.</p>
        </section>
    </main>
</div>

<style>
.review-tabs {
    padding: 0 0 24px;
}
.review-tabs-inner {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.review-tabs-inner .tab {
    padding: 10px 22px;
    border-radius: 999px;
    border: 1px solid rgba(123,229,125,0.22);
    background: transparent;
    color: #cbd8e7;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s ease;
}
.review-tabs-inner .tab.active,
.review-tabs-inner .tab:hover {
    background: var(--primary-color);
    color: #07121f;
    border-color: var(--primary-color);
}
.review-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(180px, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}
.reviews-list {
    display: grid;
    gap: 18px;
}
.review-card {
    display: grid;
    grid-template-columns: 280px minmax(0, 1fr);
    gap: 20px;
    background: rgba(16, 28, 48, 0.88);
    border: 1px solid rgba(123,229,125,0.14);
    border-radius: 18px;
    overflow: hidden;
}
.review-media {
    min-height: 232px;
    background-size: cover;
    background-position: center;
    position: relative;
}
.review-media .tag {
    position: absolute;
    top: 16px;
    left: 16px;
    background: rgba(46,204,113,0.9);
    color: white;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}
.review-body {
    padding: 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.review-header {
    display: flex;
    justify-content: space-between;
    gap: 18px;
    align-items: flex-start;
}
.review-header h3 {
    margin-bottom: 6px;
    color: #ffffff;
    font-size: 18px;
}
.review-header p {
    color: #9bb1cf;
    font-size: 13px;
}
.btn-detail {
    align-self: start;
    padding: 10px 18px;
    background: rgba(123,229,125,0.16);
    border: 1px solid rgba(123,229,125,0.32);
    border-radius: 12px;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 700;
}
.review-rating {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #f5f9ff;
    font-size: 14px;
}
.review-rating .rating-stars {
    color: #f6c259;
    letter-spacing: 1px;
}
.review-text {
    color: #cfd8e8;
    line-height: 1.7;
}
.review-meta-row {
    display: flex;
    justify-content: space-between;
    gap: 18px;
    color: rgba(189, 210, 232, 0.8);
    font-size: 13px;
}
.review-meta-row span {
    margin-right: 8px;
}
.review-note {
    padding: 18px 22px;
    border-radius: 16px;
    background: rgba(30, 48, 77, 0.75);
    border: 1px solid rgba(123,229,125,0.12);
    color: #a8bfcd;
}
@media (max-width: 1080px) {
    .review-summary-grid {
        grid-template-columns: repeat(2, minmax(180px, 1fr));
    }
}
@media (max-width: 820px) {
    .dashboard-shell {
        grid-template-columns: 1fr;
    }
    .review-card {
        grid-template-columns: 1fr;
    }
    .review-media {
        min-height: 200px;
    }
}
@media (max-width: 600px) {
    .review-summary-grid {
        grid-template-columns: 1fr;
    }
    .review-header {
        flex-direction: column;
        align-items: stretch;
    }
    .review-meta-row {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
