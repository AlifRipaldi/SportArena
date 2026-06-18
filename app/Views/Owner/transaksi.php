<?php
$typeOptions = array('Semua Tipe', 'Booking Lapangan', 'Refund', 'Pencairan');
$methodOptions = array('Semua Metode', 'QRIS', 'DANA', 'OVO', 'Transfer Bank');
$statusOptions = array('Semua Status', 'Selesai', 'Menunggu', 'Dibatalkan');
$transactionTotal = isset($transactionTotal) ? (int) $transactionTotal : count($transactions);
?>

<section class="owner-transaksi-page">
    <div class="owner-transaksi-hero">
        <div>
            <h1>Lihat Semua Transaksi</h1>
            <p>Daftar semua transaksi pembayaran yang terjadi di lapangan Anda.</p>
        </div>
    </div>

    <section class="owner-transaksi-filter-panel" aria-label="Filter transaksi">
        <label class="owner-transaksi-field owner-transaksi-date-field">
            <span>Tanggal</span>
            <button type="button">
                <span>01 Mei 2024 - 31 Mei 2024</span>
                <i class="fa-regular fa-calendar-days"></i>
            </button>
        </label>

        <label class="owner-transaksi-field">
            <span>Tipe Transaksi</span>
            <select aria-label="Tipe transaksi">
                <?php foreach ($typeOptions as $option): ?>
                    <option><?php echo e($option); ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="owner-transaksi-field">
            <span>Metode Pembayaran</span>
            <select aria-label="Metode pembayaran">
                <?php foreach ($methodOptions as $option): ?>
                    <option><?php echo e($option); ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="owner-transaksi-field">
            <span>Status Pembayaran</span>
            <select aria-label="Status pembayaran">
                <?php foreach ($statusOptions as $option): ?>
                    <option><?php echo e($option); ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="owner-transaksi-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" placeholder="Cari transaksi / order ID..." aria-label="Cari transaksi atau order ID">
        </label>

        <button class="owner-transaksi-export" type="button">
            <i class="fa-solid fa-download"></i>
            <span>Export</span>
        </button>
    </section>

    <section class="owner-transaksi-stat-grid" aria-label="Ringkasan transaksi">
        <?php foreach ($transactionStats as $stat): ?>
            <article class="owner-transaksi-stat-card">
                <span class="owner-transaksi-stat-icon <?php echo e($stat['accent']); ?>">
                    <i class="fa-solid <?php echo e($stat['icon']); ?>"></i>
                </span>
                <div>
                    <p><?php echo e($stat['label']); ?></p>
                    <strong><?php echo e($stat['value']); ?></strong>
                    <small><?php echo e($stat['note']); ?></small>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <article class="admin-panel owner-transaksi-table-panel">
        <div class="admin-table-responsive">
            <table class="admin-table owner-transaksi-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Order ID</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Lapangan</th>
                        <th>Waktu Booking</th>
                        <th>Metode Pembayaran</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $index => $transaction): ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($transaction['orderId']); ?></td>
                            <td>
                                <span class="owner-transaksi-stack">
                                    <span><?php echo e($transaction['date']); ?></span>
                                    <small><?php echo e($transaction['time']); ?></small>
                                </span>
                            </td>
                            <td>
                                <span class="owner-transaksi-stack strong">
                                    <span><?php echo e($transaction['customer']); ?></span>
                                    <small><?php echo e($transaction['phone']); ?></small>
                                </span>
                            </td>
                            <td><?php echo e($transaction['field']); ?></td>
                            <td>
                                <span class="owner-transaksi-stack">
                                    <span><?php echo e($transaction['bookingDate']); ?></span>
                                    <small><?php echo e($transaction['bookingTime']); ?></small>
                                </span>
                            </td>
                            <td>
                                <span class="owner-transaksi-method">
                                    <span class="owner-transaksi-method-logo <?php echo e($transaction['methodClass']); ?>">
                                        <i class="fa-solid <?php echo e($transaction['methodIcon']); ?>"></i>
                                    </span>
                                    <?php echo e($transaction['method']); ?>
                                </span>
                            </td>
                            <td><strong class="owner-transaksi-amount"><?php echo e($transaction['total']); ?></strong></td>
                            <td><span class="owner-transaksi-status <?php echo e($transaction['statusClass']); ?>"><?php echo e($transaction['status']); ?></span></td>
                            <td>
                                <button class="owner-transaksi-detail" type="button">Detail</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="owner-transaksi-footer">
            <p>Menampilkan 1 - <?php echo e(count($transactions)); ?> dari <?php echo e($transactionTotal); ?> transaksi</p>

            <nav class="owner-transaksi-pagination" aria-label="Paginasi transaksi">
                <button type="button" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="active" type="button" aria-current="page">1</button>
                <button type="button">2</button>
                <button type="button">3</button>
                <span>...</span>
                <button type="button">16</button>
                <button type="button" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
            </nav>

            <label class="owner-transaksi-page-size">
                <select aria-label="Jumlah transaksi per halaman">
                    <option>10 / halaman</option>
                    <option>25 / halaman</option>
                    <option>50 / halaman</option>
                </select>
            </label>
        </div>
    </article>
</section>
