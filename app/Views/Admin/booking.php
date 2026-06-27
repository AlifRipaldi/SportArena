<?php
// Admin Booking Management Page
$adminMonthNames = array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember');
$currentAdminMonth = $adminMonthNames[(int) date('n')] . ' ' . date('Y');
?>

<section class="admin-hero">
    <div>
        <h1>Manajemen Booking</h1>
        <p>Kelola dan pantau semua booking lapangan</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary" type="button" data-dialog-open="bookingCreateDialog"><i class="fa-solid fa-plus"></i> Booking Baru</button>
        <a class="admin-secondary-btn" href="<?php echo e(app_url('admin/export/booking')); ?>"><i class="fa-solid fa-download"></i> Export</a>
    </div>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar" data-admin-filter="#adminBookingRows tr">
        <input type="search" placeholder="Cari booking..." class="admin-search-input">
        <select class="admin-filter-select">
            <option value="">Status: Semua</option>
            <option value="Aktif">Status: Aktif</option>
            <option value="Selesai">Status: Selesai</option>
            <option value="Pending">Status: Pending</option>
            <option value="Dibatalkan">Status: Dibatalkan</option>
        </select>
        <select class="admin-filter-select">
            <option value="">Tanggal: Semua</option>
            <option value="<?php echo e($currentAdminMonth); ?>">Bulan Ini</option>
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
                <tbody id="adminBookingRows">
                    <?php foreach ($recentBookings as $booking): ?>
                        <tr data-filter-text="<?php echo e(implode(' ', array($booking['code'], $booking['user'], $booking['field'], $booking['date'], $booking['status']))); ?>">
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
                                    <button class="btn-icon" type="button" title="Edit" data-dialog-open="bookingEditDialog" data-payload="<?php echo e(json_encode(array('id_booking' => $booking['id'], 'status' => $booking['rawStatus']))); ?>"><i class="fa-solid fa-pen"></i></button>
                                    <form class="admin-inline-form" action="<?php echo e(app_url('admin/booking/hapus')); ?>" method="post" data-confirm="Batalkan booking <?php echo e($booking['code']); ?>?">
                                        <input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>">
                                        <input type="hidden" name="id_booking" value="<?php echo e($booking['id']); ?>">
                                        <button class="btn-icon danger" type="submit" title="Batalkan"><i class="fa-solid fa-ban"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            <span>Menampilkan <?php echo e(count($recentBookings)); ?> booking</span>
        </div>
    </article>
</div>

<dialog class="admin-dialog" id="bookingCreateDialog">
    <div class="admin-dialog-head"><h2>Booking Baru</h2><button class="admin-dialog-close" type="button" data-dialog-close>&times;</button></div>
    <form class="admin-dialog-form" action="<?php echo e(app_url('admin/booking/tambah')); ?>" method="post">
        <input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>">
        <label class="full"><span>Customer</span><select name="id_user" required><option value="">Pilih customer</option><?php foreach ($bookingCustomers as $customer): ?><option value="<?php echo e($customer['id']); ?>"><?php echo e($customer['name']); ?></option><?php endforeach; ?></select></label>
        <label class="full"><span>Jadwal tersedia</span><select name="id_jadwal" required><option value="">Pilih jadwal</option><?php foreach ($availableSchedules as $schedule): ?><option value="<?php echo e($schedule['id']); ?>"><?php echo e($schedule['field'] . ' — ' . $schedule['date'] . ' ' . substr($schedule['start'], 0, 5) . '-' . substr($schedule['end'], 0, 5) . ' (Rp' . number_format($schedule['price'], 0, ',', '.') . ')'); ?></option><?php endforeach; ?></select></label>
        <label class="full"><span>Catatan (opsional)</span><textarea name="catatan"></textarea></label>
        <div class="admin-dialog-actions"><button type="button" class="admin-secondary-btn" data-dialog-close>Batal</button><button type="submit" class="btn-primary">Simpan Booking</button></div>
    </form>
</dialog>

<dialog class="admin-dialog" id="bookingEditDialog">
    <div class="admin-dialog-head"><h2>Ubah Status Booking</h2><button class="admin-dialog-close" type="button" data-dialog-close>&times;</button></div>
    <form class="admin-dialog-form" action="<?php echo e(app_url('admin/booking/update')); ?>" method="post">
        <input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>"><input type="hidden" name="id_booking">
        <label class="full"><span>Status</span><select name="status" required><option>Menunggu Pembayaran</option><option>Aktif</option><option>Selesai</option><option>Dibatalkan</option></select></label>
        <div class="admin-dialog-actions"><button type="button" class="admin-secondary-btn" data-dialog-close>Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
    </form>
</dialog>

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
