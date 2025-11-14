-- SQL Install for Aplikasi Pengelolaan dan Maintenance Alat Multimedia HM Official

CREATE DATABASE IF NOT EXISTS multimedia_hm DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE multimedia_hm;

-- tabel_user
CREATE TABLE IF NOT EXISTS tabel_user (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  nama_user VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','kepala','staff') NOT NULL DEFAULT 'staff'
);

-- tabel_alat
CREATE TABLE IF NOT EXISTS tabel_alat (
  id_alat INT AUTO_INCREMENT PRIMARY KEY,
  nama_alat VARCHAR(255) NOT NULL,
  jenis VARCHAR(100) NOT NULL,
  kondisi ENUM('baik','rusak ringan','rusak berat') DEFAULT 'baik',
  lokasi VARCHAR(150),
  penanggung_jawab VARCHAR(100),
  tanggal_pembelian DATE
);

-- tabel_maintenance
CREATE TABLE IF NOT EXISTS tabel_maintenance (
  id_maintenance INT AUTO_INCREMENT PRIMARY KEY,
  id_alat INT NOT NULL,
  tanggal DATE,
  jenis_maintenance VARCHAR(150),
  teknisi VARCHAR(150),
  biaya DECIMAL(12,2) DEFAULT 0,
  status ENUM('selesai','belum') DEFAULT 'belum',
  FOREIGN KEY (id_alat) REFERENCES tabel_alat(id_alat) ON DELETE CASCADE
);

-- tabel_pembelian
CREATE TABLE IF NOT EXISTS tabel_pembelian (
  id_pembelian INT AUTO_INCREMENT PRIMARY KEY,
  nama_alat VARCHAR(255) NOT NULL,
  alasan TEXT,
  estimasi_biaya DECIMAL(12,2),
  tanggal_permohonan DATE,
  status ENUM('menunggu','disetujui','ditolak') DEFAULT 'menunggu'
);

-- tabel_pengeluaran
CREATE TABLE IF NOT EXISTS tabel_pengeluaran (
  id_pengeluaran INT AUTO_INCREMENT PRIMARY KEY,
  jenis_pengeluaran ENUM('pembelian','maintenance') NOT NULL,
  nama_alat VARCHAR(255),
  tanggal DATE,
  nominal DECIMAL(14,2) DEFAULT 0,
  keterangan TEXT
);

-- tabel_konten
CREATE TABLE IF NOT EXISTS tabel_konten (
  id_konten INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  jenis ENUM('foto','video','audio','desain') NOT NULL,
  deskripsi TEXT,
  penanggung_jawab VARCHAR(100),
  tanggal_upload DATE,
  file_path VARCHAR(255)
);

-- sample data: users (passwords are MD5 for demo: admin123, staff123)
INSERT INTO tabel_user (nama_user, username, password, role) VALUES
('Admin HM','admin', MD5('admin123'), 'admin'),
('Staff HM','staff', MD5('staff123'), 'staff');

-- sample data: alat (>=5)
INSERT INTO tabel_alat (nama_alat, jenis, kondisi, lokasi, penanggung_jawab, tanggal_pembelian) VALUES
('Canon EOS 80D','kamera','baik','Studio','Andi','2021-02-10'),
('Mixer Yamaha MG10XU','mixer','baik','Ruang Audio','Budi','2020-06-15'),
('Amplifier JBL','amplifier','rusak ringan','Gudang','Cici','2019-11-20'),
('Lighting Par LED','lighting','baik','Panggung','Dewa','2022-01-05'),
('Ultralink U-100','ultralink','rusak berat','Gudang','Eka','2018-09-12');

-- sample maintenance (>=5)
INSERT INTO tabel_maintenance (id_alat, tanggal, jenis_maintenance, teknisi, biaya, status) VALUES
(1,'2022-10-01','Cleaning sensor','Teknisi A',150000,'selesai'),
(2,'2023-01-15','Ganti kabel','Teknisi B',50000,'selesai'),
(3,'2023-04-20','Perbaikan speaker','Teknisi C',300000,'belum'),
(1,'2023-06-10','Update firmware','Teknisi A',0,'selesai'),
(4,'2023-07-05','Ganti LED','Teknisi D',200000,'selesai');

-- sample pembelian (>=5)
INSERT INTO tabel_pembelian (nama_alat, alasan, estimasi_biaya, tanggal_permohonan, status) VALUES
('Kamera Sony A7III','Perlu untuk dokumentasi event',20000000,'2023-08-01','menunggu'),
('Tripod heavy duty','Support untuk kamera berat',1500000,'2023-07-20','disetujui'),
('Microphone condenser','Upgrade kualitas audio',2500000,'2023-06-12','ditolak'),
('Monitor videotron kecil','Testing display','5000000','2023-05-10','menunggu'),
('Kabel XLR premium','Pengganti kabel lama',300000,'2023-09-01','disetujui');

-- sample pengeluaran (>=5)
INSERT INTO tabel_pengeluaran (jenis_pengeluaran, nama_alat, tanggal, nominal, keterangan) VALUES
('maintenance','Canon EOS 80D','2022-10-01',150000,'Cleaning sensor'),
('maintenance','Mixer Yamaha MG10XU','2023-01-15',50000,'Ganti kabel'),
('pembelian','Tripod heavy duty','2023-07-25',1500000,'Pembelian barang'),
('maintenance','Amplifier JBL','2023-04-20',300000,'Perbaikan speaker'),
('pembelian','Kabel XLR premium','2023-09-02',300000,'Pembelian barang');

-- sample konten (>=5) -- file_path left as example
INSERT INTO tabel_konten (judul, jenis, deskripsi, penanggung_jawab, tanggal_upload, file_path) VALUES
('Dokumentasi Seminar','foto','Foto acara seminar','Andi','2023-03-10','uploads/dokumentasi_seminar.jpg'),
('Video Promo','video','Video promosi HM Official','Budi','2023-04-05','uploads/video_promo.mp4'),
('Podcast Episode 1','audio','Pembahasan pertama','Cici','2023-02-20','uploads/podcast1.mp3'),
('Design Poster Event','desain','Poster acara','Dewa','2023-06-01','uploads/poster_event.jpg'),
('Rekaman Lapangan','audio','Rekaman lapangan untuk dokumentasi','Eka','2023-05-15','uploads/rekaman_lap.mp3');
