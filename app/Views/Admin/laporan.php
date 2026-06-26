<?php
$linePoints = array();
$areaPoints = array('0,100');
$highlightPoint = null;
$reportFilters = isset($reportFilters) && is_array($reportFilters) ? $reportFilters : array(
    'start' => date('Y-m-01'),
    'end' => date('Y-m-t'),
    'field' => '',
    'fieldLabel' => 'Semua Lapangan',
    'periodLabel' => date('d/m/Y', strtotime('first day of this month')) . ' - ' . date('d/m/Y', strtotime('last day of this month')),
    'monthLabel' => date('m/Y'),
);
$reportFields = isset($reportFields) && is_array($reportFields) ? $reportFields : array();
$reportExportQuery = isset($reportExportQuery) ? (string) $reportExportQuery : http_build_query(array('start' => $reportFilters['start'], 'end' => $reportFilters['end']));
$reportExportSuffix = $reportExportQuery !== '' ? '?' . $reportExportQuery : '';
$reportStartLabel = isset($reportFilters['startLabel']) ? $reportFilters['startLabel'] : date('d/m/Y', strtotime($reportFilters['start']));
$reportEndLabel = isset($reportFilters['endLabel']) ? $reportFilters['endLabel'] : date('d/m/Y', strtotime($reportFilters['end']));
$reportMonthLabel = isset($reportFilters['monthLabel']) ? $reportFilters['monthLabel'] : date('m/Y');
$revenueAxisLabels = isset($revenueAxisLabels) && is_array($revenueAxisLabels) ? $revenueAxisLabels : array('Rp5jt', 'Rp4jt', 'Rp3jt', 'Rp2jt', 'Rp1jt', 'Rp0');
$revenueDateTicks = isset($revenueDateTicks) && is_array($revenueDateTicks) ? $revenueDateTicks : array(
    date('d M', strtotime('first day of this month')),
    date('d M', strtotime('+7 days', strtotime('first day of this month'))),
    date('d M', strtotime('+14 days', strtotime('first day of this month'))),
    date('d M', strtotime('+21 days', strtotime('first day of this month'))),
    date('d M', strtotime('last day of this month')),
);
$fieldBookingAxisLabels = isset($fieldBookingAxisLabels) && is_array($fieldBookingAxisLabels) ? $fieldBookingAxisLabels : array('120', '100', '80', '60', '40', '20', '0');
$paymentColors = array(
    'blue' => '#2f74df',
    'purple' => '#9b3ff0',
    'teal' => '#35b8c8',
    'orange' => '#ff8a00',
    'light' => '#e8e8e8',
);
$paymentSegments = array();
$paymentCursor = 0;

foreach ($paymentReport as $item) {
    $percent = max(0, min(100, (int) $item['percent']));
    if ($percent <= 0) {
        continue;
    }

    $color = isset($paymentColors[$item['color']]) ? $paymentColors[$item['color']] : '#2f74df';
    $paymentNext = min(100, $paymentCursor + $percent);
    $paymentSegments[] = $color . ' ' . $paymentCursor . '% ' . $paymentNext . '%';
    $paymentCursor = $paymentNext;
}

if (empty($paymentSegments)) {
    $paymentSegments[] = '#e8e8e8 0% 100%';
} elseif ($paymentCursor < 100) {
    $paymentSegments[] = '#e8e8e8 ' . $paymentCursor . '% 100%';
}

$paymentDonutStyle = 'background: radial-gradient(circle, #07111e 0 46%, transparent 47%), conic-gradient(' . implode(', ', $paymentSegments) . ');';

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
        <a class="admin-secondary-btn" href="<?php echo e(app_url('admin/export/laporan' . $reportExportSuffix)); ?>">
            <i class="fa-solid fa-download"></i>
            <span>Export CSV</span>
        </a>
    </div>
</section>

<form class="admin-report-toolbar" method="get" action="<?php echo e(app_url('admin/laporan')); ?>" aria-label="Filter laporan">
    <label class="admin-date-filter admin-report-filter-control">
        <i class="fa-regular fa-calendar-days"></i>
        <span>Mulai</span>
        <input type="date" name="start" value="<?php echo e($reportFilters['start']); ?>" aria-label="Tanggal mulai laporan">
    </label>

    <label class="admin-date-filter admin-report-filter-control">
        <i class="fa-regular fa-calendar-check"></i>
        <span>Selesai</span>
        <input type="date" name="end" value="<?php echo e($reportFilters['end']); ?>" aria-label="Tanggal selesai laporan">
    </label>

    <label class="admin-report-field-filter">
        <i class="fa-solid fa-location-dot"></i>
        <select name="field" aria-label="Filter lapangan">
            <option value="">Semua Lapangan</option>
            <?php foreach ($reportFields as $field): ?>
                <option value="<?php echo e($field['id']); ?>" <?php echo $reportFilters['field'] === $field['id'] ? 'selected' : ''; ?>><?php echo e($field['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <div class="admin-report-toolbar-actions">
        <button class="admin-secondary-btn" type="submit">
            <i class="fa-solid fa-filter"></i>
            <span>Terapkan</span>
        </button>
        <a class="admin-report-reset" href="<?php echo e(app_url('admin/laporan')); ?>">Reset</a>
    </div>
</form>

<p class="admin-report-active-filter">
    Periode <?php echo e($reportStartLabel . ' - ' . $reportEndLabel); ?> &middot; <?php echo e(isset($reportFilters['fieldLabel']) ? $reportFilters['fieldLabel'] : 'Semua Lapangan'); ?>
</p>

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
                <p>Grafik pendapatan selama periode <?php echo e($reportMonthLabel); ?></p>
            </div>
            <span class="admin-badge active">Harian</span>
        </div>

        <div class="admin-report-line-chart">
            <div class="admin-report-chart-y">
                <?php foreach ($revenueAxisLabels as $label): ?>
                    <span><?php echo e($label); ?></span>
                <?php endforeach; ?>
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
                        <span><?php echo e($highlightPoint['label']); ?></span>
                        <strong><i></i><?php echo e($highlightPoint['amount']); ?></strong>
                    </div>
                <?php endif; ?>

                <div class="admin-report-chart-months">
                    <?php foreach ($revenueDateTicks as $tick): ?>
                        <span><?php echo e($tick); ?></span>
                    <?php endforeach; ?>
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
            <div class="admin-payment-donut" style="<?php echo e($paymentDonutStyle); ?>" aria-label="Distribusi pendapatan metode pembayaran"></div>
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
                <?php foreach ($fieldBookingAxisLabels as $label): ?>
                    <span><?php echo e($label); ?></span>
                <?php endforeach; ?>
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
                        <a href="<?php echo e(app_url('admin/export/' . $download['type'] . $reportExportSuffix)); ?>"><i class="fa-solid fa-download"></i> CSV</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>
