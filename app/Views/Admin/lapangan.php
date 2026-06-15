<?php
// Admin Lapangan (Fields) Management Page
?>

<section class="admin-hero">
    <div>
        <h1>Manajemen Lapangan</h1>
        <p>Kelola semua lapangan olahraga yang tersedia</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Lapangan</button>
    </div>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar">
        <input type="search" placeholder="Cari lapangan..." class="admin-search-input">
        <select class="admin-filter-select">
            <option>Jenis: Semua</option>
            <option>Futsal</option>
            <option>Badminton</option>
            <option>Basketball</option>
            <option>Mini Soccer</option>
        </select>
        <select class="admin-filter-select">
            <option>Status: Semua</option>
            <option>Aktif</option>
            <option>Maintenance</option>
            <option>Nonaktif</option>
        </select>
    </div>

    <div class="admin-lapangan-grid">
        <article class="admin-lapangan-card">
            <div class="admin-lapangan-header">
                <h3>Futsal A</h3>
                <span class="admin-badge success">Aktif</span>
            </div>
            <div class="admin-lapangan-info">
                <p><strong>Jenis:</strong> Futsal</p>
                <p><strong>Lokasi:</strong> Area 1</p>
                <p><strong>Harga:</strong> Rp80.000/jam</p>
                <p><strong>Booking Hari Ini:</strong> 8</p>
                <p><strong>Rating:</strong> ⭐ 4.8 (120 review)</p>
            </div>
            <div class="admin-lapangan-actions">
                <button class="btn-small"><i class="fa-solid fa-pen"></i> Edit</button>
                <button class="btn-small"><i class="fa-solid fa-calendar"></i> Jadwal</button>
                <button class="btn-small danger"><i class="fa-solid fa-trash"></i> Hapus</button>
            </div>
        </article>

        <article class="admin-lapangan-card">
            <div class="admin-lapangan-header">
                <h3>Badminton B</h3>
                <span class="admin-badge success">Aktif</span>
            </div>
            <div class="admin-lapangan-info">
                <p><strong>Jenis:</strong> Badminton</p>
                <p><strong>Lokasi:</strong> Area 2</p>
                <p><strong>Harga:</strong> Rp60.000/jam</p>
                <p><strong>Booking Hari Ini:</strong> 6</p>
                <p><strong>Rating:</strong> ⭐ 4.6 (85 review)</p>
            </div>
            <div class="admin-lapangan-actions">
                <button class="btn-small"><i class="fa-solid fa-pen"></i> Edit</button>
                <button class="btn-small"><i class="fa-solid fa-calendar"></i> Jadwal</button>
                <button class="btn-small danger"><i class="fa-solid fa-trash"></i> Hapus</button>
            </div>
        </article>

        <article class="admin-lapangan-card">
            <div class="admin-lapangan-header">
                <h3>Mini Soccer</h3>
                <span class="admin-badge success">Aktif</span>
            </div>
            <div class="admin-lapangan-info">
                <p><strong>Jenis:</strong> Mini Soccer</p>
                <p><strong>Lokasi:</strong> Area 3</p>
                <p><strong>Harga:</strong> Rp100.000/jam</p>
                <p><strong>Booking Hari Ini:</strong> 5</p>
                <p><strong>Rating:</strong> ⭐ 4.7 (98 review)</p>
            </div>
            <div class="admin-lapangan-actions">
                <button class="btn-small"><i class="fa-solid fa-pen"></i> Edit</button>
                <button class="btn-small"><i class="fa-solid fa-calendar"></i> Jadwal</button>
                <button class="btn-small danger"><i class="fa-solid fa-trash"></i> Hapus</button>
            </div>
        </article>

        <article class="admin-lapangan-card">
            <div class="admin-lapangan-header">
                <h3>Basketball A</h3>
                <span class="admin-badge warning">Maintenance</span>
            </div>
            <div class="admin-lapangan-info">
                <p><strong>Jenis:</strong> Basketball</p>
                <p><strong>Lokasi:</strong> Area 4</p>
                <p><strong>Harga:</strong> Rp70.000/jam</p>
                <p><strong>Booking Hari Ini:</strong> 0</p>
                <p><strong>Rating:</strong> ⭐ 4.4 (45 review)</p>
            </div>
            <div class="admin-lapangan-actions">
                <button class="btn-small"><i class="fa-solid fa-pen"></i> Edit</button>
                <button class="btn-small"><i class="fa-solid fa-calendar"></i> Jadwal</button>
                <button class="btn-small danger"><i class="fa-solid fa-trash"></i> Hapus</button>
            </div>
        </article>
    </div>
</div>

<style>
.admin-lapangan-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.admin-lapangan-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    padding: 20px;
    color: #f7fbff;
}

.admin-lapangan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.admin-lapangan-header h3 {
    margin: 0;
    font-size: 18px;
}

.admin-lapangan-info {
    display: grid;
    gap: 8px;
    margin-bottom: 16px;
    font-size: 14px;
}

.admin-lapangan-info p {
    margin: 0;
    color: rgba(237, 246, 255, 0.8);
}

.admin-lapangan-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-small {
    padding: 8px 12px;
    border-radius: 8px;
    background: rgba(123, 229, 125, 0.1);
    border: 1px solid rgba(123, 229, 125, 0.3);
    color: #9ef185;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
}

.btn-small:hover {
    background: rgba(123, 229, 125, 0.2);
}

.btn-small.danger {
    background: rgba(229, 62, 62, 0.1);
    border-color: rgba(229, 62, 62, 0.3);
    color: #ff9090;
}

.btn-small.danger:hover {
    background: rgba(229, 62, 62, 0.2);
}
</style>
