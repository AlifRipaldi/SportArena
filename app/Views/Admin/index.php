<?php
$linePoints = array();

foreach ($monthlyRevenue as $point) {
    $linePoints[] = $point['x'] . ',' . $point['y'];
}

$linePoints = implode(' ', $linePoints);
?>

<section class="admin-hero">
    <div>
        <h1>Dashboard Admin</h1>
        <p>Selamat datang kembali, <?php echo e($userName); ?>!</p>
    </div>
</section>

<section class="admin-stat-grid">
    <?php foreach ($summaryCards as $card): ?>
        <article class="admin-stat-card">
            <div class="admin-stat-icon <?php echo e($card['accent']); ?>">
                <i class="fa-solid <?php echo e($card['icon']); ?>"></i>
            </div>
            <div class="admin-stat-details">
                <p><?php echo e($card['label']); ?></p>
                <strong><?php echo e($card['value']); ?></strong>
                <small><i class="fa-solid fa-arrow-up"></i> <?php echo e($card['trend']); ?> <span><?php echo e($card['note']); ?></span></small>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<section class="admin-dashboard-grid">
    <article class="admin-panel admin-revenue-panel">
        <div class="admin-panel-header">
            <h2>Pendapatan Bulanan</h2>
            <button type="button">Tahun Ini <i class="fa-solid fa-chevron-down"></i></button>
        </div>

        <div class="admin-line-chart">
            <div class="admin-chart-y">
                <span>Rp20jt</span>
                <span>Rp15jt</span>
                <span>Rp10jt</span>
                <span>Rp5jt</span>
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
    </article>

    <article class="admin-panel admin-status-panel">
        <div class="admin-panel-header">
            <h2>Status Booking</h2>
        </div>

        <div class="admin-booking-status">
            <div class="admin-donut">
                <span>Total</span>
                <strong>520</strong>
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

<section class="admin-dashboard-grid secondary">
    <article class="admin-panel admin-booking-table-panel">
        <div class="admin-panel-header">
            <h2>Booking Terbaru</h2>
            <a href="<?php echo e(app_url('admin/booking')); ?>">Lihat Semua</a>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Customer</th>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><?php echo e($booking['code']); ?></td>
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
                            <td><?php echo e($booking['total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="admin-panel admin-popular-panel">
        <div class="admin-panel-header">
            <h2>Lapangan Terpopuler</h2>
            <a href="<?php echo e(app_url('admin/lapangan')); ?>">Lihat Semua</a>
        </div>

        <div class="admin-popular-table">
            <div class="admin-popular-head">
                <span>Lapangan</span>
                <span>Total Booking</span>
                <span>Persentase</span>
            </div>
            <?php foreach ($popularFields as $field): ?>
                <div class="admin-popular-row">
                    <strong><?php echo e($field['name']); ?></strong>
                    <span><?php echo e($field['booking']); ?></span>
                    <div class="admin-progress-wrap">
                        <div class="admin-progress">
                            <span style="width: <?php echo (int) $field['percent']; ?>%;"></span>
                        </div>
                        <em><?php echo (int) $field['percent']; ?>%</em>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>

<section class="admin-bottom-metrics">
    <?php foreach ($bottomMetrics as $metric): ?>
        <article class="admin-metric-item">
            <div class="admin-metric-icon <?php echo e($metric['accent']); ?>">
                <i class="fa-solid <?php echo e($metric['icon']); ?>"></i>
            </div>
            <div>
                <p><?php echo e($metric['label']); ?></p>
                <strong><?php echo e($metric['value']); ?></strong>
                <small><i class="fa-solid fa-arrow-up"></i> <?php echo e($metric['trend']); ?> <span><?php echo e($metric['note']); ?></span></small>
            </div>
        </article>
    <?php endforeach; ?>
</section>
