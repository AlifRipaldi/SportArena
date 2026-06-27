<?php
$typeOptions = isset($transactionTypeOptions) ? $transactionTypeOptions : array('semua' => 'Semua Tipe', 'booking' => 'Booking Lapangan', 'refund' => 'Refund', 'pencairan' => 'Pencairan');
$methodOptions = isset($transactionMethodOptions) ? $transactionMethodOptions : array('semua' => 'Semua Metode', 'qris' => 'QRIS', 'dana' => 'DANA', 'ovo' => 'OVO', 'bank' => 'Transfer Bank');
$statusOptions = isset($transactionStatusOptions) ? $transactionStatusOptions : array('semua' => 'Semua Status', 'selesai' => 'Selesai', 'menunggu' => 'Menunggu', 'dibatalkan' => 'Dibatalkan');
$transactionFilters = isset($transactionFilters) ? $transactionFilters : array(
    'startDate' => date('Y-m-01'),
    'endDate' => date('Y-m-t'),
    'dateLabel' => date('d/m/Y', strtotime('first day of this month')) . ' - ' . date('d/m/Y', strtotime('last day of this month')),
    'type' => 'semua',
    'method' => 'semua',
    'status' => 'semua',
    'search' => '',
    'perPage' => 10,
);
$transactionPagination = isset($transactionPagination) ? $transactionPagination : array(
    'currentPage' => 1,
    'totalPages' => 1,
    'total' => count($transactions),
    'firstItem' => count($transactions) > 0 ? 1 : 0,
    'lastItem' => count($transactions),
    'perPage' => isset($transactionFilters['perPage']) ? $transactionFilters['perPage'] : 10,
);
$transactionTotal = isset($transactionTotal) ? (int) $transactionTotal : count($transactions);
$transactionAllTotal = isset($transactionAllTotal) ? (int) $transactionAllTotal : $transactionTotal;
$transactionQuery = function (array $params) use ($transactionFilters) {
    $query = array_merge(array(
        'tanggal_mulai' => $transactionFilters['startDate'],
        'tanggal_selesai' => $transactionFilters['endDate'],
        'tipe' => $transactionFilters['type'],
        'metode' => $transactionFilters['method'],
        'status' => $transactionFilters['status'],
        'q' => $transactionFilters['search'],
        'per_page' => $transactionFilters['perPage'],
    ), $params);

    foreach ($query as $key => $value) {
        if ($value === null || $value === '') {
            unset($query[$key]);
        }
    }

    return http_build_query($query);
};
$transactionUrl = function (array $params) use ($transactionQuery) {
    return app_url('pemilik/transaksi?' . $transactionQuery($params));
};
?>

