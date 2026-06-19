<?php
$periodTabs = isset($periodTabs) ? $periodTabs : array('mingguan' => 'Mingguan', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan');
$selectedPeriodKey = isset($selectedPeriodKey) ? $selectedPeriodKey : 'bulanan';
$selectedPeriod = isset($selectedPeriod) ? $selectedPeriod : 'Juni 2025';
$selectedStartDate = isset($selectedStartDate) ? $selectedStartDate : '2025-06-01';
$selectedEndDate = isset($selectedEndDate) ? $selectedEndDate : '2025-06-30';
$reportPeriodDefault = $selectedPeriodKey === 'mingguan' ? '7_hari' : ($selectedPeriodKey === 'bulanan' ? 'bulan_ini' : 'kustom');
$formatReportInputDate = function ($dateValue) {
    $months = array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    );
    $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateValue);

    if (!$date) {
        return '01 Juni 2025';
    }

    return sprintf('%02d %s %s', (int) $date->format('j'), $months[(int) $date->format('n')], $date->format('Y'));
};
$selectedStartText = $formatReportInputDate($selectedStartDate);
$selectedEndText = $formatReportInputDate($selectedEndDate);
$revenueChartLabels = isset($revenueChartLabels) && is_array($revenueChartLabels) ? $revenueChartLabels : array('1 Jun', '5 Jun', '10 Jun', '15 Jun', '20 Jun', '25 Jun', '30 Jun');
$revenuePagination = isset($revenuePagination) ? $revenuePagination : array(
    'currentPage' => 1,
    'totalPages' => 1,
    'total' => count($revenueTransactions),
    'firstItem' => count($revenueTransactions) > 0 ? 1 : 0,
    'lastItem' => count($revenueTransactions),
);
$revenueUrl = function (array $params) use ($selectedPeriodKey, $selectedStartDate, $selectedEndDate) {
    $query = array_merge(array(
        'periode' => $selectedPeriodKey,
        'tanggal_mulai' => $selectedStartDate,
        'tanggal_selesai' => $selectedEndDate,
    ), $params);

    foreach ($query as $key => $value) {
        if ($value === null || $value === '') {
            unset($query[$key]);
        }
    }

    return app_url('pemilik/pendapatan?' . http_build_query($query));
};
$linePoints = array();
$highlightPoint = null;

foreach ($revenueChart as $point) {
    $linePoints[] = $point['x'] . ',' . $point['y'];

    if (!empty($point['highlight'])) {
        $highlightPoint = $point;
    }
}

$linePoints = implode(' ', $linePoints);
?>

