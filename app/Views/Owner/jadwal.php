<?php
$statusTabs = isset($statusTabs) ? $statusTabs : array('Semua', 'Aktif', 'Pending', 'Selesai', 'Dibatalkan');
$selectedStatus = isset($selectedStatus) ? $selectedStatus : 'Semua';
$selectedDate = isset($selectedDate) ? $selectedDate : date('d/m/Y');
$selectedDateValue = isset($selectedDateValue) ? $selectedDateValue : date('Y-m-d');
$schedule = isset($schedule) && is_array($schedule) ? $schedule : array();
$managedFields = isset($managedFields) && is_array($managedFields) ? $managedFields : array();
$pagination = isset($pagination) ? $pagination : array(
    'currentPage' => 1,
    'totalPages' => 1,
    'total' => count($schedule),
    'firstItem' => count($schedule) > 0 ? 1 : 0,
    'lastItem' => count($schedule),
);
$scheduleUrl = function (array $params) use ($selectedStatus, $selectedDateValue) {
    $query = array_merge(array(
        'status' => $selectedStatus,
        'date' => $selectedDateValue,
    ), $params);

    foreach ($query as $key => $value) {
        if ($value === null || $value === '') {
            unset($query[$key]);
        }
    }

    return app_url('pemilik/jadwal?' . http_build_query($query));
};
?>

