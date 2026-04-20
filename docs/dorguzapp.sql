-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 20, 2026 at 03:53 AM
-- Server version: 5.7.39
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dorguzapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `baseSettings`
--

CREATE TABLE `baseSettings` (
  `settings_id` int(11) NOT NULL,
  `settings_name` varchar(300) NOT NULL,
  `settings_value` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `baseSettings`
--

INSERT INTO `baseSettings` (`settings_id`, `settings_name`, `settings_value`) VALUES
(1, 'show_brand_slider', 'true'),
(2, 'brand_slider_source', 'assets/images/gallery'),
(4, 'app_color_theme', 'dark-blue');

-- --------------------------------------------------------

--
-- Table structure for table `contactformmessage`
--

CREATE TABLE `contactformmessage` (
  `contactformmessage_id` int(11) NOT NULL,
  `contactformmessage_name` varchar(50) NOT NULL,
  `contactformmessage_email` varchar(50) DEFAULT NULL,
  `contactformmessage_phone` varchar(50) DEFAULT NULL,
  `contactformmessage_message` varchar(1000) NOT NULL,
  `contactformmessage_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dgz_failed_jobs`
--

CREATE TABLE `dgz_failed_jobs` (
  `id` int(11) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `exception_trace` longtext NOT NULL,
  `attempts` int(11) NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dgz_jobs`
--

CREATE TABLE `dgz_jobs` (
  `id` int(11) NOT NULL,
  `queue` varchar(255) NOT NULL DEFAULT 'default',
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `max_attempts` int(11) NOT NULL DEFAULT '3',
  `reserved_at` datetime DEFAULT NULL,
  `available_at` datetime DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dgz_migrations`
--

CREATE TABLE `dgz_migrations` (
  `id` int(11) NOT NULL,
  `migration` varchar(200) NOT NULL,
  `batch` int(11) NOT NULL,
  `applied_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dgz_migration_locks`
--

CREATE TABLE `dgz_migration_locks` (
  `id` int(11) NOT NULL,
  `locked_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dgz_refresh_tokens`
--

CREATE TABLE `dgz_refresh_tokens` (
  `refresh_tokens_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `refresh_token` longtext,
  `refresh_token_expiry` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dgz_refresh_tokens`
--

INSERT INTO `dgz_refresh_tokens` (`refresh_tokens_id`, `user_id`, `refresh_token`, `refresh_token_expiry`) VALUES
(1, 106, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJhdWQiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJpYXQiOjE3NzQ1NzY3NTMsImV4cCI6MTc3NDU4Mzk1MywiZGF0YSI6eyJ1c2VyX2lkIjoxMDZ9fQ.r1ipW9_rslWdt3zRgyzbbvinc7_BXtNSFwtsJWAQ5tw', 1774583953),
(2, 60, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2Rvcmd1emVuLyIsImF1ZCI6Imh0dHA6Ly9sb2NhbGhvc3QvZG9yZ3V6ZW4vIiwiaWF0IjoxNzc2MjY5NDMyLCJleHAiOjE3NzYyNzY2MzIsImRhdGEiOnsidXNlcl9pZCI6NjB9fQ.Kb7azUyU5SvQFqzBzSuJtkDniu8e5hna1AHznKeJjKg', 1776276632);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `logs_id` int(11) NOT NULL,
  `logs_title` varchar(100) NOT NULL,
  `logs_message` text NOT NULL,
  `context_json` text,
  `logs_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
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
-- Table structure for table `seo`
--

CREATE TABLE `seo` (
  `seo_id` int(11) NOT NULL,
  `seo_page_name` varchar(50) NOT NULL,
  `seo_meta_title_en` varchar(60) NOT NULL,
  `seo_meta_title_fre` varchar(60) NOT NULL,
  `seo_meta_title_es` varchar(60) NOT NULL,
  `seo_meta_desc_en` varchar(150) NOT NULL,
  `seo_meta_desc_fre` varchar(150) NOT NULL,
  `seo_meta_desc_es` varchar(150) NOT NULL,
  `seo_dynamic` enum('0','1') NOT NULL DEFAULT '0',
  `seo_og_title_en` varchar(60) NOT NULL,
  `seo_og_title_fre` varchar(60) NOT NULL,
  `seo_og_title_es` varchar(60) NOT NULL,
  `seo_og_desc_en` varchar(150) NOT NULL,
  `seo_og_desc_fre` varchar(150) NOT NULL,
  `seo_og_desc_es` varchar(150) NOT NULL,
  `seo_og_image` varchar(100) DEFAULT NULL COMMENT 'Fully qualified image path',
  `seo_og_image_secure_url` varchar(100) DEFAULT NULL COMMENT 'Fully qualified SSL image URL (with https)',
  `seo_og_image_width` int(10) DEFAULT NULL,
  `seo_og_image_height` int(10) DEFAULT NULL,
  `seo_og_video` varchar(100) DEFAULT NULL COMMENT 'Fully qualified URL path of the video relevant to the view page',
  `seo_og_type_en` varchar(20) DEFAULT NULL,
  `seo_og_type_fre` varchar(20) DEFAULT NULL,
  `seo_og_type_es` varchar(20) DEFAULT NULL,
  `seo_og_url` varchar(200) DEFAULT NULL,
  `seo_twitter_title_en` varchar(60) NOT NULL,
  `seo_twitter_title_fre` varchar(60) NOT NULL,
  `seo_twitter_title_es` varchar(60) NOT NULL,
  `seo_twitter_desc_en` varchar(150) NOT NULL,
  `seo_twitter_desc_fre` varchar(150) NOT NULL,
  `seo_twitter_desc_es` varchar(150) NOT NULL,
  `seo_twitter_image` varchar(100) DEFAULT NULL,
  `seo_canonical_href` varchar(200) DEFAULT NULL,
  `seo_no_index` enum('0','1') NOT NULL DEFAULT '0',
  `seo_h1_text_en` varchar(70) NOT NULL,
  `seo_h1_text_fre` varchar(70) NOT NULL,
  `seo_h1_text_es` varchar(70) NOT NULL,
  `seo_h2_text_en` varchar(70) NOT NULL,
  `seo_h2_text_fre` varchar(70) NOT NULL,
  `seo_h2_text_es` varchar(70) NOT NULL,
  `seo_page_content_en` text NOT NULL,
  `seo_page_content_fre` text NOT NULL,
  `seo_page_content_es` text NOT NULL,
  `seo_keywords_en` varchar(200) NOT NULL,
  `seo_keywords_fre` varchar(200) NOT NULL,
  `seo_keywords_es` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `seo`
--

INSERT INTO `seo` (`seo_id`, `seo_page_name`, `seo_meta_title_en`, `seo_meta_title_fre`, `seo_meta_title_es`, `seo_meta_desc_en`, `seo_meta_desc_fre`, `seo_meta_desc_es`, `seo_dynamic`, `seo_og_title_en`, `seo_og_title_fre`, `seo_og_title_es`, `seo_og_desc_en`, `seo_og_desc_fre`, `seo_og_desc_es`, `seo_og_image`, `seo_og_image_secure_url`, `seo_og_image_width`, `seo_og_image_height`, `seo_og_video`, `seo_og_type_en`, `seo_og_type_fre`, `seo_og_type_es`, `seo_og_url`, `seo_twitter_title_en`, `seo_twitter_title_fre`, `seo_twitter_title_es`, `seo_twitter_desc_en`, `seo_twitter_desc_fre`, `seo_twitter_desc_es`, `seo_twitter_image`, `seo_canonical_href`, `seo_no_index`, `seo_h1_text_en`, `seo_h1_text_fre`, `seo_h1_text_es`, `seo_h2_text_en`, `seo_h2_text_fre`, `seo_h2_text_es`, `seo_page_content_en`, `seo_page_content_fre`, `seo_page_content_es`, `seo_keywords_en`, `seo_keywords_fre`, `seo_keywords_es`) VALUES
(1, 'home', 'This is the home page. You are welcome to DGZ', 'Ca cest la page aquable. Bien venu a DGZ', 'Etos home pageo haha. Buenos venidos dos DGZ', 'Some description about the home page of DGZ the ultimate CMS or framework-whatever you wanna call it. ', 'Ca cest un peut de desciption sur DGZ, Le framework de programmeurs, ou qua que ca soit.', 'etos estupiendo decripilla dos DGZ', '0', 'This is the home page. You are welcome to DGZ', 'Ca cest la page aquable. Bien venu a DGZ', 'Etos home pageo haha. Buenos venidos dos DGZ', 'Some description about the home page of DGZ the ultimate CMS or framework-whatever you wanna call it.', 'Ca cest un peut de desciption sur DGZ, Le framework de programmeurs, ou qua que ca soit.', 'etos estupiendo decripilla dos DGZ', 'http://dorguzen/assets/social/site.png', 'https://dorguzen/assets/social/site.png', 1200, 640, NULL, NULL, NULL, NULL, NULL, 'This is the home page. You are welcome to DGZ', 'Ca cest la page aquable. Bien venu a DGZ', 'Etos home pageo haha. Buenos venidos dos DGZ', 'Some description about the home page of DGZ the ultimate CMS or framework-whatever you wanna call it.', 'Ca cest un peut de desciption sur DGZ, Le framework de programmeurs, ou qua que ca soit.', 'etos estupiendo decripilla dos DGZ', NULL, '1', '0', 'Some cool h1 text for my awesome webpage', 'Quelque text cool pur mon formidable page de web', 'Somigo texta pora mono formidabla pagina interneta', 'Some nice text for the h2 tag of my webpage', 'Just un coudre text pour le h2 tag de mon page de web', 'Pagin pour la h2 tagino de mono page wba', 'This is the real deal; wow some really long text for the content of my web page. It is so long i am tired of typing already lo, just because i know it is meant to be long. ', 'En peut de long text pour le propre content de mon page de web', 'Contenidos de mon pagina de web. Eldo primeros de las budoncamentos i lamidados spectaculada. Yeah.', 'keyword1, keyword2, keyword3, keyword4, keyword5', 'mot cley1, mot cley2, mot cley3, mot cley4, mot cley5', 'keyword1, keyword2, keyword3, keyword4, keyword5'),
(3, 'contact', 'Dorguzen contact page', '', '', 'Contact us if you have any inquiries', '', '', '0', 'Dorguzen contact page', '', '', 'Contact us if you have any inquiries', '', '', 'https://yourSite/assets/images/social/og-image.png', 'https://yourSite/assets/images/social/image.svg', 1200, 630, ' ', 'article', '', '', 'https://dorguzen.com/feedback', 'Contact page title', '', '', 'Contact page description', '', '', '', 'https://dorguzen.com/feedback', '0', 'h1 contact text', '', '', 'h2 contact text', '', '', 'Get in touch with us for quotes, or if you would like to enquire about anything. Please complete all the fields', '', '', 'Dorguzen contact, reach out to us, send us a message', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `seo_global`
--

CREATE TABLE `seo_global` (
  `seo_global_id` int(11) NOT NULL,
  `seo_global_og_locale` varchar(10) DEFAULT NULL,
  `seo_global_og_site` varchar(20) DEFAULT NULL,
  `seo_global_og_article_publisher` varchar(200) DEFAULT NULL,
  `seo_global_og_author` varchar(200) DEFAULT NULL,
  `seo_global_geo_placename` varchar(30) DEFAULT NULL,
  `seo_global_geo_region` varchar(10) DEFAULT NULL,
  `seo_global_geo_position` varchar(100) DEFAULT NULL,
  `seo_global_fb_id` varchar(100) DEFAULT NULL,
  `seo_global_twitter_card` varchar(50) DEFAULT NULL,
  `seo_global_twitter_site` varchar(100) DEFAULT NULL,
  `seo_global_reflang_alternate1` varchar(10) DEFAULT NULL,
  `seo_global_reflang_alternate2` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `seo_global`
--

INSERT INTO `seo_global` (`seo_global_id`, `seo_global_og_locale`, `seo_global_og_site`, `seo_global_og_article_publisher`, `seo_global_og_author`, `seo_global_geo_placename`, `seo_global_geo_region`, `seo_global_geo_position`, `seo_global_fb_id`, `seo_global_twitter_card`, `seo_global_twitter_site`, `seo_global_reflang_alternate1`, `seo_global_reflang_alternate2`) VALUES
(1, 'en_UK', 'Dorguzen', 'https://www.facebook.com/Camerooncom-1686737211635233/', ' ', 'England', 'UK', '7.369722;12.354722', '1686737211635233', 'summary', '@Camerooncom2', ' ', ' '),
(2, 'en_UK', 'mySiteName', 'mySiteNameOnFacebookPage', 'Gustav', 'Manchester', 'UK', '213442342424 99786542', '224645757778', 'website', 'mySiteTwitterPageName', '', ''),
(3, 'en_US', 'Dorguzen', 'Dorguzen', 'Dorguzen', 'Canada', 'CA', '1313144335, 943743278632', 'Dorguzen', 'twitter@Dorguzen', 'twitter.com/dorguzen', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `users_id` int(10) UNSIGNED NOT NULL,
  `users_type` enum('member','admin','admin_gen','super_admin') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'member',
  `users_email` varchar(80) COLLATE utf8_swedish_ci NOT NULL,
  `users_phone_number` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL,
  `users_pass` blob NOT NULL,
  `users_first_name` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  `users_last_name` varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  `users_emailverified` enum('yes','no') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'no',
  `users_eactivationcode` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `users_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `users_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`users_id`, `users_type`, `users_email`, `users_phone_number`, `users_pass`, `users_first_name`, `users_last_name`, `users_emailverified`, `users_eactivationcode`, `users_updated`, `users_created`) VALUES
(62, 'member', 'john@colon.com', NULL, 0xd9c05f47acf76e1d30be210f557ce92a, 'John', 'Colon', 'no', NULL, '2023-06-16 13:55:54', '2020-07-26 20:22:34'),
(67, 'member', 'test@example.com', '4378496403', 0x2b96495f6ebd2186cc60260e38cef97a, 'Test', 'TestUser', 'yes', NULL, '2026-04-18 14:16:41', '2026-04-18 14:03:13'),
(69, 'super_admin', 'admin@dorguzen.com', '', 0x7f5a052f3754e0847e43fa0a51c5d09d, 'Dorguzen', 'Admin', 'yes', NULL, '2026-04-20 00:55:14', '2026-04-20 00:55:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `baseSettings`
--
ALTER TABLE `baseSettings`
  ADD PRIMARY KEY (`settings_id`);

--
-- Indexes for table `contactformmessage`
--
ALTER TABLE `contactformmessage`
  ADD PRIMARY KEY (`contactformmessage_id`);

--
-- Indexes for table `dgz_failed_jobs`
--
ALTER TABLE `dgz_failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dgz_jobs`
--
ALTER TABLE `dgz_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_queue` (`queue`);

--
-- Indexes for table `dgz_migrations`
--
ALTER TABLE `dgz_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dgz_migration_locks`
--
ALTER TABLE `dgz_migration_locks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dgz_refresh_tokens`
--
ALTER TABLE `dgz_refresh_tokens`
  ADD PRIMARY KEY (`refresh_tokens_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`logs_id`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`password_reset_id`);

--
-- Indexes for table `seo`
--
ALTER TABLE `seo`
  ADD PRIMARY KEY (`seo_id`);

--
-- Indexes for table `seo_global`
--
ALTER TABLE `seo_global`
  ADD PRIMARY KEY (`seo_global_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD UNIQUE KEY `email` (`users_email`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `baseSettings`
--
ALTER TABLE `baseSettings`
  MODIFY `settings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contactformmessage`
--
ALTER TABLE `contactformmessage`
  MODIFY `contactformmessage_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dgz_failed_jobs`
--
ALTER TABLE `dgz_failed_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dgz_jobs`
--
ALTER TABLE `dgz_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dgz_migrations`
--
ALTER TABLE `dgz_migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dgz_refresh_tokens`
--
ALTER TABLE `dgz_refresh_tokens`
  MODIFY `refresh_tokens_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `logs_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `password_reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seo`
--
ALTER TABLE `seo`
  MODIFY `seo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seo_global`
--
ALTER TABLE `seo_global`
  MODIFY `seo_global_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
