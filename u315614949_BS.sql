-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 09, 2026 at 08:16 PM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u315614949_BS`
--

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'هل تقدمون الخدمة خارج الرياض؟', 'نعم، نستقبل طلبات التأجير داخل وخارج الرياض. تواصل معنا لتحديد الموقع ومعرفة إمكانية الخدمة.', 1, 1, '2026-11-09 20:04:37'),
(2, 'هل يمكن طلب أكثر من خدمة في نفس المناسبة؟', 'نعم، يمكنك الاستفسار عن أكثر من خدمة مثل بيت الشعر والجلسات والكراسي والطاولات والمكيفات والصوتيات.', 2, 1, '2026-11-09 20:04:37'),
(3, 'كيف أعرف الأسعار والتوفر؟', 'اتصل بنا على الرقم 0530729970، واذكر نوع التجهيز والموقع والتاريخ للحصول على التفاصيل.', 3, 1, '2026-11-09 20:04:37'),
(4, 'هل توفرون القهوجيين والصبابين؟', 'نعم، تتوفر خدمة القهوجيين والصبابين ضمن خدمات الضيافة، ويمكنك الاستفسار عن التفاصيل عند التواصل.', 4, 1, '2026-11-09 20:04:37');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `image`, `category_id`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'بيوت شعر', 'imgs/img1.jpeg', 1, 1, 1, '2026-11-09 20:04:37'),
(2, 'خيام المناسبات', 'imgs/img2.jpeg', 1, 2, 1, '2026-11-09 20:04:37'),
(3, 'جلسات عربية', 'imgs/img3.jpeg', 2, 3, 1, '2026-11-09 20:04:37'),
(4, 'تجهيز المناسبات', 'imgs/img4.jpeg', 3, 4, 1, '2026-11-09 20:04:37'),
(5, 'مجالس الضيافة', 'imgs/img5.jpeg', 2, 5, 1, '2026-11-09 20:04:37'),
(6, 'كراسي وطاولات', 'imgs/img6.jpeg', 3, 6, 1, '2026-11-09 20:04:37'),
(7, 'تفاصيل التجهيز', 'imgs/img7.jpeg', 1, 7, 1, '2026-11-09 20:04:37'),
(8, 'مستلزمات الحفلات', 'imgs/img8.jpeg', 3, 8, 1, '2026-11-09 20:04:37'),
(9, 'جلسات للمناسبات', 'imgs/img10.jpeg', 2, 9, 1, '2026-11-09 20:04:37'),
(10, 'جلسات للمناسبات', 'imgs/img9.jpeg', 2, 10, 1, '2026-11-09 20:04:37'),
(11, 'جلسات للمناسبات', 'imgs/img11.jpeg', 2, 11, 1, '2026-11-09 20:04:37'),
(12, 'جلسات للمناسبات', 'imgs/img12.jpeg', 2, 12, 1, '2026-11-09 20:04:37'),
(13, 'جلسات للمناسبات', 'imgs/img13.jpeg', 2, 13, 1, '2026-11-09 20:04:37'),
(14, 'جلسات للمناسبات', 'imgs/img14.jpeg', 2, 14, 1, '2026-11-09 20:04:37'),
(15, 'جلسات للمناسبات', 'imgs/img15.jpeg', 2, 15, 1, '2026-11-09 20:04:37'),
(16, 'جلسات للمناسبات', 'imgs/img16.jpeg', 2, 16, 1, '2026-11-09 20:04:37'),
(17, 'جلسات للمناسبات', 'imgs/img17.jpeg', 2, 17, 1, '2026-11-09 20:04:37'),
(18, 'جلسات للمناسبات', 'imgs/img18.jpeg', 2, 18, 1, '2026-11-09 20:04:37'),
(19, 'جلسات للمناسبات', 'imgs/img19.jpeg', 2, 19, 1, '2026-11-09 20:04:37'),
(20, 'جلسات للمناسبات', 'imgs/img20.jpeg', 2, 20, 1, '2026-11-09 20:04:37'),
(21, 'جلسات للمناسبات', 'imgs/img21.jpeg', 2, 21, 1, '2026-11-09 20:04:37'),
(22, 'جلسات للمناسبات', 'imgs/img22.jpeg', 2, 22, 1, '2026-11-09 20:04:37'),
(23, 'جلسات للمناسبات', 'imgs/img23.jpeg', 2, 23, 1, '2026-11-09 20:04:37'),
(24, 'جلسات للمناسبات', 'imgs/img24.jpeg', 2, 24, 1, '2026-11-09 20:04:37');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_categories`
--

CREATE TABLE `gallery_categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_categories`
--

INSERT INTO `gallery_categories` (`id`, `slug`, `name`, `sort_order`) VALUES
(1, 'tents', 'بيوت شعر وخيام', 1),
(2, 'sessions', 'جلسات', 2),
(3, 'events', 'مناسبات وتجهيزات', 3);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `occasions`
--

