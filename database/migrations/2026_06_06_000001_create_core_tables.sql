CREATE TABLE IF NOT EXISTS `user` (
    `ID_User` varchar(20) NOT NULL,
    `Nama` varchar(120) NOT NULL,
    `Email` varchar(160) NOT NULL,
    `Password` varchar(255) NOT NULL,
    `Nomor_telepon` varchar(20) NOT NULL,
    `Role` varchar(30) NOT NULL DEFAULT 'User',
    PRIMARY KEY (`ID_User`),
    UNIQUE KEY `user_email_unique` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pemilik_lapangan` (
    `ID_Pemilik` varchar(50) NOT NULL,
    `ID_User` varchar(50) NOT NULL,
    `nama_usaha` varchar(255) NOT NULL,
    `alamat` varchar(255) NOT NULL,
    PRIMARY KEY (`ID_Pemilik`),
    KEY `pemilik_lapangan_user_index` (`ID_User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `lapangan` (
    `ID_Lapangan` varchar(50) NOT NULL,
    `Nama_lapangan` varchar(160) NOT NULL,
    `Lokasi` varchar(180) NOT NULL,
    `Jenis_olahraga` varchar(80) NOT NULL,
    `Fasilitas` text NOT NULL,
    `ID_Pemilik` varchar(50) NOT NULL,
    `Harga` int NOT NULL DEFAULT 0,
    `Status` varchar(30) NOT NULL DEFAULT 'Aktif',
    `Deskripsi` text NULL,
    `Foto` text NULL,
    PRIMARY KEY (`ID_Lapangan`),
    KEY `lapangan_pemilik_index` (`ID_Pemilik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `jadwal` (
    `ID_Jadwal` varchar(20) NOT NULL,
    `ID_Lapangan` varchar(20) NOT NULL,
    `Tanggal` date NOT NULL,
    `Jam_mulai` time NOT NULL,
    `Jam_selesai` time NOT NULL,
    `Status` varchar(30) NOT NULL DEFAULT 'Available',
    PRIMARY KEY (`ID_Jadwal`),
    KEY `jadwal_lapangan_index` (`ID_Lapangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `booking` (
    `ID_Booking` varchar(20) NOT NULL,
    `ID_Jadwal` varchar(20) NOT NULL,
    `ID_User` varchar(20) NOT NULL,
    `Waktu_transaksi` datetime NOT NULL,
    `Total_harga` int NOT NULL DEFAULT 0,
    PRIMARY KEY (`ID_Booking`),
    KEY `booking_jadwal_index` (`ID_Jadwal`),
    KEY `booking_user_index` (`ID_User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
