<?php
// Owner - Booking Management
?>

<section class="admin-hero">
    <div>
        <h1>Manajemen Booking</h1>
        <p>Kelola semua booking lapangan Anda</p>
    </div>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar">
        <input type="search" placeholder="Cari booking..." class="admin-search-input">
        <select class="admin-filter-select">
            <option>Status: Semua</option>
            <option>Aktif</option>
            <option>Selesai</option>
            <option>Pending</option>
            <option>Dibatalkan</option>
        </select>
        <select class="admin-filter-select">
            <option>Lapangan: Semua</option>
            <?php foreach ($bookings as $booking): ?>
                <?php if (!isset($fields_shown[$booking['field']])): ?>
                    <option><?php echo e($booking['field']); ?></option>
                    <?php $fields_shown[$booking['field']] = true; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <article class="admin-panel admin-full-width">
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Lapangan</th>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong><?php echo e($booking['code']); ?></strong></td>
                            <td><?php echo e($booking['field']); ?></td>
                            <td>
                                <div class="admin-customer">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($booking['user']); ?>&background=20314a&color=ffffff" alt="">
                                    <span><?php echo e($booking['user']); ?></span>
                                </div>
                            </td>
                            <td><?php echo e($booking['date']); ?></td>
                            <td><?php echo e($booking['time']); ?></td>
                            <td><span class="admin-badge <?php echo e($booking['statusClass']); ?>"><?php echo e($booking['status']); ?></span></td>
                            <td><?php echo e($booking['total']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="btn-icon" title="Lihat Detail"><i class="fa-solid fa-eye"></i></button>
                                    <button class="btn-icon" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            <button class="admin-pagination-btn"><i class="fa-solid fa-chevron-left"></i></button>
            <span>Halaman 1 dari 3</span>
            <button class="admin-pagination-btn"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </article>
</div>
