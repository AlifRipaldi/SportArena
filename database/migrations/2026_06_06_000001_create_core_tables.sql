CREATE TABLE IF NOT EXISTS `users` (
    `ID_User` varchar(50) NOT NULL,
    `Nama` varchar(120) NOT NULL,
    `Email` varchar(160) NOT NULL,
    `Password` varchar(255) NOT NULL,
    `Nomor_telepon` varchar(50) NOT NULL,
    `Role` enum('admin', 'pemilik', 'customer') NOT NULL DEFAULT 'customer',
    `Status` varchar(30) NOT NULL DEFAULT 'Aktif',
    `Kota` varchar(100) NULL,
    `Alamat` text NULL,
    `Avatar` varchar(255) NULL,
    `Email_verified_at` datetime NULL,
    `Must_Reset_Password` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`ID_User`),
    UNIQUE KEY `users_email_unique` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pemilik_lapangan` (
    `ID_Pemilik` varchar(50) NOT NULL,
    `ID_User` varchar(50) NOT NULL,
    `nama_usaha` varchar(255) NOT NULL,
    `alamat` varchar(255) NOT NULL,
    `Nomor_identitas` varchar(100) NULL,
    `NPWP` varchar(100) NULL,
    `Status_verifikasi` varchar(30) NOT NULL DEFAULT 'Pending',
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`ID_Pemilik`),
    UNIQUE KEY `pemilik_user_unique` (`ID_User`),
    CONSTRAINT `fk_pemilik_user` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `lapangan` (
    `ID_Lapangan` varchar(50) NOT NULL,
    `Nama_lapangan` varchar(160) NOT NULL,
    `Lokasi` varchar(180) NOT NULL,
    `Jenis_olahraga` varchar(80) NOT NULL,
    `Fasilitas` text NOT NULL,
    `ID_Pemilik` varchar(50) NOT NULL,
    `Harga` int unsigned NOT NULL DEFAULT 0,
    `Status` varchar(30) NOT NULL DEFAULT 'Aktif',
    `Deskripsi` text NULL,
    `Foto` text NULL,
    `Latitude` decimal(10,8) NULL,
    `Longitude` decimal(11,8) NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted_at` datetime NULL,
    PRIMARY KEY (`ID_Lapangan`),
    KEY `lapangan_pemilik_index` (`ID_Pemilik`),
    KEY `lapangan_status_index` (`Status`),
    CONSTRAINT `fk_lapangan_pemilik` FOREIGN KEY (`ID_Pemilik`) REFERENCES `pemilik_lapangan` (`ID_Pemilik`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `jadwal` (
    `ID_Jadwal` varchar(50) NOT NULL,
    `ID_Lapangan` varchar(50) NOT NULL,
    `Tanggal` date NOT NULL,
    `Jam_Mulai` time NOT NULL,
    `Jam_Selesai` time NOT NULL,
    `Status` varchar(50) NOT NULL DEFAULT 'Available',
    `Harga` int unsigned NOT NULL DEFAULT 0,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`ID_Jadwal`),
    UNIQUE KEY `jadwal_slot_unique` (`ID_Lapangan`, `Tanggal`, `Jam_Mulai`, `Jam_Selesai`),
    KEY `jadwal_status_index` (`Status`),
    CONSTRAINT `fk_jadwal_lapangan` FOREIGN KEY (`ID_Lapangan`) REFERENCES `lapangan` (`ID_Lapangan`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `booking` (
    `ID_Booking` varchar(50) NOT NULL,
    `ID_Jadwal` varchar(50) NOT NULL,
    `ID_User` varchar(50) NOT NULL,
    `Waktu_transaksi` datetime NOT NULL,
    `Total_harga` int unsigned NOT NULL DEFAULT 0,
    `Status` varchar(30) NOT NULL DEFAULT 'Menunggu Pembayaran',
    `Catatan` text NULL,
    `Dibatalkan_pada` datetime NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`ID_Booking`),
    KEY `booking_jadwal_index` (`ID_Jadwal`),
    KEY `booking_user_index` (`ID_User`),
    KEY `booking_status_index` (`Status`),
    CONSTRAINT `fk_booking_jadwal` FOREIGN KEY (`ID_Jadwal`) REFERENCES `jadwal` (`ID_Jadwal`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_booking_user` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `metode_pembayaran` (
    `ID_Metode` varchar(30) NOT NULL,
    `Nama` varchar(100) NOT NULL,
    `Tipe` varchar(30) NOT NULL,
    `Biaya_admin` int unsigned NOT NULL DEFAULT 0,
    `Aktif` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`ID_Metode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pembayaran` (
    `ID_Pembayaran` varchar(50) NOT NULL,
    `ID_Booking` varchar(50) NOT NULL,
    `Jumlah` bigint unsigned NOT NULL DEFAULT 0,
    `Keterangan` varchar(255) NULL,
    `Metode` varchar(50) NOT NULL,
    `Status` varchar(30) NOT NULL DEFAULT 'Pending',
    `Referensi` varchar(100) NULL,
    `Bukti` varchar(255) NULL,
    `Waktu_pembayaran` datetime NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`ID_Pembayaran`),
    UNIQUE KEY `pembayaran_referensi_unique` (`Referensi`),
    KEY `pembayaran_booking_index` (`ID_Booking`),
    KEY `pembayaran_status_index` (`Status`),
    CONSTRAINT `fk_pembayaran_booking` FOREIGN KEY (`ID_Booking`) REFERENCES `booking` (`ID_Booking`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `review` (
    `ID_Review` varchar(50) NOT NULL,
    `ID_User` varchar(50) NOT NULL,
    `ID_Lapangan` varchar(50) NOT NULL,
    `ID_Booking` varchar(50) NULL,
    `Rating` tinyint unsigned NOT NULL,
    `Komentar` text NOT NULL,
    `Balasan` text NULL,
    `Status` varchar(30) NOT NULL DEFAULT 'Tampil',
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`ID_Review`),
    UNIQUE KEY `review_booking_unique` (`ID_Booking`),
    KEY `review_user_index` (`ID_User`),
    KEY `review_lapangan_index` (`ID_Lapangan`),
    CONSTRAINT `review_rating_check` CHECK (`Rating` BETWEEN 1 AND 5),
    CONSTRAINT `fk_review_user` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_review_lapangan` FOREIGN KEY (`ID_Lapangan`) REFERENCES `lapangan` (`ID_Lapangan`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_review_booking` FOREIGN KEY (`ID_Booking`) REFERENCES `booking` (`ID_Booking`)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `favorit` (
    `ID_User` varchar(50) NOT NULL,
    `ID_Lapangan` varchar(50) NOT NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`ID_User`, `ID_Lapangan`),
    CONSTRAINT `fk_favorit_user` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_favorit_lapangan` FOREIGN KEY (`ID_Lapangan`) REFERENCES `lapangan` (`ID_Lapangan`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_settings` (
    `ID_User` varchar(50) NOT NULL,
    `Theme_mode` varchar(20) NOT NULL DEFAULT 'dark',
    `Bahasa` varchar(10) NOT NULL DEFAULT 'id',
    `Notifikasi_booking` tinyint(1) NOT NULL DEFAULT 1,
    `Notifikasi_jadwal` tinyint(1) NOT NULL DEFAULT 1,
    `Notifikasi_promo` tinyint(1) NOT NULL DEFAULT 0,
    `Kota_favorit` varchar(100) NULL,
    `Olahraga_favorit` varchar(80) NULL,
    `Radius_pencarian` int unsigned NOT NULL DEFAULT 10,
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`ID_User`),
    CONSTRAINT `fk_user_settings_user` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `notifikasi` (
    `ID_Notifikasi` bigint unsigned NOT NULL AUTO_INCREMENT,
    `ID_User` varchar(50) NOT NULL,
    `Judul` varchar(160) NOT NULL,
    `Pesan` text NOT NULL,
    `Tipe` varchar(30) NOT NULL DEFAULT 'info',
    `Link` varchar(255) NULL,
    `Dibaca_pada` datetime NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`ID_Notifikasi`),
    KEY `notifikasi_user_index` (`ID_User`),
    CONSTRAINT `fk_notifikasi_user` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `jam_operasional` (
    `ID_Operasional` bigint unsigned NOT NULL AUTO_INCREMENT,
    `ID_Lapangan` varchar(50) NOT NULL,
    `Hari` tinyint unsigned NOT NULL,
    `Jam_buka` time NULL,
    `Jam_tutup` time NULL,
    `Tutup` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`ID_Operasional`),
    UNIQUE KEY `operasional_hari_unique` (`ID_Lapangan`, `Hari`),
    CONSTRAINT `operasional_hari_check` CHECK (`Hari` BETWEEN 0 AND 6),
    CONSTRAINT `fk_operasional_lapangan` FOREIGN KEY (`ID_Lapangan`) REFERENCES `lapangan` (`ID_Lapangan`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `rekening_pemilik` (
    `ID_Rekening` bigint unsigned NOT NULL AUTO_INCREMENT,
    `ID_Pemilik` varchar(50) NOT NULL,
    `Nama_bank` varchar(100) NOT NULL,
    `Nomor_rekening` varchar(100) NOT NULL,
    `Nama_pemilik` varchar(160) NOT NULL,
    `Utama` tinyint(1) NOT NULL DEFAULT 0,
    `Status` varchar(30) NOT NULL DEFAULT 'Aktif',
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`ID_Rekening`),
    KEY `rekening_pemilik_index` (`ID_Pemilik`),
    CONSTRAINT `fk_rekening_pemilik` FOREIGN KEY (`ID_Pemilik`) REFERENCES `pemilik_lapangan` (`ID_Pemilik`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
