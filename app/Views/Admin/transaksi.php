<?php
$paymentMarks = array(
    'bca' => 'VA',
    'gopay' => 'GP',
    'dana' => 'DN',
    'ovo' => 'OVO',
    'mandiri' => 'M',
);
?>

<section class="admin-hero">
    <div>
        <h1>Transaksi</h1>
        <p>Kelola semua transaksi pembayaran yang terjadi di Arena Sport.</p>
    </div>
</section>

<section class="admin-transaction-stat-grid" aria-label="Ringkasan transaksi">
    <?php foreach ($transactionStats as $stat): ?>
        <article class="admin-stat-card">
            <span class="admin-stat-icon <?php echo e($stat['accent']); ?>">
                <i class="fa-solid <?php echo e($stat['icon']); ?>"></i>
            </span>
            <div class="admin-stat-details">
                <p><?php echo e($stat['label']); ?></p>
                <strong><?php echo e($stat['value']); ?></strong>
                <small class="<?php echo $stat['direction'] === 'down' ? 'is-down' : ''; ?>">
                    <i class="fa-solid fa-arrow-<?php echo $stat['direction'] === 'down' ? 'down' : 'up'; ?>"></i>
                    <?php echo e($stat['trend']); ?> <span><?php echo e($stat['note']); ?></span>
                </small>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<div class="admin-content-section">
    <article class="admin-panel admin-full-width admin-transaction-panel">
        <div class="admin-transaction-toolbar">
            <label class="admin-filter-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="Cari ID transaksi, pengguna..." class="admin-search-input" aria-label="Cari transaksi">
            </label>

            <button class="admin-date-filter" type="button">
                <span>01/05/2024 - 31/05/2024</span>
                <i class="fa-regular fa-calendar-days"></i>
            </button>

            <select class="admin-filter-select" aria-label="Filter metode pembayaran">
                <option>Semua Metode</option>
                <option>VA BCA</option>
                <option>VA Mandiri</option>
                <option>GoPay</option>
                <option>DANA</option>
                <option>OVO</option>
            </select>

            <select class="admin-filter-select" aria-label="Filter status transaksi">
                <option>Semua Status</option>
                <option>Berhasil</option>
                <option>Gagal</option>
                <option>Refund</option>
            </select>

            <button class="admin-secondary-btn" type="button">
                <i class="fa-solid fa-download"></i>
                <span>Export</span>
            </button>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table admin-transaction-table">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Booking</th>
                        <th>Pengguna</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <?php
                            $paymentMark = isset($paymentMarks[$transaction['methodClass']]) ? $paymentMarks[$transaction['methodClass']] : strtoupper(substr($transaction['method'], 0, 2));
                        ?>
                        <tr>
                            <td><?php echo e($transaction['id']); ?></td>
                            <td>
                                <div class="admin-transaction-booking">
                                    <strong><?php echo e($transaction['booking']); ?></strong>
                                    <small><?php echo e($transaction['field']); ?></small>
                                </div>
                            </td>
                            <td>
                                <div class="admin-transaction-user">
                                    <span class="admin-review-avatar <?php echo e($transaction['accent']); ?>"><?php echo e($transaction['initials']); ?></span>
                                    <span>
                                        <strong><?php echo e($transaction['user']); ?></strong>
                                        <small><?php echo e($transaction['phone']); ?></small>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="admin-payment-method">
                                    <span class="admin-payment-logo <?php echo e($transaction['methodClass']); ?>"><?php echo e($paymentMark); ?></span>
                                    <?php echo e($transaction['method']); ?>
                                </span>
                            </td>
                            <td><strong class="admin-transaction-amount"><?php echo e($transaction['amount']); ?></strong></td>
                            <td><span class="admin-badge <?php echo e($transaction['statusClass']); ?>"><?php echo e($transaction['status']); ?></span></td>
                            <td>
                                <div class="admin-transaction-date">
                                    <span><?php echo e($transaction['date']); ?></span>
                                    <small><?php echo e($transaction['time']); ?></small>
                                </div>
                            </td>
                            <td>
                                <button class="btn-icon" type="button" title="Lihat transaksi <?php echo e($transaction['id']); ?>" aria-label="Lihat transaksi <?php echo e($transaction['id']); ?>">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination admin-pagination-spread">
            <span>Menampilkan 1 - 8 dari 362 data</span>
            <div class="admin-pagination-pages">
                <button class="admin-pagination-btn" type="button" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="admin-page-number active" type="button">1</button>
                <button class="admin-page-number" type="button">2</button>
                <button class="admin-page-number" type="button">3</button>
                <span>...</span>
                <button class="admin-page-number" type="button">46</button>
                <button class="admin-pagination-btn" type="button" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </article>
</div>
