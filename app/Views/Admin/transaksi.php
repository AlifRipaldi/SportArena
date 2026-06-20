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
        <div class="admin-transaction-toolbar" data-admin-filter="#adminTransactionRows tr[data-filter-text]">
            <label class="admin-filter-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="Cari ID transaksi, pengguna..." class="admin-search-input" aria-label="Cari transaksi">
            </label>

            <select class="admin-filter-select" aria-label="Filter metode pembayaran">
                <option value="">Semua Metode</option>
                <?php foreach (array_values(array_unique(array_column($transactions, 'method'))) as $paymentMethod): ?><option value="<?php echo e($paymentMethod); ?>"><?php echo e($paymentMethod); ?></option><?php endforeach; ?>
            </select>

            <select class="admin-filter-select" aria-label="Filter status transaksi">
                <option value="">Semua Status</option>
                <option value="Berhasil">Berhasil</option><option value="Pending">Pending</option><option value="Gagal">Gagal</option><option value="Refund">Refund</option>
            </select>

            <a class="admin-secondary-btn" href="<?php echo e(app_url('admin/export/transaksi')); ?>">
                <i class="fa-solid fa-download"></i>
                <span>Export</span>
            </a>
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
                <tbody id="adminTransactionRows">
                    <?php foreach ($transactions as $transaction): ?>
                        <?php
                            $paymentMark = isset($paymentMarks[$transaction['methodClass']]) ? $paymentMarks[$transaction['methodClass']] : strtoupper(substr($transaction['method'], 0, 2));
                        ?>
                        <tr data-filter-text="<?php echo e(implode(' ', array($transaction['id'], $transaction['booking'], $transaction['user'], $transaction['field'], $transaction['method'], $transaction['status'], $transaction['date']))); ?>">
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
                                <button class="btn-icon" type="button" title="Lihat transaksi <?php echo e($transaction['id']); ?>" aria-label="Lihat transaksi <?php echo e($transaction['id']); ?>" data-dialog-open="transactionDialog" data-payload="<?php echo e(json_encode(array('id_pembayaran' => $transaction['id'], 'booking' => $transaction['booking'], 'pengguna' => $transaction['user'], 'lapangan' => $transaction['field'], 'metode' => $transaction['method'], 'jumlah' => $transaction['amount'], 'status' => $transaction['rawStatus']))); ?>">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination admin-pagination-spread">
            <span>Menampilkan <?php echo e(count($transactions)); ?> data</span>
        </div>
    </article>
</div>

<dialog class="admin-dialog" id="transactionDialog">
    <div class="admin-dialog-head"><h2>Detail Transaksi</h2><button class="admin-dialog-close" type="button" data-dialog-close>&times;</button></div>
    <form class="admin-dialog-form" action="<?php echo e(app_url('admin/transaksi/update')); ?>" method="post"><input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>"><input type="hidden" name="id_pembayaran"><label><span>Booking</span><input name="booking" readonly></label><label><span>Pengguna</span><input name="pengguna" readonly></label><label><span>Lapangan</span><input name="lapangan" readonly></label><label><span>Metode</span><input name="metode" readonly></label><label><span>Jumlah</span><input name="jumlah" readonly></label><label><span>Status</span><select name="status"><option>Pending</option><option>Berhasil</option><option>Gagal</option><option>Refund</option></select></label><div class="admin-dialog-actions"><button type="button" class="admin-secondary-btn" data-dialog-close>Tutup</button><button type="submit" class="btn-primary">Perbarui Status</button></div></form>
</dialog>
