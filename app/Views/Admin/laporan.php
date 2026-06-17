<?php
$linePoints = array();
$areaPoints = array('0,100');
$highlightPoint = null;

foreach ($revenueReportPoints as $point) {
    $coordinate = $point['x'] . ',' . $point['y'];
    $linePoints[] = $coordinate;
    $areaPoints[] = $coordinate;

    if (!empty($point['highlight'])) {
        $highlightPoint = $point;
    }
}

$areaPoints[] = '100,100';
$linePoints = implode(' ', $linePoints);
$areaPoints = implode(' ', $areaPoints);
?>

<section class="admin-hero admin-report-hero">
    <div>
        <h1>Laporan</h1>
        <p>Lihat dan unduh laporan performa Arena Sport.</p>
    </div>
    <div class="admin-hero-actions">
        <button class="admin-secondary-btn" type="button">
            <i class="fa-solid fa-download"></i>
            <span>Export PDF</span>
        </button>
    </div>
</section>

<section class="admin-report-toolbar" aria-label="Filter laporan">
    <button class="admin-date-filter" type="button">
        <i class="fa-regular fa-calendar-days"></i>
        <span>01/05/2024 - 31/05/2024</span>
    </button>

    <select class="admin-filter-select" aria-label="Filter lapangan laporan">
        <option>Semua Lapangan</option>
        <option>Arena Futsal Parepare</option>
        <option>Badminton Center</option>
        <option>Mini Soccer Victory</option>
        <option>Basketball Court</option>
    </select>

    <select class="admin-filter-select admin-report-period" aria-label="Periode laporan">
        <option>Periode: Bulan Ini</option>
        <option>Periode: Minggu Ini</option>
        <option>Periode: Tahun Ini</option>
    </select>
</section>

<section class="admin-report-stat-grid" aria-label="Ringkasan laporan">
    <?php foreach ($reportStats as $stat): ?>
        <article class="admin-report-stat-card">
            <div>
                <p><?php echo e($stat['label']); ?></p>
                <strong><?php echo e($stat['value']); ?></strong>
                <small><i class="fa-solid fa-arrow-up"></i> <?php echo e($stat['trend']); ?> <span><?php echo e($stat['note']); ?></span></small>
            </div>
            <span class="admin-stat-icon <?php echo e($stat['accent']); ?>">
                <i class="fa-solid <?php echo e($stat['icon']); ?>"></i>
            </span>
        </article>
    <?php endforeach; ?>
</section>

<section class="admin-report-grid">
    <article class="admin-panel admin-report-chart-panel">
        <div class="admin-report-panel-head">
            <div>
                <h2>Grafik Pendapatan</h2>
                <p>Grafik pendapatan selama periode 01 Mei - 31 Mei 2024</p>
            </div>
            <button type="button">Harian <i class="fa-solid fa-chevron-down"></i></button>
        </div>

        <div class="admin-report-line-chart">
            <div class="admin-report-chart-y">
                <span>Rp5jt</span>
                <span>Rp4jt</span>
                <span>Rp3jt</span>
                <span>Rp2jt</span>
                <span>Rp1jt</span>
                <span>Rp0</span>
            </div>
            <div class="admin-report-chart-stage">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polygon points="<?php echo e($areaPoints); ?>" class="admin-report-chart-fill"></polygon>
                    <polyline points="<?php echo e($linePoints); ?>" class="admin-report-chart-line"></polyline>
                </svg>

                <?php foreach ($revenueReportPoints as $point): ?>
                    <span class="admin-report-dot <?php echo !empty($point['highlight']) ? 'active' : ''; ?>" style="left: <?php echo e($point['x']); ?>%; top: <?php echo e($point['y']); ?>%;" title="<?php echo e($point['label'] . ' - ' . $point['amount']); ?>"></span>
                <?php endforeach; ?>

                <?php if ($highlightPoint): ?>
                    <div class="admin-report-tooltip" style="left: <?php echo e($highlightPoint['x']); ?>%; top: <?php echo e($highlightPoint['y']); ?>%;">
                        <span><?php echo e($highlightPoint['label']); ?> 2024</span>
                        <strong><i></i><?php echo e($highlightPoint['amount']); ?></strong>
                    </div>
                <?php endif; ?>

                <div class="admin-report-chart-months">
                    <span>1 Mei</span>
                    <span>8 Mei</span>
                    <span>15 Mei</span>
                    <span>22 Mei</span>
                    <span>31 Mei</span>
                </div>
            </div>
        </div>
    </article>

    <article class="admin-panel admin-payment-report-panel">
        <div class="admin-report-panel-head">
            <div>
                <h2>Pendapatan per Metode Pembayaran</h2>
                <p>Total pendapatan dikelompokkan berdasarkan metode pembayaran</p>
            </div>
        </div>

        <div class="admin-payment-report-content">
            <div class="admin-payment-donut" aria-label="Distribusi pendapatan metode pembayaran"></div>
            <div class="admin-payment-legend">
                <?php foreach ($paymentReport as $item): ?>
                    <div>
                        <span class="admin-report-dot-label <?php echo e($item['color']); ?>"></span>
                        <strong><?php echo e($item['method']); ?></strong>
                        <em><?php echo e($item['amount']); ?></em>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </article>
</section>

<section class="admin-report-grid">
    <article class="admin-panel admin-booking-report-panel">
        <div class="admin-report-panel-head">
            <div>
                <h2>Booking per Lapangan</h2>
                <p>Jumlah booking pada setiap lapangan</p>
            </div>
        </div>

        <div class="admin-booking-bars">
            <div class="admin-booking-axis">
                <span>120</span>
                <span>100</span>
                <span>80</span>
                <span>60</span>
                <span>40</span>
                <span>20</span>
                <span>0</span>
            </div>
            <div class="admin-booking-bars-stage">
                <?php foreach ($fieldBookingReport as $field): ?>
                    <div class="admin-booking-bar-item">
                        <strong><?php echo (int) $field['value']; ?></strong>
                        <span style="height: <?php echo (int) $field['height']; ?>%;"></span>
                        <small><?php echo $field['short']; ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </article>

    <article class="admin-panel admin-report-summary-panel">
        <div class="admin-report-panel-head">
            <div>
                <h2>Ringkasan Laporan</h2>
            </div>
        </div>

        <div class="admin-report-download-list">
            <?php foreach ($reportDownloads as $download): ?>
                <div class="admin-report-download-item">
                    <span class="admin-report-file-icon"><i class="fa-solid <?php echo e($download['icon']); ?>"></i></span>
                    <div>
                        <strong><?php echo e($download['title']); ?></strong>
                        <small><?php echo e($download['description']); ?></small>
                    </div>
                    <div class="admin-report-download-actions">
                        <button type="button"><i class="fa-solid fa-download"></i> PDF</button>
                        <button type="button"><i class="fa-solid fa-download"></i> Excel</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>
