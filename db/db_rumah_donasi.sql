-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 11:19 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_rumah_donasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang_donasi`
--

CREATE TABLE `barang_donasi` (
  `barang_id` int(11) NOT NULL,
  `donatur_id` int(11) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `deskripsi_barang` text DEFAULT NULL,
  `satuan` enum('Unit','Pcs','Pasang','Lainnya') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `kondisi_barang` enum('Baru','Bekas Layak Pakai','Rusak Ringan') NOT NULL,
  `tanggal_donasi` datetime DEFAULT current_timestamp(),
  `status_barang` enum('Tersedia','Sebagian Didistribusikan','Habis') DEFAULT 'Tersedia',
  `foto` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang_donasi`
--

INSERT INTO `barang_donasi` (`barang_id`, `donatur_id`, `nama_barang`, `kategori`, `deskripsi_barang`, `satuan`, `jumlah`, `kondisi_barang`, `tanggal_donasi`, `status_barang`, `foto`) VALUES
(1, 21, 'Novel', 'Buku', 'Sekumpulan novel cerita fiksi', 'Unit', 6, 'Bekas Layak Pakai', '2025-06-25 00:17:06', 'Habis', 'uploads/barang_donasi/685acf8295513_chaozzy-lin-Rsg-wY7pbDY-unsplash.jpg'),
(2, 21, 'Baju Polos', 'Pakaian', 'Baju Kaos Polos Baru', 'Unit', 12, 'Baru', '2025-06-26 02:35:20', 'Sebagian Didistribusikan', 'uploads/barang_donasi/685c416871f5a_towfiqu-barbhuiya-998DibZwpIc-unsplash.jpg'),
(3, 21, 'Buku Tulis', 'Buku', 'Buku tulis', 'Unit', 24, 'Baru', '2025-06-26 04:36:38', 'Tersedia', 'uploads/barang_donasi/685c5dd677c00_chaozzy-lin-Rsg-wY7pbDY-unsplash.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id_pesan` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(75) DEFAULT NULL,
  `pesan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `distribusi_donasi`
--

CREATE TABLE `distribusi_donasi` (
  `distribusi_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `distributor_id` int(11) NOT NULL,
  `tanggal_pengajuan` datetime DEFAULT current_timestamp(),
  `jumlah_disalurkan` int(11) NOT NULL,
  `tanggal_penerimaan` datetime DEFAULT NULL,
  `tanggal_penyaluran` datetime DEFAULT NULL,
  `status_penyaluran` enum('Menunggu','Diproses','Diproses Distributor','Menunggu Konfirmasi Selesai','Selesai','Ditolak') DEFAULT 'Menunggu',
  `bukti_foto_penyaluran` varchar(255) DEFAULT NULL,
  `catatan_distribusi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `distribusi_donasi`
--

INSERT INTO `distribusi_donasi` (`distribusi_id`, `barang_id`, `distributor_id`, `tanggal_pengajuan`, `jumlah_disalurkan`, `tanggal_penerimaan`, `tanggal_penyaluran`, `status_penyaluran`, `bukti_foto_penyaluran`, `catatan_distribusi`) VALUES
(1, 1, 23, '2025-06-25 04:30:02', 1, '2025-06-25 05:34:40', '2025-06-26 02:38:02', 'Selesai', 'uploads/bukti_penyaluran_selesai/1_685b6d11163ef_AI-Generated-Image.png', ''),
(2, 1, 23, '2025-06-25 04:30:02', 4, '2025-06-25 05:02:42', NULL, 'Diproses Distributor', NULL, NULL),
(3, 1, 23, '2025-06-26 02:07:54', 1, NULL, NULL, 'Ditolak', NULL, NULL),
(4, 1, 23, '2025-06-26 02:10:40', 1, NULL, NULL, 'Ditolak', NULL, NULL),
(5, 1, 23, '2025-06-26 02:17:02', 1, NULL, NULL, 'Ditolak', NULL, NULL),
(6, 2, 23, '2025-06-26 02:36:03', 4, '2025-06-26 02:38:53', NULL, 'Diproses Distributor', NULL, NULL),
(7, 1, 23, '2025-06-26 02:39:16', 1, NULL, NULL, 'Diproses', NULL, NULL);

--
-- Triggers `distribusi_donasi`
--
DELIMITER $$
CREATE TRIGGER `after_distribusi_insert` AFTER INSERT ON `distribusi_donasi` FOR EACH ROW BEGIN
    DECLARE total_terdistribusi INT;
    DECLARE total_awal INT;
    DECLARE barang_id_terkait INT;

    SET barang_id_terkait = NEW.barang_id;

    -- Ambil jumlah total awal dari barang donasi
    SELECT jumlah INTO total_awal FROM barang_donasi WHERE barang_id = barang_id_terkait;

    -- Hitung total jumlah yang sudah dialokasikan (bukan 'Menunggu' atau 'Ditolak')
    SELECT IFNULL(SUM(jumlah_disalurkan), 0) INTO total_terdistribusi 
    FROM distribusi_donasi 
    WHERE barang_id = barang_id_terkait AND status_penyaluran NOT IN ('Menunggu', 'Ditolak');

    -- Tentukan status baru dan update tabel barang_donasi
    IF total_terdistribusi >= total_awal THEN
        UPDATE barang_donasi SET status_barang = 'Habis' WHERE barang_id = barang_id_terkait;
    ELSEIF total_terdistribusi > 0 THEN
        UPDATE barang_donasi SET status_barang = 'Sebagian Didistribusikan' WHERE barang_id = barang_id_terkait;
    ELSE
        UPDATE barang_donasi SET status_barang = 'Tersedia' WHERE barang_id = barang_id_terkait;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_distribusi_update` AFTER UPDATE ON `distribusi_donasi` FOR EACH ROW BEGIN
    DECLARE total_terdistribusi INT;
    DECLARE total_awal INT;
    DECLARE barang_id_terkait INT;

    SET barang_id_terkait = NEW.barang_id;

    -- Ambil jumlah total awal dari barang donasi
    SELECT jumlah INTO total_awal FROM barang_donasi WHERE barang_id = barang_id_terkait;

    -- Hitung total jumlah yang sudah dialokasikan (bukan 'Menunggu' atau 'Ditolak')
    SELECT IFNULL(SUM(jumlah_disalurkan), 0) INTO total_terdistribusi 
    FROM distribusi_donasi 
    WHERE barang_id = barang_id_terkait AND status_penyaluran NOT IN ('Menunggu', 'Ditolak');

    -- Tentukan status baru dan update tabel barang_donasi
    IF total_terdistribusi >= total_awal THEN
        UPDATE barang_donasi SET status_barang = 'Habis' WHERE barang_id = barang_id_terkait;
    ELSEIF total_terdistribusi > 0 THEN
        UPDATE barang_donasi SET status_barang = 'Sebagian Didistribusikan' WHERE barang_id = barang_id_terkait;
    ELSE
        UPDATE barang_donasi SET status_barang = 'Tersedia' WHERE barang_id = barang_id_terkait;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `distributor`
--

CREATE TABLE `distributor` (
  `distributor_id` int(11) NOT NULL,
  `jenis_distributor` enum('Yayasan','Organisasi Sosial','Individu Relawan','Lainnya') NOT NULL,
  `alamat` text DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `distributor`
--

INSERT INTO `distributor` (`distributor_id`, `jenis_distributor`, `alamat`, `deskripsi`) VALUES
(23, 'Yayasan', 'Kekalik Jaya, Mataram', 'Yayasan bla bla bla');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(75) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `level_user` enum('Admin','Donatur','Distributor') NOT NULL,
  `tanggal_daftar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `username`, `password`, `telepon`, `level_user`, `tanggal_daftar`) VALUES
(20, 'Admin', 'admin@example.com', 'Admin', '$2y$10$Dty99jFJl2A3KCBoTz3mNeUEP8MxL633OQ9uoslLpAhZ0.pfl/LM6', NULL, 'Admin', '2025-06-24 20:37:11'),
(21, 'synz', 'synz@gmail.com', 'synz', '$2y$10$9fJrrPtqZU0b.Ngp9FwEauwr60lPn0hwQePcIAJNS/lSOHXKWa.4u', '01234', 'Donatur', '2025-06-24 21:46:08'),
(23, 'Yayasan A', 'yayasana@email.com', 'Yayasan_a', '$2y$10$mL38fQQgjmp.gwn7SdpvM.adjj7MA7QaS82X9YGc3OXJWq7TGuSmm', '08192', 'Distributor', '2025-06-24 23:38:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang_donasi`
--
ALTER TABLE `barang_donasi`
  ADD PRIMARY KEY (`barang_id`),
  ADD KEY `donatur_id` (`donatur_id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id_pesan`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `distribusi_donasi`
--
ALTER TABLE `distribusi_donasi`
  ADD PRIMARY KEY (`distribusi_id`),
  ADD KEY `barang_id` (`barang_id`),
  ADD KEY `distributor_id` (`distributor_id`);

--
-- Indexes for table `distributor`
--
ALTER TABLE `distributor`
  ADD KEY `fk_id_distributor` (`distributor_id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang_donasi`
--
ALTER TABLE `barang_donasi`
  MODIFY `barang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id_pesan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `distribusi_donasi`
--
ALTER TABLE `distribusi_donasi`
  MODIFY `distribusi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang_donasi`
--
ALTER TABLE `barang_donasi`
  ADD CONSTRAINT `barang_donasi_ibfk_1` FOREIGN KEY (`donatur_id`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `distribusi_donasi`
--
ALTER TABLE `distribusi_donasi`
  ADD CONSTRAINT `distribusi_donasi_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang_donasi` (`barang_id`),
  ADD CONSTRAINT `distribusi_donasi_ibfk_2` FOREIGN KEY (`distributor_id`) REFERENCES `distributor` (`distributor_id`);

--
-- Constraints for table `distributor`
--
ALTER TABLE `distributor`
  ADD CONSTRAINT `fk_distributor_user` FOREIGN KEY (`distributor_id`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
