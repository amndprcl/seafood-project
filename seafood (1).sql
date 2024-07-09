-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Jul 2024 pada 19.28
-- Versi server: 10.4.21-MariaDB
-- Versi PHP: 7.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `seafood`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`) VALUES
(1, 'admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `id` int(20) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `price`, `quantity`, `image`) VALUES
(119, 3, 2, 'Gurame asam manis', 30000, 1, 'Resep Gurame Asam Manis ala Resto - TOPWISATA.jpg'),
(120, 3, 3, 'Udang goreng', 20000, 1, 'Resep Udang Goreng Tepung ala Rumah Makan Seafood.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id` int(20) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
(4, 2, 'taka', 'demo@sitchi.dev', '6737437338', 'halo'),
(5, 1, 'manda', 'Babyowl@bokuto.com', '0899757765', 'heloooo');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(20) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `number` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `total_products` varchar(100) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` date NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`) VALUES
(49, 2, 'taka', '6289975776', 'demo@sitchi.dev', 'Qriss', 'jepang jalan sakura nomor 40', 'Gurame asam manis (30000)', 30000, '2024-06-29', 'success'),
(50, 2, 'taka', '6289975776', 'demo@sitchi.dev', 'Qriss', 'jepang jalan sakura nomor 40', 'Udang goreng (20000)', 20000, '2024-06-29', 'success'),
(51, 2, 'taka', '6289975776', 'demo@sitchi.dev', 'Bank BCA', 'jepang jalan sakura nomor 40', 'Kepiting (40000)', 40000, '2024-06-29', 'success'),
(68, 2, 'taka', '6289776656', 'demo@sitchi.dev', 'Bank BCA', 'jepang jalan sakura nomor 40', 'Gurame asam manis (30000)', 30000, '2024-07-05', 'success'),
(69, 2, 'taka', '6289776656', 'demo@sitchi.dev', 'Qriss', 'jepang jalan sakura nomor 40', 'Gurame bakar (30000)', 30000, '2024-07-05', 'success'),
(70, 3, 'bude kendal', '6289678776', 'rizqiyatuss29@gmail.com', 'Bank BCA', 'cakunk56', 'Kepiting (40000)', 40000, '2024-07-05', 'success'),
(71, 3, 'bude kendal', '6289678776', 'rizqiyatuss29@gmail.com', 'Bank BCA', 'cakunk56', 'Nasi Goreng (15000)', 15000, '2024-07-05', 'success'),
(72, 3, 'bude kendal', '6289678776', 'rizqiyatuss29@gmail.com', 'Bank BCA', 'cakunk56', 'Gurame asam manis (30000), Udang goreng (20000), Cumi goreng (15000), Cah kangkung (12000), Cah taug', 89000, '2024-07-05', 'success'),
(74, 1, 'manda', '6282298293', 'Babyowl@bokuto.com', 'Bank BCA', 'amanda, upinipin blok c nomor 11 jalan ehsan, , , , , 0898975545 - ', 'Gurame asam manis (30000)', 30000, '2024-07-06', 'success'),
(76, 2, 'taka', '6289776656', 'demo@sitchi.dev', 'Qriss', 'jepang jalan sakura nomor 40', 'Cah tauge (12000)', 12000, '2024-07-07', 'success'),
(77, 1, 'manda', '6282298293', 'Babyowl@bokuto.com', 'Bank BCA', 'amanda, upinipin blok c nomor 11 jalan ehsan, , , , , 0898975545 - ', 'Gurame bakar (30000), Gurame asam manis (30000)', 60000, '2024-07-07', 'success'),
(78, 4, 'jaka', '6289959485', 'endogawaranpo@gmail.com', 'Qriss', 'jepang jalan sakura nomor 40', 'Nasi Goreng (15000)', 15000, '2024-07-07', 'success');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `image`) VALUES
(1, 'Gurame bakar', 'menu ikan', 30000, 'Resep Gurame Bakar Untuk Momen Spesial Bersama Teman dan Keluarga.jpg'),
(2, 'Gurame asam manis', 'menu ikan', 30000, 'Resep Gurame Asam Manis ala Resto - TOPWISATA.jpg'),
(3, 'Udang goreng', 'seafood', 20000, 'Resep Udang Goreng Tepung ala Rumah Makan Seafood.jpg'),
(4, 'Nasi Goreng', 'menu nasi', 15000, '10+ Resep Nasi Goreng Rumahan, Rasa Mewah, Coba Pilih.jpeg'),
(5, 'Kepiting', 'seafood', 40000, 'Kepiting Saos Padang__.jpeg'),
(6, 'Capcay', 'menu sayur', 10000, 'Tips Menggoreng Capcay agar Sayuran Matang Sempurna.jpeg'),
(7, 'Cumi goreng', 'seafood', 15000, 'Resep Cumi goreng tepung praktis oleh Susi Agung.jpeg'),
(8, 'Cah kangkung', 'menu sayur', 12000, 'Resep Cah Kangkung Tauco Saus Tiram Enak.jpeg'),
(9, 'Cah tauge', 'menu sayur', 12000, 'Cah Tauge 📍Le Jardin BJB.jpeg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` varchar(10) NOT NULL,
  `password` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `number`, `password`, `address`) VALUES
(1, 'manda', 'Babyowl@bokuto.com', '6282298293', '8cb2237d0679ca88db6464eac60da96345513964', 'amanda, upinipin blok c nomor 11 jalan ehsan, , , , , 0898975545 - '),
(2, 'taka', 'demo@sitchi.dev', '6289776656', '8cb2237d0679ca88db6464eac60da96345513964', 'jepang jalan sakura nomor 40'),
(3, 'bude kendal', 'rizqiyatuss29@gmail.com', '6289678776', '8cb2237d0679ca88db6464eac60da96345513964', 'cakunk56'),
(4, 'jaka', 'endogawaranpo@gmail.com', '6289959485', '8cb2237d0679ca88db6464eac60da96345513964', 'jepang jalan sakura nomor 40');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