<section class="owner-pendapatan-page">
    <div class="owner-pendapatan-hero">
        <div>
            <h1>Pendapatan</h1>
            <p>Pantau semua pendapatan dari lapangan Anda</p>
        </div>

        <div class="owner-pendapatan-toolbar" aria-label="Filter pendapatan">
            <div class="owner-pendapatan-tabs" role="tablist" aria-label="Periode pendapatan">
                <?php foreach ($periodTabs as $periodKey => $tab): ?>
                    <?php $isActiveTab = $selectedPeriodKey === $periodKey; ?>
                    <a class="<?php echo $isActiveTab ? 'active' : ''; ?>" href="<?php echo e($revenueUrl(array('periode' => $periodKey, 'tanggal_mulai' => null, 'tanggal_selesai' => null, 'page' => null))); ?>" role="tab" aria-selected="<?php echo $isActiveTab ? 'true' : 'false'; ?>">
                        <?php echo e($tab); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <details class="owner-pendapatan-dropdown owner-pendapatan-date-dropdown">
                <summary class="owner-pendapatan-filter-btn owner-pendapatan-date">
                    <i class="fa-regular fa-calendar"></i>
                    <span><?php echo e($selectedPeriod); ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </summary>

                <form class="owner-pendapatan-filter-panel" action="<?php echo e(app_url('pemilik/pendapatan')); ?>" method="get">
                    <input type="hidden" name="periode" value="<?php echo e($selectedPeriodKey); ?>">

                    <label>
                        <span>Dari tanggal</span>
                        <input type="date" name="tanggal_mulai" value="<?php echo e($selectedStartDate); ?>">
                    </label>

                    <label>
                        <span>Sampai tanggal</span>
                        <input type="date" name="tanggal_selesai" value="<?php echo e($selectedEndDate); ?>">
                    </label>

                    <div class="owner-pendapatan-filter-panel-actions">
                        <a href="<?php echo e($revenueUrl(array('tanggal_mulai' => null, 'tanggal_selesai' => null, 'page' => null))); ?>">Reset</a>
                        <button type="submit">Terapkan</button>
                    </div>
                </form>
            </details>

            <button class="owner-pendapatan-download" type="button" data-owner-report-open aria-haspopup="dialog" aria-controls="ownerRevenueReportModal" aria-expanded="false">
                <i class="fa-solid fa-download"></i>
                <span>Download Laporan</span>
            </button>
        </div>
    </div>

    <section class="owner-pendapatan-stat-grid" aria-label="Ringkasan pendapatan">
        <?php foreach ($revenueStats as $stat): ?>
            <article class="owner-pendapatan-stat-card">
                <span class="owner-pendapatan-stat-icon <?php echo e($stat['accent']); ?>">
                    <i class="fa-solid <?php echo e($stat['icon']); ?>"></i>
                </span>
                <div>
                    <p><?php echo e($stat['label']); ?></p>
                    <strong><?php echo e($stat['value']); ?></strong>
                    <small><i class="fa-solid fa-arrow-up"></i> <?php echo e($stat['trend']); ?> <span><?php echo e($stat['note']); ?></span></small>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="owner-pendapatan-content-grid">
        <article class="admin-panel owner-pendapatan-chart-panel">
            <div class="owner-pendapatan-panel-header">
                <h2>Grafik Pendapatan</h2>
            </div>

            <div class="owner-pendapatan-chart">
                <div class="owner-pendapatan-y-axis" aria-hidden="true">
                    <span>Rp1jt</span>
                    <span>Rp800rb</span>
                    <span>Rp600rb</span>
                    <span>Rp400rb</span>
                    <span>Rp200rb</span>
                    <span>Rp0</span>
                </div>

                <div class="owner-pendapatan-chart-stage">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                        <defs>
                            <linearGradient id="ownerRevenueFill" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#9bea25" stop-opacity="0.34"></stop>
                                <stop offset="100%" stop-color="#9bea25" stop-opacity="0.04"></stop>
                            </linearGradient>
                        </defs>
                        <polygon points="0,100 <?php echo e($linePoints); ?> 100,100" class="owner-pendapatan-chart-fill"></polygon>
                        <polyline points="<?php echo e($linePoints); ?>" class="owner-pendapatan-chart-line"></polyline>
                    </svg>

                    <?php foreach ($revenueChart as $point): ?>
                        <span class="owner-pendapatan-chart-dot <?php echo !empty($point['highlight']) ? 'highlight' : ''; ?>" title="<?php echo e($point['label'] . ' - ' . $point['amount']); ?>" style="left: <?php echo e($point['x']); ?>%; top: <?php echo e($point['y']); ?>%;"></span>
                    <?php endforeach; ?>

                    <?php if ($highlightPoint): ?>
                        <div class="owner-pendapatan-chart-tooltip" style="left: <?php echo e($highlightPoint['x']); ?>%; top: <?php echo e($highlightPoint['y']); ?>%;">
                            <span><?php echo e($highlightPoint['label']); ?></span>
                            <strong><?php echo e($highlightPoint['amount']); ?></strong>
                        </div>
                    <?php endif; ?>

                    <div class="owner-pendapatan-x-axis" aria-hidden="true">
                        <?php foreach ($revenueChartLabels as $labelIndex => $chartLabel): ?>
                            <?php if ($labelIndex === 0 || $labelIndex === count($revenueChartLabels) - 1 || $labelIndex % max(1, (int) ceil(count($revenueChartLabels) / 6)) === 0): ?>
                                <span><?php echo e($chartLabel); ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </article>

        <article class="admin-panel owner-pendapatan-summary-panel">
            <div class="owner-pendapatan-panel-header">
                <h2>Ringkasan Pendapatan</h2>
            </div>

            <div class="owner-pendapatan-summary-list">
                <?php foreach ($revenueSummary as $index => $item): ?>
                    <?php if ($index === 2): ?>
                        <span class="owner-pendapatan-summary-divider"></span>
                    <?php endif; ?>
                    <div class="owner-pendapatan-summary-item">
                        <span class="owner-pendapatan-summary-icon <?php echo e($item['accent']); ?>">
                            <i class="fa-solid <?php echo e($item['icon']); ?>"></i>
                        </span>
                        <p><?php echo e($item['label']); ?></p>
                        <strong class="<?php echo e($item['tone']); ?>"><?php echo e($item['value']); ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </section>

    <article class="admin-panel owner-pendapatan-table-panel">
        <div class="owner-pendapatan-table-header">
            <h2>Riwayat Transaksi</h2>
            <a href="<?php echo e(app_url('pemilik/transaksi')); ?>">
                <i class="fa-solid fa-list"></i>
                <span>Lihat Semua Transaksi</span>
            </a>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table owner-pendapatan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Lapangan</th>
                        <th>Nama Penyewa</th>
                        <th>Metode Pembayaran</th>
                        <th>Total</th>
                        <th>Potongan (2%)</th>
                        <th>Bersih</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($revenueTransactions)): ?>
                        <tr class="owner-pendapatan-empty-row">
                            <td colspan="9">
                                <div class="owner-pendapatan-empty">
                                    <i class="fa-regular fa-calendar-xmark"></i>
                                    <strong>Tidak ada transaksi ditemukan</strong>
                                    <span>Ubah periode atau tanggal filter untuk melihat data pendapatan lain.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($revenueTransactions as $index => $transaction): ?>
                            <tr>
                                <td><?php echo e((int) $revenuePagination['firstItem'] + $index); ?></td>
                                <td><?php echo e($transaction['date']); ?></td>
                                <td><?php echo e($transaction['field']); ?></td>
                                <td><?php echo e($transaction['tenant']); ?></td>
                                <td>
                                    <span class="owner-pendapatan-method <?php echo e($transaction['methodClass']); ?>">
                                        <i class="fa-solid <?php echo e($transaction['methodIcon']); ?>"></i>
                                        <?php echo e($transaction['method']); ?>
                                    </span>
                                </td>
                                <td><?php echo e($transaction['total']); ?></td>
                                <td class="owner-pendapatan-fee"><?php echo e($transaction['fee']); ?></td>
                                <td class="owner-pendapatan-net"><?php echo e($transaction['net']); ?></td>
                                <td><span class="owner-pendapatan-status"><?php echo e($transaction['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="owner-pendapatan-table-footer">
            <p>
                <?php if ((int) $revenuePagination['total'] > 0): ?>
                    Menampilkan <?php echo e($revenuePagination['firstItem']); ?> - <?php echo e($revenuePagination['lastItem']); ?> dari <?php echo e($revenuePagination['total']); ?> transaksi
                <?php else: ?>
                    Tidak ada transaksi pada <?php echo e($selectedPeriod); ?>
                <?php endif; ?>
            </p>
            <nav class="owner-pendapatan-pagination" aria-label="Paginasi transaksi pendapatan">
                <?php if ((int) $revenuePagination['currentPage'] > 1): ?>
                    <a href="<?php echo e($revenueUrl(array('page' => (int) $revenuePagination['currentPage'] - 1))); ?>" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></a>
                <?php else: ?>
                    <span class="disabled" aria-disabled="true" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></span>
                <?php endif; ?>

                <?php for ($pageNumber = 1; $pageNumber <= (int) $revenuePagination['totalPages']; $pageNumber++): ?>
                    <?php if ($pageNumber === (int) $revenuePagination['currentPage']): ?>
                        <span class="active" aria-current="page"><?php echo e($pageNumber); ?></span>
                    <?php else: ?>
                        <a href="<?php echo e($revenueUrl(array('page' => $pageNumber))); ?>"><?php echo e($pageNumber); ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ((int) $revenuePagination['currentPage'] < (int) $revenuePagination['totalPages']): ?>
                    <a href="<?php echo e($revenueUrl(array('page' => (int) $revenuePagination['currentPage'] + 1))); ?>" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></a>
                <?php else: ?>
                    <span class="disabled" aria-disabled="true" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></span>
                <?php endif; ?>
            </nav>
        </div>
    </article>

    <div class="owner-report-modal" id="ownerRevenueReportModal" data-owner-report-modal hidden>
        <div class="owner-report-modal-backdrop" data-owner-report-close></div>

        <section class="owner-report-dialog" role="dialog" aria-modal="true" aria-labelledby="ownerRevenueReportTitle">
            <header class="owner-report-header">
                <div>
                    <h2 id="ownerRevenueReportTitle">Download Laporan</h2>
                    <p>Pilih periode dan format laporan yang ingin Anda download.</p>
                </div>

                <button class="owner-report-close" type="button" data-owner-report-close aria-label="Tutup download laporan">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </header>

            <form class="owner-report-form" method="get" action="<?php echo e(app_url('pemilik/pendapatan/download')); ?>" data-owner-report-form>
                <section class="owner-report-section">
                    <h3>1. Pilih Periode Laporan</h3>

                    <div class="owner-report-period-options" role="group" aria-label="Periode laporan">
                        <label class="owner-report-period-option">
                            <input type="radio" name="periode_laporan" value="hari_ini" <?php echo $reportPeriodDefault === 'hari_ini' ? 'checked' : ''; ?>>
                            <span>Hari Ini</span>
                        </label>
                        <label class="owner-report-period-option">
                            <input type="radio" name="periode_laporan" value="kemarin" <?php echo $reportPeriodDefault === 'kemarin' ? 'checked' : ''; ?>>
                            <span>Kemarin</span>
                        </label>
                        <label class="owner-report-period-option">
                            <input type="radio" name="periode_laporan" value="7_hari" <?php echo $reportPeriodDefault === '7_hari' ? 'checked' : ''; ?>>
                            <span>7 Hari Terakhir</span>
                        </label>
                        <label class="owner-report-period-option">
                            <input type="radio" name="periode_laporan" value="30_hari" <?php echo $reportPeriodDefault === '30_hari' ? 'checked' : ''; ?>>
                            <span>30 Hari Terakhir</span>
                        </label>
                        <label class="owner-report-period-option">
                            <input type="radio" name="periode_laporan" value="bulan_ini" <?php echo $reportPeriodDefault === 'bulan_ini' ? 'checked' : ''; ?>>
                            <span>Bulan Ini</span>
                        </label>
                        <label class="owner-report-period-option">
                            <input type="radio" name="periode_laporan" value="kustom" <?php echo $reportPeriodDefault === 'kustom' ? 'checked' : ''; ?>>
                            <span>Kustom <i class="fa-regular fa-calendar-days"></i></span>
                        </label>
                    </div>

                    <div class="owner-report-date-grid">
                        <label class="owner-report-date-field">
                            <span>Dari Tanggal</span>
                            <span class="owner-report-date-input">
                                <input type="text" name="tanggal_mulai" value="<?php echo e($selectedStartText); ?>" aria-label="Tanggal mulai laporan" autocomplete="off" data-owner-report-start>
                                <i class="fa-regular fa-calendar-days"></i>
                            </span>
                        </label>

                        <label class="owner-report-date-field">
                            <span>Sampai Tanggal</span>
                            <span class="owner-report-date-input">
                                <input type="text" name="tanggal_selesai" value="<?php echo e($selectedEndText); ?>" aria-label="Tanggal selesai laporan" autocomplete="off" data-owner-report-end>
                                <i class="fa-regular fa-calendar-days"></i>
                            </span>
                        </label>
                    </div>
                </section>

                <section class="owner-report-section">
                    <h3>2. Pilih Tipe Laporan</h3>

                    <div class="owner-report-type-list">
                        <label class="owner-report-type-option">
                            <input type="radio" name="tipe_laporan" value="pendapatan" checked>
                            <span class="owner-report-type-mark"></span>
                            <span class="owner-report-type-copy">
                                <strong>Laporan Pendapatan</strong>
                                <small>Ringkasan pemasukan dan pendapatan bersih</small>
                            </span>
                        </label>

                        <label class="owner-report-type-option">
                            <input type="radio" name="tipe_laporan" value="transaksi">
                            <span class="owner-report-type-mark"></span>
                            <span class="owner-report-type-copy">
                                <strong>Laporan Transaksi</strong>
                                <small>Berisi semua detail transaksi pembayaran</small>
                            </span>
                        </label>

                        <label class="owner-report-type-option">
                            <input type="radio" name="tipe_laporan" value="potongan">
                            <span class="owner-report-type-mark"></span>
                            <span class="owner-report-type-copy">
                                <strong>Laporan Potongan Platform</strong>
                                <small>Rincian biaya platform dan pendapatan bersih</small>
                            </span>
                        </label>
                    </div>
                </section>

                <section class="owner-report-section">
                    <h3>3. Pilih Format File</h3>

                    <div class="owner-report-format-grid">
                        <label class="owner-report-format-option">
                            <input type="radio" name="format_laporan" value="xlsx" checked>
                            <span class="owner-report-format-body">
                                <span class="owner-report-format-icon excel"><i class="fa-solid fa-file-excel"></i></span>
                                <span>
                                    <strong>Excel (.xlsx)</strong>
                                    <small>Cocok untuk analisis data</small>
                                </span>
                            </span>
                        </label>

                        <label class="owner-report-format-option">
                            <input type="radio" name="format_laporan" value="pdf">
                            <span class="owner-report-format-body">
                                <span class="owner-report-format-icon pdf"><i class="fa-solid fa-file-pdf"></i></span>
                                <span>
                                    <strong>PDF (.pdf)</strong>
                                    <small>Cocok untuk dokumen</small>
                                </span>
                            </span>
                        </label>

                        <label class="owner-report-format-option">
                            <input type="radio" name="format_laporan" value="csv">
                            <span class="owner-report-format-body">
                                <span class="owner-report-format-icon csv"><i class="fa-solid fa-file-csv"></i></span>
                                <span>
                                    <strong>CSV (.csv)</strong>
                                    <small>Cocok untuk data mentah</small>
                                </span>
                            </span>
                        </label>
                    </div>
                </section>

                <div class="owner-report-actions">
                    <button class="owner-report-cancel" type="button" data-owner-report-close>Batal</button>
                    <button class="owner-report-submit" type="submit" data-owner-report-submit>
                        <i class="fa-solid fa-download"></i>
                        <span>Download Laporan</span>
                    </button>
                </div>

                <p class="owner-report-note" data-owner-report-note>
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Laporan akan diunduh sesuai filter dan pengaturan yang Anda pilih.</span>
                </p>
            </form>
        </section>
    </div>
</section>

<script>
(function () {
    var modal = document.querySelector('[data-owner-report-modal]');
    var openButton = document.querySelector('[data-owner-report-open]');

    if (!modal || !openButton) {
        return;
    }

    var closeButtons = modal.querySelectorAll('[data-owner-report-close]');
    var form = modal.querySelector('[data-owner-report-form]');
    var firstField = modal.querySelector('input[name="periode_laporan"]');
    var periodInputs = modal.querySelectorAll('input[name="periode_laporan"]');
    var startInput = modal.querySelector('[data-owner-report-start]');
    var endInput = modal.querySelector('[data-owner-report-end]');
    var note = modal.querySelector('[data-owner-report-note]');
    var submitButton = modal.querySelector('[data-owner-report-submit]');
    var submitLabel = submitButton ? submitButton.querySelector('span') : null;
    var submitDefaultLabel = submitLabel ? submitLabel.textContent : '';
    var defaultNote = note ? note.querySelector('span').textContent : '';
    var reportBaseDate = new Date(2025, 5, 30);
    var monthNames = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    var monthLookup = {
        jan: 0,
        januari: 0,
        feb: 1,
        februari: 1,
        mar: 2,
        maret: 2,
        apr: 3,
        april: 3,
        mei: 4,
        may: 4,
        jun: 5,
        juni: 5,
        jul: 6,
        juli: 6,
        agu: 7,
        agus: 7,
        agustus: 7,
        aug: 7,
        sep: 8,
        september: 8,
        okt: 9,
        oktober: 9,
        oct: 9,
        nov: 10,
        november: 10,
        des: 11,
        desember: 11,
        dec: 11
    };

    function cloneDate(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    function addDays(date, days) {
        var nextDate = cloneDate(date);
        nextDate.setDate(nextDate.getDate() + days);

        return nextDate;
    }

    function formatReportDate(date) {
        var day = String(date.getDate()).padStart(2, '0');

        return day + ' ' + monthNames[date.getMonth()] + ' ' + date.getFullYear();
    }

    function parseReportDate(value) {
        var normalized = String(value || '').trim().toLowerCase().replace(/[,.]/g, ' ').replace(/\s+/g, ' ');
        var isoMatch = normalized.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        var textMatch;
        var date;
        var day;
        var month;
        var year;

        if (isoMatch) {
            year = Number(isoMatch[1]);
            month = Number(isoMatch[2]) - 1;
            day = Number(isoMatch[3]);
        } else {
            textMatch = normalized.match(/^(\d{1,2})\s+([a-z]+)\s+(\d{4})$/);

            if (!textMatch || typeof monthLookup[textMatch[2]] === 'undefined') {
                return null;
            }

            day = Number(textMatch[1]);
            month = monthLookup[textMatch[2]];
            year = Number(textMatch[3]);
        }

        date = new Date(year, month, day);

        if (date.getFullYear() !== year || date.getMonth() !== month || date.getDate() !== day) {
            return null;
        }

        return date;
    }

    function setNote(message, isError) {
        if (!note) {
            return;
        }

        note.classList.toggle('is-error', !!isError);
        note.querySelector('span').textContent = message || defaultNote;
    }

    function setSubmitting(isSubmitting) {
        if (!submitButton) {
            return;
        }

        submitButton.disabled = isSubmitting;
        submitButton.classList.toggle('is-loading', isSubmitting);

        if (submitLabel) {
            submitLabel.textContent = isSubmitting ? 'Menyiapkan...' : submitDefaultLabel;
        }
    }

    function setDateRange(startDate, endDate) {
        if (startInput) {
            startInput.value = formatReportDate(startDate);
        }

        if (endInput) {
            endInput.value = formatReportDate(endDate);
        }

        setNote(defaultNote, false);
    }

    function setRangeFromPeriod(period) {
        var startDate = cloneDate(reportBaseDate);
        var endDate = cloneDate(reportBaseDate);

        if (period === 'kustom') {
            return;
        }

        if (period === 'kemarin') {
            startDate = addDays(reportBaseDate, -1);
            endDate = cloneDate(startDate);
        } else if (period === '7_hari') {
            startDate = addDays(reportBaseDate, -6);
        } else if (period === '30_hari') {
            startDate = addDays(reportBaseDate, -29);
        } else if (period === 'bulan_ini') {
            startDate = new Date(reportBaseDate.getFullYear(), reportBaseDate.getMonth(), 1);
            endDate = new Date(reportBaseDate.getFullYear(), reportBaseDate.getMonth() + 1, 0);
        }

        setDateRange(startDate, endDate);
    }

    function chooseCustomPeriod() {
        var customInput = modal.querySelector('input[name="periode_laporan"][value="kustom"]');

        if (customInput) {
            customInput.checked = true;
        }
    }

    function openModal() {
        modal.hidden = false;
        document.body.classList.add('owner-report-modal-open');
        openButton.setAttribute('aria-expanded', 'true');
        setSubmitting(false);

        window.setTimeout(function () {
            if (firstField) {
                firstField.focus();
            }
        }, 0);
    }

    function closeModal() {
        modal.hidden = true;
        document.body.classList.remove('owner-report-modal-open');
        openButton.setAttribute('aria-expanded', 'false');
        setSubmitting(false);
        openButton.focus();
    }

    openButton.addEventListener('click', openModal);

    Array.prototype.forEach.call(closeButtons, function (button) {
        button.addEventListener('click', closeModal);
    });

    Array.prototype.forEach.call(periodInputs, function (input) {
        input.addEventListener('change', function () {
            if (input.checked) {
                setRangeFromPeriod(input.value);
            }
        });
    });

    [startInput, endInput].forEach(function (input) {
        if (!input) {
            return;
        }

        input.addEventListener('focus', chooseCustomPeriod);
        input.addEventListener('input', function () {
            chooseCustomPeriod();
            setNote(defaultNote, false);
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    if (form) {
        form.addEventListener('submit', function (event) {
            var startDate = parseReportDate(startInput ? startInput.value : '');
            var endDate = parseReportDate(endInput ? endInput.value : '');

            if (!startDate || !endDate) {
                event.preventDefault();
                setNote('Tanggal harus ditulis seperti 01 Juni 2025.', true);
                setSubmitting(false);
                return;
            }

            if (startDate > endDate) {
                event.preventDefault();
                setNote('Tanggal mulai tidak boleh melewati tanggal selesai.', true);
                setSubmitting(false);
                return;
            }

            setSubmitting(true);
            setNote('Laporan sedang disiapkan untuk diunduh...', false);

            window.setTimeout(function () {
                if (!modal.hidden) {
                    closeModal();
                }
            }, 450);
        });
    }
})();
</script>
