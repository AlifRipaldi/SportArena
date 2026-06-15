<?php
// Admin Users Management Page
?>

<section class="admin-hero">
    <div>
        <h1>Manajemen User</h1>
        <p>Kelola semua pengguna dalam sistem Arena Sport</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah User</button>
    </div>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar">
        <input type="search" placeholder="Cari user..." class="admin-search-input">
        <select class="admin-filter-select">
            <option>Role: Semua</option>
            <option>Admin</option>
            <option>Pemilik</option>
            <option>User</option>
        </select>
        <select class="admin-filter-select">
            <option>Status: Semua</option>
            <option>Aktif</option>
            <option>Nonaktif</option>
        </select>
    </div>

    <article class="admin-panel admin-full-width">
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="admin-customer">
                                <img src="https://ui-avatars.com/api/?name=Ahmad+Fauzi&background=20314a&color=ffffff" alt="">
                                <span>Ahmad Fauzi</span>
                            </div>
                        </td>
                        <td>ahmad@email.com</td>
                        <td>081234567890</td>
                        <td><span class="admin-badge success">User</span></td>
                        <td><span class="admin-badge success">Aktif</span></td>
                        <td>15 Mei 2024</td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn-icon" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="admin-customer">
                                <img src="https://ui-avatars.com/api/?name=Siti+Aminah&background=20314a&color=ffffff" alt="">
                                <span>Siti Aminah</span>
                            </div>
                        </td>
                        <td>siti@email.com</td>
                        <td>082345678901</td>
                        <td><span class="admin-badge info">Pemilik</span></td>
                        <td><span class="admin-badge success">Aktif</span></td>
                        <td>10 April 2024</td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn-icon" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="admin-customer">
                                <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=20314a&color=ffffff" alt="">
                                <span>Budi Santoso</span>
                            </div>
                        </td>
                        <td>budi@email.com</td>
                        <td>083456789012</td>
                        <td><span class="admin-badge success">User</span></td>
                        <td><span class="admin-badge success">Aktif</span></td>
                        <td>05 Mei 2024</td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn-icon" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="admin-customer">
                                <img src="https://ui-avatars.com/api/?name=Dinda+Putri&background=20314a&color=ffffff" alt="">
                                <span>Dinda Putri</span>
                            </div>
                        </td>
                        <td>dinda@email.com</td>
                        <td>084567890123</td>
                        <td><span class="admin-badge success">User</span></td>
                        <td><span class="admin-badge warning">Nonaktif</span></td>
                        <td>20 Maret 2024</td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn-icon" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            <button class="admin-pagination-btn"><i class="fa-solid fa-chevron-left"></i></button>
            <span>Halaman 1 dari 10</span>
            <button class="admin-pagination-btn"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </article>
</div>

<style>
.admin-info {
    background: rgba(102, 159, 252, 0.1);
    border: 1px solid rgba(102, 159, 252, 0.3);
}

.admin-info .admin-badge.info {
    background: rgba(102, 159, 252, 0.15);
    color: #66a0ff;
    border-color: rgba(102, 159, 252, 0.3);
}
</style>
