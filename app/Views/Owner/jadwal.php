<?php
$statusTabs = array('Semua', 'Aktif', 'Pending', 'Selesai', 'Dibatalkan');
$selectedDate = isset($selectedDate) ? $selectedDate : '16 Juni 2025';
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
            <?php foreach ($statusTabs as $index => $tab): ?>
                <button class="<?php echo $index === 0 ? 'active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                    <?php echo e($tab); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="owner-jadwal-filter-actions">
            <button class="owner-jadwal-filter-btn owner-jadwal-date" type="button">
                <i class="fa-regular fa-calendar"></i>
                <span><?php echo e($selectedDate); ?></span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>
            <button class="owner-jadwal-filter-btn" type="button">
                <i class="fa-solid fa-filter"></i>
                <span>Filter</span>
            </button>
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
                    <?php foreach ($schedule as $index => $booking): ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
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
                </tbody>
            </table>
        </div>
    </article>

    <div class="owner-jadwal-footer">
        <p>Menampilkan 1 - 7 dari 25 data</p>
        <nav class="owner-jadwal-pagination" aria-label="Paginasi jadwal booking">
            <button class="active" type="button" aria-current="page">1</button>
            <button type="button">2</button>
            <button type="button">3</button>
            <button type="button">4</button>
            <button type="button" aria-label="Halaman berikutnya">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </nav>
    </div>
</section>
