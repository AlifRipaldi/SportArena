<?php
// Owner Dashboard
?>

<section class="admin-hero">
    <div>
        <h1>Dashboard Pemilik</h1>
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
    <article class="admin-panel owner-revenue-panel">
        <div class="admin-panel-header">
            <h2>Pendapatan Mingguan</h2>
        </div>

        <div class="owner-weekly-chart">
            <div class="owner-chart-bars">
                <?php foreach ($weeklyRevenue as $item): ?>
                    <div class="owner-chart-bar-group">
                        <div class="owner-chart-bar">
                            <div class="owner-chart-fill" style="height: <?php echo (intval($item['bookings']) / 40 * 100); ?>%;" title="<?php echo e($item['revenue']); ?>"></div>
                        </div>
                        <span class="owner-chart-label"><?php echo e(substr($item['day'], 0, 3)); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="owner-chart-info">
                <p>Total Minggu Ini: <strong>Rp19.7 juta</strong></p>
                <p>Total Booking: <strong>177</strong></p>
            </div>
        </div>
    </article>

    <article class="admin-panel owner-status-panel">
        <div class="admin-panel-header">
            <h2>Status Lapangan</h2>
        </div>

        <div class="owner-field-status">
            <?php foreach ($fieldStatus as $field): ?>
                <div class="owner-field-item">
                    <div class="owner-field-info">
                        <strong><?php echo e($field['name']); ?></strong>
                        <span class="admin-badge <?php echo $field['status'] === 'Aktif' ? 'success' : 'warning'; ?>"><?php echo e($field['status']); ?></span>
                    </div>
                    <div class="owner-field-stats">
                        <span>📅 <?php echo e($field['bookingToday']); ?> booking hari ini</span>
                        <span>⭐ <?php echo e($field['rating']); ?>/5</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>

<section class="admin-dashboard-grid secondary">
    <article class="admin-panel admin-booking-table-panel">
        <div class="admin-panel-header">
            <h2>Booking Terbaru</h2>
            <a href="<?php echo e(app_url('pemilik/booking')); ?>">Lihat Semua</a>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Lapangan</th>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><strong><?php echo e($booking['code']); ?></strong></td>
                            <td><?php echo e($booking['field']); ?></td>
                            <td>
                                <div class="admin-customer">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($booking['user']); ?>&background=20314a&color=ffffff" alt="">
                                    <span><?php echo e($booking['user']); ?></span>
                                </div>
                            </td>
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
            <h2>Performa Lapangan</h2>
        </div>

        <div class="owner-performance">
            <?php foreach ($fieldPerformance as $field): ?>
                <div class="owner-perf-item">
                    <div>
                        <strong><?php echo e($field['name']); ?></strong>
                        <p><?php echo e($field['bookings']); ?> bookings</p>
                    </div>
                    <div class="owner-progress-bar">
                        <div class="owner-progress-fill" style="width: <?php echo e($field['percent']); ?>%;"></div>
                    </div>
                    <span><?php echo e($field['percent']); ?>%</span>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>

<style>
.owner-weekly-chart {
    display: grid;
    gap: 20px;
}

.owner-chart-bars {
    display: flex;
    gap: 12px;
    align-items: flex-end;
    height: 150px;
    padding: 16px 0;
}

.owner-chart-bar-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    flex: 1;
}

.owner-chart-bar {
    width: 100%;
    height: 100px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px 8px 0 0;
    overflow: hidden;
}

.owner-chart-fill {
    width: 100%;
    background: linear-gradient(180deg, #7be57d, #2ecc71);
    border-radius: 8px 8px 0 0;
}

.owner-chart-label {
    font-size: 12px;
    color: rgba(237, 246, 255, 0.7);
}

.owner-chart-info {
    display: flex;
    gap: 20px;
    padding: 12px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.owner-chart-info p {
    margin: 0;
    font-size: 14px;
    color: rgba(237, 246, 255, 0.8);
}

.owner-field-status {
    display: grid;
    gap: 12px;
}

.owner-field-item {
    padding: 12px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.owner-field-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.owner-field-info strong {
    font-size: 14px;
}

.owner-field-stats {
    display: flex;
    gap: 12px;
    font-size: 13px;
    color: rgba(237, 246, 255, 0.7);
}

.owner-performance {
    display: grid;
    gap: 16px;
}

.owner-perf-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
}

.owner-perf-item > div:first-child {
    min-width: 120px;
}

.owner-perf-item strong {
    display: block;
    font-size: 14px;
}

.owner-perf-item p {
    margin: 4px 0 0 0;
    font-size: 12px;
    color: rgba(237, 246, 255, 0.6);
}

.owner-progress-bar {
    flex: 1;
    height: 8px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 999px;
    overflow: hidden;
}

.owner-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #7be57d, #2ecc71);
    border-radius: 999px;
}

.owner-perf-item > span:last-child {
    min-width: 40px;
    text-align: right;
    font-size: 12px;
    color: #7be57d;
    font-weight: 700;
}
</style>