CREATE TABLE `occasions` (
  `id` int(11) NOT NULL,
  `subtitle` varchar(100) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `occasions`
--

INSERT INTO `occasions` (`id`, `subtitle`, `title`, `description`, `image`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'ليالي الفرح', 'حفلات الزواج والملكة', 'بيوت شعر وخيام وجلسات وضيافة لاستقبال الأهل والأصدقاء في أجواء مميزة.', NULL, 1, 1, '2026-11-09 20:04:37'),
(2, 'لمة الأحباب', 'العزائم والمناسبات العائلية', 'مجالس وجلسات وكراسي وطاولات لتجهيز المكان واستقبال ضيوفك براحة.', NULL, 2, 1, '2026-11-09 20:04:37'),
(3, 'تنظيم واحتفال', 'الفعاليات والاحتفالات', 'خيارات متنوعة من الخيام والصوتيات والمولدات والمستلزمات حسب احتياج الفعالية.', NULL, 3, 1, '2026-11-09 20:04:37'),
(4, 'كرم الضيافة', 'المجالس والاستقبالات', 'بيوت شعر وفرش وقهوجيين وصبابين لتكتمل تفاصيل المجلس وحفاوة الاستقبال.', NULL, 4, 1, '2026-11-09 20:04:37');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT '✦',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `icon`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'تأجير بيوت شعر', 'بيوت شعر بطابع عربي أصيل، مناسبة للمجالس والمناسبات والاستقبالات.', '⌂', 1, 1, '2026-11-09 20:04:37'),
(2, 'تأجير خيام', 'خيارات متنوعة من الخيام لتجهيز المناسبات والتجمعات بمساحات مختلفة.', '⛺', 2, 1, '2026-11-09 20:04:37'),
(3, 'تأجير جلسات', 'جلسات أرضية ومجالس للضيوف تضيف الراحة واللمسة الجميلة للمكان.', '♧', 3, 1, '2026-11-09 20:04:37'),
(4, 'كراسي وطاولات', 'تأجير كراسي وطاولات بتنسيقات تناسب حفلاتكم واجتماعاتكم.', '▤', 4, 1, '2026-11-09 20:04:37'),
(5, 'تأجير الزل والفرش', 'زل وفرش للمجالس وبيوت الشعر والخيام لتكتمل جاهزية موقع المناسبة.', '▧', 5, 1, '2026-11-09 20:04:37'),
(6, 'مكيفات سبليت', 'تأجير مكيفات سبليت للمساعدة في توفير أجواء مناسبة ومريحة للضيوف.', '❄', 6, 1, '2026-11-09 20:04:37'),
(7, 'تأجير مولدات', 'مولدات كهرباء لتلبية احتياجات موقع المناسبة حسب طبيعة التجهيز.', '⚡', 7, 1, '2026-11-09 20:04:37'),
(8, 'السماعات والصوتيات', 'تأجير سماعات وتجهيزات صوتية لإضافة أجواء مناسبة لفعاليتك.', '♫', 8, 1, '2026-11-09 20:04:37'),
(9, 'قهوجيين وصبابين', 'خدمة القهوة والضيافة واستقبال الضيوف بما يناسب مناسباتكم.', '☕', 9, 1, '2026-11-09 20:04:37'),
(10, 'مستلزمات الحفلات', 'نوفر لك ما تحتاجه من مستلزمات وتجهيزات إضافية حسب طلب المناسبة.', '✦', 10, 1, '2026-11-09 20:04:37');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('about_image', 'assets/uploads/about_image_20261109200902_ba5be32c.jpg'),
('about_text', 'إذا كنت تخطط لزواج أو عزيمة أو مناسبة عائلية أو فعالية خاصة، نوفر لك خيارات متعددة لتجهيز المكان من البداية. من بيت الشعر والخيمة إلى الكراسي والطاولات والتكييف والصوتيات، نساعدك تجمع كل احتياجاتك في طلب واحد.\r\n\r\nنهتم بأن تكون التجهيزات مناسبة لطبيعة مناسبتك ومساحة موقعك، ونعمل على خدمة العملاء داخل الرياض وخارجها. تواصل معنا واذكر لنا نوع المناسبة والاحتياج، ونساعدك في اختيار التجهيز المناسب.'),
('about_title', 'نرتب المكان.. <em>ونصنع أجواء</em><br>تستحق الذكرى'),
('email', ''),
('footer_text', 'تأجير بيوت شعر وخيام وجلسات وكراسي وطاولات ومكيفات ومولدات ومستلزمات الحفلات داخل وخارج الرياض.'),
('hero_image', 'assets/uploads/hero_image_20261109200902_8c39a576.jpg'),
('hero_lead', 'نجهز لك أجواء مناسبتك من البداية؛ بيوت شعر وخيام وجلسات وكراسي وطاولات ومكيفات ومولدات وصوتيات، بالإضافة إلى القهوجيين والصبابين وكل ما يلزم الحفلات. تواصل معنا وحدد احتياجك، ونساعدك في اختيار التجهيز المناسب داخل وخارج الرياض.'),
('hero_subtitle', 'تأجير بيوت شعر وتجهيز مناسبات'),
('hero_title', 'تأجير خيام الرياض'),
('meta_description', 'تأجير بيوت شعر وخيام وجلسات وكراسي وطاولات ومكيفات ومولدات ومستلزمات الحفلات داخل وخارج الرياض. للحجز والاستفسار 0530729970.'),
('phone', '0530729970'),
('site_name', 'تأجير خيام وبيوت شعر'),
('site_tagline', 'للمناسبات والضيافة'),
('whatsapp', '966530729970');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `role` enum('admin','editor') DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `role`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$KUDE.5Tg2yA32Lf5zE1b0urqKi4WNm5kncZuBxLrPdb7Q0q2t6HSO', 'Admin', '', 'admin', '2026-11-09 20:16:34', '2026-11-09 20:04:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gallery_cat` (`category_id`);

--
-- Indexes for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `occasions`
--
ALTER TABLE `occasions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `occasions`
--
ALTER TABLE `occasions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `fk_gallery_cat` FOREIGN KEY (`category_id`) REFERENCES `gallery_categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
