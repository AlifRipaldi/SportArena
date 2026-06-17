<?php
$linePoints = array();

foreach ($monthlyRevenue as $point) {
    $linePoints[] = $point['x'] . ',' . $point['y'];
}

$linePoints = implode(' ', $linePoints);
?>

<section class="owner-dashboard-hero">
    <div>
        <p>Selamat datang kembali, <strong><?php echo e($userName); ?></strong></p>
        <h1>Kelola lapangan dan pantau pendapatan Anda</h1>
    </div>
</section>

<section class="admin-stat-grid owner-stat-grid" aria-label="Ringkasan dashboard pemilik">
    <?php foreach ($summaryCards as $card): ?>
        <article class="admin-stat-card owner-stat-card">
            <div class="admin-stat-icon <?php echo e($card['accent']); ?>">
                <i class="fa-solid <?php echo e($card['icon']); ?>"></i>
            </div>
            <div class="admin-stat-details">
                <p><?php echo e($card['label']); ?></p>
                <strong><?php echo e($card['value']); ?></strong>
                <?php if ($card['note'] !== ''): ?>
                    <small><i class="fa-solid fa-arrow-up"></i> <?php echo e($card['trend']); ?> <span><?php echo e($card['note']); ?></span></small>
                <?php else: ?>
                    <small class="owner-stat-muted"><?php echo e($card['trend']); ?></small>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<section class="admin-dashboard-grid owner-dashboard-grid">
    <article class="admin-panel owner-revenue-panel" id="pendapatan">
        <div class="admin-panel-header">
            <h2>Pendapatan Bulanan</h2>
            <button type="button">Tahun Ini <i class="fa-solid fa-chevron-down"></i></button>
        </div>

        <div class="admin-line-chart owner-line-chart">
            <div class="admin-chart-y">
                <span>Rp10jt</span>
                <span>Rp8jt</span>
                <span>Rp6jt</span>
                <span>Rp4jt</span>
                <span>Rp0</span>
            </div>
            <div class="admin-chart-stage">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polygon points="0,100 <?php echo e($linePoints); ?> 100,100" class="admin-chart-fill"></polygon>
                    <polyline points="<?php echo e($linePoints); ?>" class="admin-chart-line"></polyline>
                </svg>

                <?php foreach ($monthlyRevenue as $point): ?>
                    <span class="admin-chart-dot" title="<?php echo e($point['month'] . ' - ' . $point['amount']); ?>" style="left: <?php echo e($point['x']); ?>%; top: <?php echo e($point['y']); ?>%;"></span>
                <?php endforeach; ?>

                <div class="admin-chart-months">
                    <?php foreach ($monthlyRevenue as $point): ?>
                        <span><?php echo e($point['month']); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="owner-chart-legend">
            <span class="admin-status-dot lime"></span>
            <span>Pendapatan (Rp)</span>
        </div>
    </article>

    <article class="admin-panel owner-status-panel" id="statistik">
        <div class="admin-panel-header">
            <h2>Status Booking</h2>
        </div>

        <div class="admin-booking-status owner-booking-status">
            <div class="admin-donut owner-donut">
                <span>Total</span>
                <strong>120</strong>
                <small>Booking</small>
            </div>

            <div class="admin-status-list">
                <?php foreach ($bookingStatus as $status): ?>
                    <div class="admin-status-item">
                        <span class="admin-status-dot <?php echo e($status['color']); ?>"></span>
                        <strong><?php echo e($status['label']); ?></strong>
                        <em><?php echo e($status['value']); ?> (<?php echo e($status['count']); ?>)</em>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </article>
</section>

<section class="owner-bottom-grid">
    <article class="admin-panel owner-booking-panel">
        <div class="admin-panel-header">
            <h2>Booking Terbaru</h2>
            <a href="<?php echo e(app_url('pemilik/jadwal')); ?>">Lihat Semua</a>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table owner-booking-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td>
                                <div class="admin-customer">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($booking['user']); ?>&background=20314a&color=ffffff" alt="">
                                    <span><?php echo e($booking['user']); ?></span>
                                </div>
                            </td>
                            <td><?php echo e($booking['field']); ?></td>
                            <td><?php echo e($booking['date']); ?></td>
                            <td><?php echo e($booking['time']); ?></td>
                            <td><span class="admin-badge <?php echo e($booking['statusClass']); ?>"><?php echo e($booking['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="admin-panel owner-fields-panel">
        <div class="admin-panel-header">
            <h2>Lapangan Saya</h2>
            <a href="<?php echo e(app_url('pemilik/lapangan')); ?>">Lihat Semua</a>
        </div>

        <div class="owner-field-cards">
            <?php foreach ($ownerFields as $field): ?>
                <article class="owner-field-card">
                    <div class="owner-field-visual <?php echo e($field['visual']); ?>">
                        <span class="admin-badge success"><?php echo e($field['status']); ?></span>
                        <button class="btn-icon owner-field-like" type="button" aria-label="Favorit">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>
                    <div class="owner-field-body">
                        <h3><?php echo e($field['name']); ?></h3>
                        <p><i class="fa-solid fa-location-dot"></i> <?php echo e($field['location']); ?></p>
                        <p class="owner-rating"><i class="fa-solid fa-star"></i> <?php echo e($field['rating']); ?> <span>(<?php echo e($field['reviews']); ?> ulasan)</span></p>
                        <strong><?php echo e($field['price']); ?> <span>/jam</span></strong>
                        <div class="owner-field-actions">
                            <a class="admin-secondary-btn" href="<?php echo e(app_url('pemilik/lapangan')); ?>"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a class="admin-secondary-btn primary" href="<?php echo e(app_url('pemilik/lapangan')); ?>"><i class="fa-solid fa-eye"></i> Detail</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="admin-panel owner-review-panel" id="ulasan">
        <div class="admin-panel-header">
            <h2>Ulasan Terbaru</h2>
            <a href="<?php echo e(app_url('pemilik/ulasan')); ?>">Lihat Semua</a>
        </div>

        <div class="owner-review-list">
            <?php foreach ($latestReviews as $review): ?>
                <article class="owner-review-item">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($review['name']); ?>&background=20314a&color=ffffff" alt="">
                    <div>
                        <div class="owner-review-head">
                            <strong><?php echo e($review['name']); ?></strong>
                            <small><?php echo e($review['time']); ?></small>
                        </div>
                        <div class="owner-review-stars" aria-label="<?php echo e($review['rating']); ?> dari 5">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?php echo $i <= $review['rating'] ? 'fa-solid' : 'fa-regular'; ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p><?php echo e($review['text']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </article>
</section>

<span class="owner-anchor" id="profil"></span>
<span class="owner-anchor" id="pengaturan"></span>
