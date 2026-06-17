<?php
$periodTabs = array('Mingguan', 'Bulanan', 'Tahunan');
$selectedPeriod = isset($selectedPeriod) ? $selectedPeriod : 'Juni 2025';
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
                <?php foreach ($periodTabs as $tab): ?>
                    <button class="<?php echo $tab === 'Bulanan' ? 'active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $tab === 'Bulanan' ? 'true' : 'false'; ?>">
                        <?php echo e($tab); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <button class="owner-pendapatan-filter-btn owner-pendapatan-date" type="button">
                <i class="fa-regular fa-calendar"></i>
                <span><?php echo e($selectedPeriod); ?></span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>

            <button class="owner-pendapatan-download" type="button">
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
                        <span>1 Jun</span>
                        <span>5 Jun</span>
                        <span>10 Jun</span>
                        <span>15 Jun</span>
                        <span>20 Jun</span>
                        <span>25 Jun</span>
                        <span>30 Jun</span>
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
            <button type="button">
                <i class="fa-solid fa-list"></i>
                <span>Lihat Semua Transaksi</span>
            </button>
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
                    <?php foreach ($revenueTransactions as $index => $transaction): ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
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
                </tbody>
            </table>
        </div>

        <div class="owner-pendapatan-table-footer">
            <p>Menampilkan 1 - 5 dari 120 transaksi</p>
            <nav class="owner-pendapatan-pagination" aria-label="Paginasi transaksi pendapatan">
                <button type="button" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="active" type="button" aria-current="page">1</button>
                <button type="button">2</button>
                <button type="button">3</button>
                <span>...</span>
                <button type="button">24</button>
                <button type="button" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
            </nav>
        </div>
    </article>
</section>