<section class="owner-transaksi-page">
    <div class="owner-transaksi-hero">
        <div>
            <h1>Lihat Semua Transaksi</h1>
            <p>Daftar semua transaksi pembayaran yang terjadi di lapangan Anda.</p>
        </div>
    </div>

    <form class="owner-transaksi-filter-panel" id="ownerTransaksiFilterForm" action="<?php echo e(app_url('pemilik/transaksi')); ?>" method="get" aria-label="Filter transaksi" data-owner-transaction-filter>
        <div class="owner-transaksi-field owner-transaksi-date-field">
            <span>Tanggal</span>
            <details class="owner-transaksi-date-picker" data-owner-transaction-date-picker>
                <summary>
                    <span><?php echo e($transactionFilters['dateLabel']); ?></span>
                    <i class="fa-regular fa-calendar-days"></i>
                </summary>

                <div class="owner-transaksi-date-panel">
                    <label>
                        <span>Dari tanggal</span>
                        <input type="date" name="tanggal_mulai" value="<?php echo e($transactionFilters['startDate']); ?>">
                    </label>

                    <label>
                        <span>Sampai tanggal</span>
                        <input type="date" name="tanggal_selesai" value="<?php echo e($transactionFilters['endDate']); ?>">
                    </label>

                    <div class="owner-transaksi-date-actions">
                        <a href="<?php echo e(app_url('pemilik/transaksi')); ?>">Reset</a>
                        <button type="submit">Terapkan</button>
                    </div>
                </div>
            </details>
        </div>

        <label class="owner-transaksi-field">
            <span>Tipe Transaksi</span>
            <select name="tipe" aria-label="Tipe transaksi" data-owner-transaction-autosubmit>
                <?php foreach ($typeOptions as $value => $label): ?>
                    <option value="<?php echo e($value); ?>" <?php echo $transactionFilters['type'] === $value ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="owner-transaksi-field">
            <span>Metode Pembayaran</span>
            <select name="metode" aria-label="Metode pembayaran" data-owner-transaction-autosubmit>
                <?php foreach ($methodOptions as $value => $label): ?>
                    <option value="<?php echo e($value); ?>" <?php echo $transactionFilters['method'] === $value ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="owner-transaksi-field">
            <span>Status Pembayaran</span>
            <select name="status" aria-label="Status pembayaran" data-owner-transaction-autosubmit>
                <?php foreach ($statusOptions as $value => $label): ?>
                    <option value="<?php echo e($value); ?>" <?php echo $transactionFilters['status'] === $value ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="owner-transaksi-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" name="q" value="<?php echo e($transactionFilters['search']); ?>" placeholder="Cari transaksi / order ID..." aria-label="Cari transaksi atau order ID">
        </label>

        <button class="owner-transaksi-export" type="submit" formaction="<?php echo e(app_url('pemilik/transaksi/export')); ?>">
            <i class="fa-solid fa-download"></i>
            <span>Export</span>
        </button>
    </form>

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
                        <th>Tipe</th>
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
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="11">
                                <div class="owner-transaksi-empty">
                                    <i class="fa-regular fa-calendar-xmark"></i>
                                    <strong>Tidak ada transaksi ditemukan</strong>
                                    <span>Coba ubah tanggal, status, metode pembayaran, atau kata kunci pencarian.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $index => $transaction): ?>
                            <?php $detailPayload = json_encode($transaction, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
                            <tr>
                                <td><?php echo e((int) $transactionPagination['firstItem'] + $index); ?></td>
                                <td><?php echo e($transaction['orderId']); ?></td>
                                <td><?php echo e($transaction['type']); ?></td>
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
                                    <button class="owner-transaksi-detail" type="button" data-owner-transaction-detail="<?php echo e($detailPayload); ?>">Detail</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="owner-transaksi-footer">
            <p>
                <?php if ((int) $transactionPagination['total'] > 0): ?>
                    Menampilkan <?php echo e($transactionPagination['firstItem']); ?> - <?php echo e($transactionPagination['lastItem']); ?> dari <?php echo e($transactionTotal); ?> transaksi
                <?php else: ?>
                    Tidak ada transaksi dari total <?php echo e($transactionAllTotal); ?> data
                <?php endif; ?>
            </p>

            <nav class="owner-transaksi-pagination" aria-label="Paginasi transaksi">
                <?php if ((int) $transactionPagination['currentPage'] > 1): ?>
                    <a href="<?php echo e($transactionUrl(array('page' => (int) $transactionPagination['currentPage'] - 1))); ?>" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></a>
                <?php else: ?>
                    <span class="disabled" aria-disabled="true" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></span>
                <?php endif; ?>

                <?php for ($pageNumber = 1; $pageNumber <= (int) $transactionPagination['totalPages']; $pageNumber++): ?>
                    <?php if ($pageNumber === (int) $transactionPagination['currentPage']): ?>
                        <span class="active" aria-current="page"><?php echo e($pageNumber); ?></span>
                    <?php else: ?>
                        <a href="<?php echo e($transactionUrl(array('page' => $pageNumber))); ?>"><?php echo e($pageNumber); ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ((int) $transactionPagination['currentPage'] < (int) $transactionPagination['totalPages']): ?>
                    <a href="<?php echo e($transactionUrl(array('page' => (int) $transactionPagination['currentPage'] + 1))); ?>" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></a>
                <?php else: ?>
                    <span class="disabled" aria-disabled="true" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></span>
                <?php endif; ?>
            </nav>

            <label class="owner-transaksi-page-size">
                <select name="per_page" form="ownerTransaksiFilterForm" aria-label="Jumlah transaksi per halaman" data-owner-transaction-autosubmit>
                    <?php foreach (array(10, 25, 50) as $pageSize): ?>
                        <option value="<?php echo e($pageSize); ?>" <?php echo (int) $transactionFilters['perPage'] === $pageSize ? 'selected' : ''; ?>><?php echo e($pageSize); ?> / halaman</option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </article>

    <div class="owner-transaction-modal" data-owner-transaction-modal hidden>
        <div class="owner-transaction-modal-backdrop" data-owner-transaction-close></div>

        <section class="owner-transaction-dialog" role="dialog" aria-modal="true" aria-labelledby="ownerTransactionDetailTitle">
            <header class="owner-transaction-dialog-head">
                <div>
                    <h2 id="ownerTransactionDetailTitle">Detail Transaksi</h2>
                    <p data-owner-transaction-field="orderId">ORD-000000-000</p>
                </div>

                <button type="button" data-owner-transaction-close aria-label="Tutup detail transaksi">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </header>

            <dl class="owner-transaction-detail-list">
                <div>
                    <dt>Tipe Transaksi</dt>
                    <dd data-owner-transaction-field="type">-</dd>
                </div>
                <div>
                    <dt>Pelanggan</dt>
                    <dd data-owner-transaction-field="customer">-</dd>
                </div>
                <div>
                    <dt>No HP</dt>
                    <dd data-owner-transaction-field="phone">-</dd>
                </div>
                <div>
                    <dt>Lapangan</dt>
                    <dd data-owner-transaction-field="field">-</dd>
                </div>
                <div>
                    <dt>Tanggal Transaksi</dt>
                    <dd><span data-owner-transaction-field="date">-</span> <small data-owner-transaction-field="time"></small></dd>
                </div>
                <div>
                    <dt>Waktu Booking</dt>
                    <dd><span data-owner-transaction-field="bookingDate">-</span> <small data-owner-transaction-field="bookingTime"></small></dd>
                </div>
                <div>
                    <dt>Metode Pembayaran</dt>
                    <dd data-owner-transaction-field="method">-</dd>
                </div>
                <div>
                    <dt>Total</dt>
                    <dd data-owner-transaction-field="total">-</dd>
                </div>
                <div>
                    <dt>Status</dt>
                    <dd><span class="owner-transaksi-status" data-owner-transaction-status>-</span></dd>
                </div>
            </dl>
        </section>
    </div>
</section>

<script>
(function () {
    var form = document.querySelector('[data-owner-transaction-filter]');
    var autoSubmitControls = document.querySelectorAll('[data-owner-transaction-autosubmit]');
    var datePicker = document.querySelector('[data-owner-transaction-date-picker]');
    var searchInput = form ? form.querySelector('input[name="q"]') : null;
    var topbarSearch = document.querySelector('.owner-topbar-search input[type="search"]');

    function submitFilters() {
        if (!form) {
            return;
        }

        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
            return;
        }

        form.submit();
    }

    Array.prototype.forEach.call(autoSubmitControls, function (control) {
        control.addEventListener('change', submitFilters);
    });

    if (datePicker) {
        document.addEventListener('click', function (event) {
            if (datePicker.open && !datePicker.contains(event.target)) {
                datePicker.open = false;
            }
        });
    }

    if (topbarSearch && searchInput) {
        topbarSearch.value = searchInput.value;
        topbarSearch.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') {
                return;
            }

            event.preventDefault();
            searchInput.value = topbarSearch.value;
            submitFilters();
        });
        topbarSearch.addEventListener('search', function () {
            searchInput.value = topbarSearch.value;
            submitFilters();
        });
    }

    var modal = document.querySelector('[data-owner-transaction-modal]');

    if (!modal) {
        return;
    }

    var closeButtons = modal.querySelectorAll('[data-owner-transaction-close]');
    var statusBadge = modal.querySelector('[data-owner-transaction-status]');
    var fieldNodes = modal.querySelectorAll('[data-owner-transaction-field]');

    function setField(name, value) {
        Array.prototype.forEach.call(fieldNodes, function (node) {
            if (node.dataset.ownerTransactionField === name) {
                node.textContent = value || '-';
            }
        });
    }

    function openDetail(transaction) {
        setField('orderId', transaction.orderId);
        setField('type', transaction.type);
        setField('customer', transaction.customer);
        setField('phone', transaction.phone);
        setField('field', transaction.field);
        setField('date', transaction.date);
        setField('time', transaction.time);
        setField('bookingDate', transaction.bookingDate);
        setField('bookingTime', transaction.bookingTime);
        setField('method', transaction.method);
        setField('total', transaction.total);

        if (statusBadge) {
            statusBadge.className = 'owner-transaksi-status ' + (transaction.statusClass || '');
            statusBadge.textContent = transaction.status || '-';
        }

        modal.hidden = false;
        document.body.classList.add('owner-transaction-modal-open');
    }

    function closeDetail() {
        modal.hidden = true;
        document.body.classList.remove('owner-transaction-modal-open');
    }

    document.querySelectorAll('[data-owner-transaction-detail]').forEach(function (button) {
        button.addEventListener('click', function () {
            var payload = button.getAttribute('data-owner-transaction-detail');

            try {
                openDetail(JSON.parse(payload));
            } catch (error) {
                return;
            }
        });
    });

    Array.prototype.forEach.call(closeButtons, function (button) {
        button.addEventListener('click', closeDetail);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.hidden) {
            closeDetail();
        }
    });
})();
</script>
