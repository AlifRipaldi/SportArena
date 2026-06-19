<?php
// Admin Booking Management Page
?>

<section class="admin-hero">
    <div>
        <h1>Manajemen Booking</h1>
        <p>Kelola dan pantau semua booking lapangan</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary"><i class="fa-solid fa-plus"></i> Booking Baru</button>
    </div>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar">
        <input type="search" placeholder="Cari booking..." class="admin-search-input">
        <select class="admin-filter-select">
            <option>Status: Semua</option>
            <option>Status: Aktif</option>
            <option>Status: Selesai</option>
            <option>Status: Pending</option>
            <option>Status: Dibatalkan</option>
        </select>
        <select class="admin-filter-select">
            <option>Tanggal: Semua</option>
            <option>Hari Ini</option>
            <option>Minggu Ini</option>
            <option>Bulan Ini</option>
        </select>
    </div>

    <article class="admin-panel admin-full-width">
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="admin-checkbox-all"></th>
                        <th>Kode Booking</th>
                        <th>Customer</th>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td><strong><?php echo e($booking['code']); ?></strong></td>
                            <td>
                                <div class="admin-customer">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($booking['user']); ?>&background=20314a&color=ffffff" alt="">
                                    <span><?php echo e($booking['user']); ?></span>
                                </div>
                            </td>
                            <td><?php echo e($booking['field']); ?></td>
                            <td><?php echo e($booking['date']); ?></td>
                            <td><?php echo e($booking['time']); ?></td>
                            <td><span class="admin-badge <?php echo e($booking['statusClass']); ?>"><?php echo e($booking['status']); ?></span></td>
                            <td><?php echo e($booking['total']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></button>
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
            <span>Halaman 1 dari 5</span>
            <button class="admin-pagination-btn"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </article>
</div>

<style>
.admin-filter-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.admin-search-input,
.admin-filter-select {
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
    color: #f7fbff;
    font-size: 14px;
}

.admin-checkbox-all {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.admin-actions {
    display: flex;
    gap: 6px;
}

.btn-icon {
    padding: 6px 10px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #f7fbff;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-icon:hover {
    background: rgba(123, 229, 125, 0.2);
    border-color: rgba(123, 229, 125, 0.3);
}

.admin-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.admin-pagination-btn {
    padding: 8px 12px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #f7fbff;
    cursor: pointer;
}

.admin-full-width {
    grid-column: 1 / -1;
}
</style>
