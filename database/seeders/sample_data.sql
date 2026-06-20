INSERT INTO `metode_pembayaran`
    (`ID_Metode`, `Nama`, `Tipe`, `Biaya_admin`, `Aktif`)
VALUES
    ('COD', 'Bayar di Tempat', 'cash', 0, 1),
    ('QRIS', 'QRIS', 'qris', 0, 1),
    ('TRANSFER', 'Transfer Bank', 'bank', 0, 1)
ON DUPLICATE KEY UPDATE
    `Nama` = VALUES(`Nama`),
    `Tipe` = VALUES(`Tipe`),
    `Biaya_admin` = VALUES(`Biaya_admin`),
    `Aktif` = VALUES(`Aktif`);

-- Akun, profil pemilik, lapangan, dan jadwal sebaiknya dibuat melalui aplikasi
-- agar password selalu di-hash dan seluruh foreign key terisi dengan benar.