<section class="owner-jadwal-page">
    <div class="owner-jadwal-hero">
        <div>
            <h1>Jadwal Booking</h1>
            <p>Lihat dan kelola semua jadwal booking lapangan Anda</p>
        </div>
    </div>

    <div class="owner-jadwal-toolbar" aria-label="Filter jadwal booking">
        <div class="owner-jadwal-tabs" role="tablist" aria-label="Status booking">
            <?php foreach ($statusTabs as $tab): ?>
                <?php $isActiveTab = $selectedStatus === $tab; ?>
                <a class="<?php echo $isActiveTab ? 'active' : ''; ?>" href="<?php echo e($scheduleUrl(array('status' => $tab, 'page' => null))); ?>" role="tab" aria-selected="<?php echo $isActiveTab ? 'true' : 'false'; ?>">
                    <?php echo e($tab); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="owner-jadwal-filter-actions">
            <details class="owner-jadwal-dropdown owner-jadwal-filter-dropdown">
                <summary class="owner-jadwal-filter-btn">
                    <i class="fa-solid fa-plus"></i><span>Tambah Slot</span>
                </summary>
                <form class="owner-jadwal-filter-panel" action="<?php echo e(app_url('pemilik/jadwal/tambah')); ?>" method="post">
                    <label><span>Lapangan</span><select name="id_lapangan" required>
                        <option value="">Pilih lapangan</option>
                        <?php foreach ($managedFields as $field): ?><option value="<?php echo e($field['id']); ?>"><?php echo e($field['name']); ?></option><?php endforeach; ?>
                    </select></label>
                    <label><span>Tanggal</span><input type="date" name="tanggal" min="<?php echo e(date('Y-m-d')); ?>" value="<?php echo e($selectedDateValue); ?>" required></label>
                    <label><span>Jam mulai</span><input type="time" name="jam_mulai" required></label>
                    <label><span>Jam selesai</span><input type="time" name="jam_selesai" required></label>
                    <label><span>Harga slot</span><input type="number" name="harga" min="0" step="1000" placeholder="0 = harga lapangan"></label>
                    <div class="owner-jadwal-filter-panel-actions"><button type="submit">Simpan Slot</button></div>
                </form>
            </details>
            <details class="owner-jadwal-dropdown owner-jadwal-date-dropdown">
                <summary class="owner-jadwal-filter-btn owner-jadwal-date">
                    <i class="fa-regular fa-calendar"></i>
                    <span><?php echo e($selectedDate); ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </summary>

                <form class="owner-jadwal-menu owner-jadwal-date-menu" action="<?php echo e(app_url('pemilik/jadwal')); ?>" method="get">
                    <input type="hidden" name="status" value="<?php echo e($selectedStatus); ?>">

                    <label>
                        <span>Pilih tanggal</span>
                        <input type="date" name="date" value="<?php echo e($selectedDateValue); ?>" aria-label="Pilih tanggal jadwal booking">
                    </label>

                    <div class="owner-jadwal-filter-panel-actions">
                        <a href="<?php echo e($scheduleUrl(array('date' => date('Y-m-d'), 'page' => null))); ?>">Reset</a>
                        <button type="submit">Terapkan</button>
                    </div>
                </form>
            </details>

            <details class="owner-jadwal-dropdown owner-jadwal-filter-dropdown">
                <summary class="owner-jadwal-filter-btn">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </summary>

                <form class="owner-jadwal-filter-panel" action="<?php echo e(app_url('pemilik/jadwal')); ?>" method="get">
                    <label>
                        <span>Status</span>
                        <select name="status">
                            <?php foreach ($statusTabs as $tab): ?>
                                <option value="<?php echo e($tab); ?>" <?php echo $selectedStatus === $tab ? 'selected' : ''; ?>><?php echo e($tab); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        <span>Tanggal</span>
                        <input type="date" name="date" value="<?php echo e($selectedDateValue); ?>">
                    </label>

                    <div class="owner-jadwal-filter-panel-actions">
                        <a href="<?php echo e(app_url('pemilik/jadwal')); ?>">Reset</a>
                        <button type="submit">Terapkan</button>
                    </div>
                </form>
            </details>
        </div>
    </div>

    <article class="admin-panel owner-jadwal-panel">
        <div class="admin-table-responsive">
            <table class="admin-table owner-jadwal-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Penyewa</th>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedule)): ?>
                        <tr class="owner-jadwal-empty-row">
                            <td colspan="9">
                                <div class="owner-jadwal-empty">
                                    <i class="fa-regular fa-calendar-xmark"></i>
                                    <strong>Tidak ada jadwal ditemukan</strong>
                                    <span>Ubah status atau tanggal filter untuk melihat booking lain.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedule as $index => $booking): ?>
                            <?php $bookingDetailPayload = json_encode($booking, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>
                            <tr>
                                <td><?php echo e((int) $pagination['firstItem'] + $index); ?></td>
                                <td class="owner-jadwal-name"><?php echo e($booking['tenant']); ?></td>
                                <td><?php echo e($booking['field']); ?></td>
                                <td><?php echo e($booking['date']); ?></td>
                                <td><?php echo e($booking['time']); ?></td>
                                <td><?php echo e($booking['duration']); ?></td>
                                <td>
                                    <span class="admin-badge owner-jadwal-status <?php echo e($booking['statusClass']); ?>">
                                        <?php echo e($booking['status']); ?>
                                    </span>
                                </td>
                                <td class="owner-jadwal-total"><?php echo e($booking['total']); ?></td>
                                <td>
                                    <button class="btn-icon owner-jadwal-view" type="button" aria-label="Lihat detail booking <?php echo e($booking['tenant']); ?>" data-owner-schedule-detail="<?php echo e($bookingDetailPayload); ?>">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </article>

    <div class="owner-jadwal-footer">
        <p>
            <?php if ((int) $pagination['total'] > 0): ?>
                Menampilkan <?php echo e($pagination['firstItem']); ?> - <?php echo e($pagination['lastItem']); ?> dari <?php echo e($pagination['total']); ?> data
            <?php else: ?>
                Tidak ada data untuk filter <?php echo e($selectedStatus); ?> pada <?php echo e($selectedDate); ?>
            <?php endif; ?>
        </p>
        <nav class="owner-jadwal-pagination" aria-label="Paginasi jadwal booking">
            <?php if ((int) $pagination['currentPage'] > 1): ?>
                <a href="<?php echo e($scheduleUrl(array('page' => (int) $pagination['currentPage'] - 1))); ?>" aria-label="Halaman sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            <?php else: ?>
                <span class="disabled" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            <?php endif; ?>

            <?php for ($pageNumber = 1; $pageNumber <= (int) $pagination['totalPages']; $pageNumber++): ?>
                <?php if ($pageNumber === (int) $pagination['currentPage']): ?>
                    <span class="active" aria-current="page"><?php echo e($pageNumber); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($scheduleUrl(array('page' => $pageNumber))); ?>"><?php echo e($pageNumber); ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ((int) $pagination['currentPage'] < (int) $pagination['totalPages']): ?>
                <a href="<?php echo e($scheduleUrl(array('page' => (int) $pagination['currentPage'] + 1))); ?>" aria-label="Halaman berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php else: ?>
                <span class="disabled" aria-disabled="true" aria-label="Halaman berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            <?php endif; ?>
        </nav>
    </div>

    <div class="owner-schedule-modal" data-owner-schedule-modal hidden>
        <div class="owner-schedule-modal-backdrop" data-owner-schedule-close></div>

        <section class="owner-schedule-dialog" role="dialog" aria-modal="true" aria-labelledby="ownerScheduleDetailTitle">
            <header class="owner-schedule-dialog-head">
                <div>
                    <span class="owner-schedule-dialog-icon"><i class="fa-regular fa-calendar-check"></i></span>
                    <div>
                        <h2 id="ownerScheduleDetailTitle">Detail Jadwal</h2>
                        <p data-owner-schedule-field="bookingCode">-</p>
                    </div>
                </div>
                <button type="button" data-owner-schedule-close aria-label="Tutup detail jadwal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </header>

            <div class="owner-schedule-dialog-summary">
                <div>
                    <small>Lapangan</small>
                    <strong data-owner-schedule-field="field">-</strong>
                    <span><i class="fa-regular fa-calendar"></i> <b data-owner-schedule-field="date">-</b></span>
                </div>
                <span class="admin-badge owner-jadwal-status" data-owner-schedule-status>-</span>
            </div>

            <dl class="owner-schedule-detail-list">
                <div>
                    <dt><i class="fa-solid fa-user"></i> Nama Penyewa</dt>
                    <dd data-owner-schedule-field="tenant">-</dd>
                </div>
                <div>
                    <dt><i class="fa-solid fa-phone"></i> Nomor Telepon</dt>
                    <dd data-owner-schedule-field="phone">-</dd>
                </div>
                <div>
                    <dt><i class="fa-regular fa-envelope"></i> Email</dt>
                    <dd data-owner-schedule-field="email">-</dd>
                </div>
                <div>
                    <dt><i class="fa-regular fa-clock"></i> Jam Bermain</dt>
                    <dd data-owner-schedule-field="time">-</dd>
                </div>
                <div>
                    <dt><i class="fa-solid fa-hourglass-half"></i> Durasi</dt>
                    <dd data-owner-schedule-field="duration">-</dd>
                </div>
                <div>
                    <dt><i class="fa-solid fa-money-bill-wave"></i> Total</dt>
                    <dd class="owner-schedule-detail-total" data-owner-schedule-field="total">-</dd>
                </div>
                <div>
                    <dt><i class="fa-solid fa-receipt"></i> Status Pembayaran</dt>
                    <dd data-owner-schedule-field="paymentStatus">-</dd>
                </div>
                <div>
                    <dt><i class="fa-solid fa-hashtag"></i> ID Jadwal</dt>
                    <dd data-owner-schedule-field="scheduleId">-</dd>
                </div>
            </dl>

            <footer class="owner-schedule-dialog-actions">
                <button type="button" data-owner-schedule-close>Tutup</button>
                <a href="<?php echo e(app_url('pemilik/booking')); ?>">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Kelola Booking</span>
                </a>
            </footer>
        </section>
    </div>
</section>

<script>
(function () {
    var dropdowns = document.querySelectorAll('.owner-jadwal-dropdown');

    dropdowns.forEach(function (dropdown) {
        dropdown.addEventListener('toggle', function () {
            if (!dropdown.open) {
                return;
            }

            dropdowns.forEach(function (otherDropdown) {
                if (otherDropdown !== dropdown) {
                    otherDropdown.removeAttribute('open');
                }
            });
        });
    });

    document.addEventListener('click', function (event) {
        dropdowns.forEach(function (dropdown) {
            if (!dropdown.contains(event.target)) {
                dropdown.removeAttribute('open');
            }
        });
    });
}());
</script>

<script>
(function () {
    var modal = document.querySelector('[data-owner-schedule-modal]');

    if (!modal) {
        return;
    }

    var detailButtons = document.querySelectorAll('[data-owner-schedule-detail]');
    var closeButtons = modal.querySelectorAll('[data-owner-schedule-close]');
    var fieldNodes = modal.querySelectorAll('[data-owner-schedule-field]');
    var statusBadge = modal.querySelector('[data-owner-schedule-status]');
    var lastTrigger = null;

    function setField(name, value) {
        fieldNodes.forEach(function (node) {
            if (node.dataset.ownerScheduleField === name) {
                node.textContent = value || '-';
            }
        });
    }

    function openDetail(detail, trigger) {
        lastTrigger = trigger;
        setField('bookingCode', detail.bookingCode === '-' ? 'Slot belum dipesan' : detail.bookingCode);
        setField('scheduleId', detail.scheduleId);
        setField('tenant', detail.tenant);
        setField('phone', detail.phone);
        setField('email', detail.email);
        setField('field', detail.field);
        setField('date', detail.date);
        setField('time', detail.time);
        setField('duration', detail.duration);
        setField('total', detail.total);
        setField('paymentStatus', detail.paymentStatus);

        if (statusBadge) {
            statusBadge.className = 'admin-badge owner-jadwal-status ' + (detail.statusClass || '');
            statusBadge.textContent = detail.status || '-';
        }

        modal.hidden = false;
        document.body.classList.add('owner-schedule-modal-open');
        window.setTimeout(function () {
            var closeButton = modal.querySelector('.owner-schedule-dialog-head [data-owner-schedule-close]');
            if (closeButton) {
                closeButton.focus();
            }
        }, 0);
    }

    function closeDetail() {
        modal.hidden = true;
        document.body.classList.remove('owner-schedule-modal-open');

        if (lastTrigger && lastTrigger.isConnected) {
            lastTrigger.focus();
        }
    }

    detailButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            try {
                openDetail(JSON.parse(button.getAttribute('data-owner-schedule-detail')), button);
            } catch (error) {
                return;
            }
        });
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeDetail);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.hidden) {
            closeDetail();
        }
    });
}());
</script>
