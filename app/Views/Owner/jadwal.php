<?php
// Owner - Schedule/Jadwal Management
?>

<section class="admin-hero">
    <div>
        <h1>Manajemen Jadwal</h1>
        <p>Atur jam operasional lapangan Anda</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Jadwal</button>
    </div>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar">
        <select class="admin-filter-select">
            <option>Lapangan: Semua</option>
            <option>Futsal A</option>
            <option>Badminton B</option>
            <option>Mini Soccer</option>
            <option>Basketball A</option>
        </select>
        <input type="date" class="admin-filter-select">
    </div>

    <div class="owner-schedule-grid">
        <?php foreach ($schedule as $item): ?>
            <article class="admin-panel owner-schedule-card">
                <div class="owner-schedule-header">
                    <h3><?php echo e($item['lapangan']); ?></h3>
                    <span class="admin-badge success"><?php echo e($item['status']); ?></span>
                </div>
                <div class="owner-schedule-info">
                    <div class="schedule-item">
                        <label>📅 Tanggal</label>
                        <p><?php echo e($item['date']); ?></p>
                    </div>
                    <div class="schedule-item">
                        <label>🕐 Jam Mulai</label>
                        <p><?php echo e($item['jam_mulai']); ?></p>
                    </div>
                    <div class="schedule-item">
                        <label>🕑 Jam Selesai</label>
                        <p><?php echo e($item['jam_selesai']); ?></p>
                    </div>
                </div>
                <div class="owner-schedule-actions">
                    <button class="btn-small"><i class="fa-solid fa-pen"></i> Edit</button>
                    <button class="btn-small danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Alternative Calendar View -->
    <article class="admin-panel admin-full-width" style="margin-top: 20px;">
        <div class="admin-panel-header">
            <h2>Kalender Jadwal</h2>
        </div>
        <div class="owner-calendar">
            <table class="owner-calendar-table">
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Futsal A</th>
                        <th>Badminton B</th>
                        <th>Mini Soccer</th>
                        <th>Basketball A</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10:00 - 11:00</td>
                        <td><span class="schedule-booked">Booked</span></td>
                        <td><span class="schedule-available">Available</span></td>
                        <td><span class="schedule-available">Available</span></td>
                        <td><span class="schedule-maintenance">Maintenance</span></td>
                    </tr>
                    <tr>
                        <td>11:00 - 12:00</td>
                        <td><span class="schedule-available">Available</span></td>
                        <td><span class="schedule-booked">Booked</span></td>
                        <td><span class="schedule-available">Available</span></td>
                        <td><span class="schedule-maintenance">Maintenance</span></td>
                    </tr>
                    <tr>
                        <td>14:00 - 15:00</td>
                        <td><span class="schedule-available">Available</span></td>
                        <td><span class="schedule-available">Available</span></td>
                        <td><span class="schedule-booked">Booked</span></td>
                        <td><span class="schedule-available">Available</span></td>
                    </tr>
                    <tr>
                        <td>17:00 - 18:00</td>
                        <td><span class="schedule-booked">Booked</span></td>
                        <td><span class="schedule-available">Available</span></td>
                        <td><span class="schedule-booked">Booked</span></td>
                        <td><span class="schedule-available">Available</span></td>
                    </tr>
                    <tr>
                        <td>19:00 - 20:00</td>
                        <td><span class="schedule-booked">Booked</span></td>
                        <td><span class="schedule-booked">Booked</span></td>
                        <td><span class="schedule-booked">Booked</span></td>
                        <td><span class="schedule-available">Available</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>
</div>

<style>
.owner-schedule-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.owner-schedule-card {
    padding: 18px;
}

.owner-schedule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.owner-schedule-header h3 {
    margin: 0;
    font-size: 16px;
}

.owner-schedule-info {
    display: grid;
    gap: 12px;
    margin-bottom: 16px;
}

.schedule-item {
    padding: 10px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 8px;
}

.schedule-item label {
    display: block;
    font-size: 12px;
    color: rgba(237, 246, 255, 0.6);
    margin-bottom: 4px;
}

.schedule-item p {
    margin: 0;
    font-size: 14px;
    color: #f7fbff;
    font-weight: 600;
}

.owner-schedule-actions {
    display: flex;
    gap: 8px;
}

.owner-calendar-table {
    width: 100%;
    border-collapse: collapse;
}

.owner-calendar-table thead {
    background: rgba(255, 255, 255, 0.05);
}

.owner-calendar-table th,
.owner-calendar-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.owner-calendar-table th {
    color: rgba(237, 246, 255, 0.8);
    font-weight: 600;
    font-size: 13px;
}

.owner-calendar-table td {
    color: #f7fbff;
}

.schedule-available,
.schedule-booked,
.schedule-maintenance {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.schedule-available {
    background: rgba(123, 229, 125, 0.15);
    color: #9ef185;
}

.schedule-booked {
    background: rgba(102, 159, 252, 0.15);
    color: #66a0ff;
}

.schedule-maintenance {
    background: rgba(255, 193, 7, 0.15);
    color: #ffc107;
}
</style>
