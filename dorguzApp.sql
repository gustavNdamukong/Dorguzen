-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Gegenereerd op: 12 aug 2020 om 23:28
-- Serverversie: 5.7.26
-- PHP-versie: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dorguzapp`
--
CREATE DATABASE IF NOT EXISTS `dorguzapp` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `dorguzapp`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `baseSettings`
--

CREATE TABLE `baseSettings` (
  `settings_id` int(11) NOT NULL,
  `settings_name` varchar(300) NOT NULL,
  `settings_value` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `baseSettings`
--

INSERT INTO `baseSettings` (`settings_id`, `settings_name`, `settings_value`) VALUES
(1, 'show_brand_slider', 'true'),
(2, 'brand_slider_source', 'gallery'),
(3, 'default_language', 'french'),
(4, 'app_color_theme', 'dark-blue');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `contactformmessage`
--

CREATE TABLE `contactformmessage` (
  `contactformmessage_id` int(11) NOT NULL,
  `contactformmessage_name` varchar(50) NOT NULL,
  `contactformmessage_email` varchar(50) DEFAULT NULL,
  `contactformmessage_phone` varchar(50) DEFAULT NULL,
  `contactformmessage_message` varchar(1000) NOT NULL,
  `contactformmessage_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `contactformmessage`
--

INSERT INTO `contactformmessage` (`contactformmessage_id`, `contactformmessage_name`, `contactformmessage_email`, `contactformmessage_phone`, `contactformmessage_message`, `contactformmessage_date`) VALUES
(2, 'Gustav der Damme', 'gustavdd@yahoo.co.uk', '+447507897969', 'gegegegegeg\r\ngegegegeggege', '2020-07-05 13:30:10'),
(3, 'Gustav der Damme', 'gustavdd@yahoo.co.uk', '+447507897969', 'gegegegegeg\r\ngegegegeggege', '2020-07-05 13:31:06'),
(4, 'Gustav der Damme', 'gustavdd@yahoo.co.uk', '+447507897969', 'gegegegegeg\r\ngegegegeggege', '2020-07-05 13:33:13'),
(7, 'Cheboy', 'gustavdd@yahoo.co.uk', '07507897969', 'A fully responsive website and and a mobile app for our business.', '2020-07-07 17:09:53'),
(9, 'Gustav Ndamukong', NULL, '07507897969', 'tdtduufuuutduduyduytd', '2020-07-10 00:18:28'),
(10, 'Gustav Ndamukong', NULL, '07507897969', 'brbrhrhhththththttjjyjyj', '2020-07-10 00:19:12'),
(11, 'Gustav Ndamukong', 'gustavdd@yahoo.co.uk', '07507897969', 'eggeeteetetet', '2020-07-10 00:20:03'),
(23, 'Gustav Ndamukong', 'gustavdd@yahoo.co.uk', '07507897969', 'testing again very important', '2020-07-16 18:13:42'),
(36, 'Gustav Ndamukong', 'gustavdd@yahoo.co.uk', '07507897969', 'ffefefefefefef', '2020-07-16 20:18:44'),
(37, 'Gustav Ndamukong', 'gustavdd@yahoo.co.uk', '07507897969', 'ff33fefefefefefef', '2020-07-16 20:21:29'),
(38, 'Gustav der Damme', 'gustavdd@yahoo.co.uk', '07507897969', 'wdddffffwfwwf', '2020-07-16 20:36:23'),
(39, 'Gustav Ndamukong', 'gustavdd@yahoo.co.uk', '07507897969', 'dwdffwfwwfw', '2020-07-16 20:56:02'),
(40, 'Jimmy gawn', 'gustavdd@yahoo.co.uk', '07507897969', 'Testing if contact form saves on generic save method', '2020-07-27 12:30:18');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `password_reset`
--

CREATE TABLE `password_reset` (
  `password_reset_id` int(11) NOT NULL,
  `password_reset_users_id` int(10) NOT NULL,
  `password_reset_firstname` varchar(50) NOT NULL,
  `password_reset_email` varchar(50) NOT NULL,
  `password_reset_date` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `password_reset_reset_code` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `users_id` int(10) UNSIGNED NOT NULL,
  `users_type` enum('admin','admin_gen') COLLATE utf8_swedish_ci NOT NULL,
  `users_email` varchar(80) COLLATE utf8_swedish_ci NOT NULL,
  `users_pass` blob NOT NULL,
  `users_first_name` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  `users_last_name` varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  `users_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `users_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`users_id`, `users_type`, `users_email`, `users_pass`, `users_first_name`, `users_last_name`, `users_updated`, `users_created`) VALUES
(60, 'admin_gen', 'dorguzen@dorguzen.com', 0xdfc6ba98e0f3778c407e0333d32e2fa4, 'Dorguzen', 'Dorguzen', '2020-07-29 20:56:37', '2019-07-26 10:55:56'),
(62, 'admin', 'john@colon.com', 0xd9c05f47acf76e1d30be210f557ce92a, 'John', 'Colon', '2020-07-26 20:44:53', '2020-07-26 20:22:34');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `baseSettings`
--
ALTER TABLE `baseSettings`
  ADD PRIMARY KEY (`settings_id`);

--
-- Indexen voor tabel `contactformmessage`
--
ALTER TABLE `contactformmessage`
  ADD PRIMARY KEY (`contactformmessage_id`);

--
-- Indexen voor tabel `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`password_reset_id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD UNIQUE KEY `email` (`users_email`) USING BTREE;

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `baseSettings`
--
ALTER TABLE `baseSettings`
  MODIFY `settings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `contactformmessage`
--
ALTER TABLE `contactformmessage`
  MODIFY `contactformmessage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT voor een tabel `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `password_reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
