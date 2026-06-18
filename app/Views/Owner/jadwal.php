<?php
$statusTabs = isset($statusTabs) ? $statusTabs : array('Semua', 'Aktif', 'Pending', 'Selesai', 'Dibatalkan');
$selectedStatus = isset($selectedStatus) ? $selectedStatus : 'Semua';
$selectedDate = isset($selectedDate) ? $selectedDate : '16 Juni 2025';
$selectedDateValue = isset($selectedDateValue) ? $selectedDateValue : '2025-06-16';
$schedule = isset($schedule) && is_array($schedule) ? $schedule : array();
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
                        <a href="<?php echo e($scheduleUrl(array('date' => '2025-06-16', 'page' => null))); ?>">Reset</a>
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
                                    <button class="btn-icon owner-jadwal-view" type="button" aria-label="Lihat detail booking <?php echo e($booking['tenant']); ?>">
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
