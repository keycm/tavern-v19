-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 04, 2025 at 07:21 AM
-- Server version: 10.6.22-MariaDB-cll-lve
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tavern_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `blocked_dates`
--

CREATE TABLE `blocked_dates` (
  `id` int(11) NOT NULL,
  `block_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blocked_slots`
--

CREATE TABLE `blocked_slots` (
  `block_id` int(11) NOT NULL,
  `block_reason` varchar(255) NOT NULL,
  `block_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `assigned_table` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `admin_reply`, `replied_at`, `is_read`, `created_at`, `deleted_at`) VALUES
(5, 'user', 'penapaul858@gmail.com', 'reservation', 'good night', 'Good', '2025-09-26 16:13:46', 1, '2025-09-26 16:13:22', NULL),
(6, 'user', 'penapaul858@gmail.com', 'reservation', 'I want to rreserve', 'You\'ve found a PHP warning bug. The error messages you\'re seeing, \"Constant DB_SERVER already defined', '2025-09-26 17:04:36', 1, '2025-09-26 17:04:03', NULL),
(7, 'dfgh', '12jfksdfvk@gmail.com', 'dfg', 'dwfg', NULL, NULL, 0, '2025-09-27 06:54:57', NULL),
(8, 'fgh', '123454@gmail.com', 'Reservation Inquiry', 'efghjcvb', NULL, NULL, 0, '2025-09-27 06:59:18', NULL),
(9, 'admin', 'keycm109@gmail.com', 'Reservation Inquiry', 'HELLLO', 'sdfgh', '2025-09-28 12:44:50', 1, '2025-09-27 15:02:49', '2025-10-03 07:51:11'),
(10, 'user', 'penapaul858@gmail.com', 'Reservation Inquiry', 'Of course. I\'ve updated the notification_control.php file to include a \"View\" button for both messages and comments. Clicking this button will open a modal window displaying the full text, which is especially useful for longer entries.', 'joshua', '2025-09-29 07:41:34', 1, '2025-09-28 10:00:07', '2025-10-03 07:04:29'),
(0, 'user', 'penapaul858@gmail.com', 'Reservation Inquiry', 'Hello', NULL, NULL, 0, '2025-10-03 16:39:03', NULL),
(0, 'Mike Sem Schneider', 'mike@monkeydigital.co', 'Grow Your Website Traffic with Country-Specific Social Ads – Only $10 for 10K Visits!', 'Hi there, \r\n \r\nI wanted to check in with something that could seriously boost your website’s reach. We work with a trusted ad network that allows us to deliver real, country-targeted social ads traffic for just $10 per 10,000 visits. \r\n \r\nThis isn\'t fake traffic—it’s engaged traffic, tailored to your target country and niche. \r\n \r\nWhat you get: \r\n \r\n10,000+ high-quality visitors for just $10 \r\nCountry-specific traffic for your chosen location \r\nHigher volumes available based on your needs \r\nProv', NULL, NULL, 0, '2025-10-04 13:10:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deletion_history`
--

CREATE TABLE `deletion_history` (
  `log_id` int(11) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `purge_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deletion_history`
--

INSERT INTO `deletion_history` (`log_id`, `item_type`, `item_id`, `item_data`, `deleted_at`, `purge_date`) VALUES
(7, 'menu_item', 24, '{\"id\":24,\"name\":\"ertgh\",\"category\":\"Specialty\",\"price\":\"400.00\",\"image\":\"uploads\\/68d6d906e925d9.11936485.jpg\",\"description\":\"dfg\",\"deleted_at\":null}', '2025-09-28 13:03:04', '2025-10-28'),
(12, 'event', 9, '{\"id\":9,\"title\":\"Hallowen\",\"date\":\"2025-11-01\",\"end_date\":\"2025-11-05\",\"description\":\"Happ Halloween\",\"image\":\"uploads\\/68d9326184f316.63890607.jpeg\",\"deleted_at\":null}', '2025-09-29 03:28:20', '2025-10-29'),
(13, 'event', 10, '{\"id\":10,\"title\":\"Birthday ko ngayon\",\"date\":\"2025-09-30\",\"end_date\":\"2025-09-29\",\"description\":\"Anjing\",\"image\":\"uploads\\/68d9fd838946c2.41190803.png\",\"deleted_at\":null}', '2025-09-29 03:31:53', '2025-10-29'),
(22, 'hero_slide', 15, '{\"id\":15,\"image_path\":\"uploads\\/68da9aa16c43f7.95794272.jpeg\",\"title\":\"HEllo\",\"subtitle\":\"cvbn\",\"video_path\":\"\",\"media_type\":\"image\",\"created_at\":\"2025-09-29 22:41:37\",\"deleted_at\":null}', '2025-09-29 17:47:48', '2025-10-30'),
(23, 'hero_slide', 14, '{\"id\":14,\"image_path\":\"uploads\\/68da9a94154ec6.29158311.jpeg\",\"title\":\"Tavern Publico\",\"subtitle\":\"fghjk\",\"video_path\":\"\",\"media_type\":\"image\",\"created_at\":\"2025-09-29 22:41:24\",\"deleted_at\":null}', '2025-09-29 17:47:51', '2025-10-30'),
(25, 'menu_item', 25, '{\"id\":25,\"name\":\"ert\",\"category\":\"Specialty\",\"price\":\"34.00\",\"image\":\"uploads\\/68d6d914ba4907.26884802.png\",\"description\":\"wertghj\",\"deleted_at\":null}', '2025-09-29 17:57:08', '2025-10-30'),
(26, 'menu_item', 18, '{\"id\":18,\"name\":\"sdfgh\",\"category\":\"Specialty\",\"price\":\"34.00\",\"image\":\"uploads\\/68d6239f7c51c5.11311157.png\",\"description\":\"dfgh\",\"deleted_at\":null}', '2025-09-29 17:57:15', '2025-10-30'),
(27, 'menu_item', 23, '{\"id\":23,\"name\":\"wdefg\",\"category\":\"Specialty\",\"price\":\"2.00\",\"image\":\"uploads\\/68d6d8f85b38e2.02182447.png\",\"description\":\"defgh\",\"deleted_at\":null}', '2025-09-29 17:57:20', '2025-10-30'),
(28, 'menu_item', 26, '{\"id\":26,\"name\":\"caramel\",\"category\":\"Coffee\",\"price\":\"85.00\",\"image\":\"uploads\\/68d9329e59bee4.03672499.jpg\",\"description\":\"yummy\",\"deleted_at\":null}', '2025-09-29 18:03:29', '2025-10-30'),
(29, 'menu_item', 22, '{\"id\":22,\"name\":\"asdf\",\"category\":\"Lunch\",\"price\":\"234.00\",\"image\":\"uploads\\/68d657427b8541.26434268.png\",\"description\":\"sdfghgfdvb\",\"deleted_at\":null}', '2025-09-29 18:03:32', '2025-10-30'),
(30, 'menu_item', 21, '{\"id\":21,\"name\":\"cfe\",\"category\":\"Lunch\",\"price\":\"23.00\",\"image\":\"uploads\\/68d62a9ca42191.99713898.png\",\"description\":\"Completely replace the code in your update.php file with this corrected version. The only change is to the sanitize function.\",\"deleted_at\":null}', '2025-09-29 18:03:34', '2025-10-30'),
(0, 'user', 8, '{\"user_id\":8,\"username\":\"VIncent\",\"email\":\"penapaul858@gmail.com\",\"is_verified\":0,\"verification_token\":\"34c396b85df97f785d4dd670ac2ef158a8ee7e24a02654e993ab2331630cc40312f0488cad9a9fa73e4ff816e028b773422a\",\"is_admin\":0,\"avatar\":null,\"created_at\":\"2025-10-02 10:28:25\",\"deleted_at\":null}', '2025-10-02 17:36:40', '2025-11-01'),
(0, 'team_member', 2, '{\"id\":2,\"name\":\"karl\",\"title\":\"CEO\",\"bio\":\"FULL STACK\",\"image\":\"uploads\\/68d9322c4e2517.13457155.jpg\",\"created_at\":\"2025-09-28 06:03:40\",\"deleted_at\":null}', '2025-10-03 14:02:13', '2025-11-02'),
(0, 'menu_item', 29, '{\"id\":29,\"name\":\"Pork Steak\",\"category\":\"Specialty\",\"price\":\"178.00\",\"image\":\"uploads\\/68dac9ed426bc6.00407464.jpg\",\"description\":\"The sound of the sauce simmering, the scent of caramelized onions... Filipino Pork Steak is less a dish, and more a call home.\",\"deleted_at\":null}', '2025-10-03 14:02:45', '2025-11-02'),
(0, 'contact_message', 10, '{\"id\":10,\"name\":\"user\",\"email\":\"penapaul858@gmail.com\",\"subject\":\"Reservation Inquiry\",\"message\":\"Of course. I\'ve updated the notification_control.php file to include a \\\"View\\\" button for both messages and comments. Clicking this button will open a modal window displaying the full text, which is especially useful for longer entries.\",\"admin_reply\":\"joshua\",\"replied_at\":\"2025-09-29 00:41:34\",\"is_read\":1,\"created_at\":\"2025-09-28 03:00:07\",\"deleted_at\":null}', '2025-10-03 14:04:29', '2025-11-02'),
(0, 'event', 8, '{\"id\":8,\"title\":\"Chrismast\",\"date\":\"2025-12-21\",\"end_date\":\"2025-12-25\",\"description\":\"My apologies. I shortened the code in my last response to make it easier to copy, but I see now that you\'d prefer to see it fully formatted. You are correct, no functionality was removed, it was only compressed.\",\"image\":\"uploads\\/68d62ec99388d6.32195318.png\",\"deleted_at\":null}', '2025-10-03 14:05:17', '2025-11-02'),
(0, 'gallery_image', 12, '{\"id\":12,\"image\":\"uploads\\/68d62b9ea7afe7.31026399.png\",\"description\":\"seiokjhgfdsxcvbnmjhfdxcv\",\"deleted_at\":null}', '2025-10-03 14:05:40', '2025-11-02'),
(0, 'hero_slide', 17, '{\"id\":17,\"image_path\":\"uploads\\/68dac695b68195.67723369.jpg\",\"title\":\"2nd\",\"subtitle\":\"AWRSDSSSSSS\",\"video_path\":\"\",\"media_type\":\"image\",\"created_at\":\"2025-09-29 10:49:09\",\"deleted_at\":null}', '2025-10-03 14:06:10', '2025-11-02'),
(0, 'team_member', 1, '{\"id\":1,\"name\":\"fdghyjuh\",\"title\":\"rtyui\",\"bio\":\"rtyuik\",\"image\":\"uploads\\/68d66bb522e764.00626705.jpg\",\"created_at\":\"2025-09-26 03:32:21\",\"deleted_at\":null}', '2025-10-03 14:06:49', '2025-11-02'),
(0, 'team_member', 2, '{\"id\":2,\"name\":\"karl\",\"title\":\"CEO\",\"bio\":\"FULL STACK\",\"image\":\"uploads\\/68d9322c4e2517.13457155.jpg\",\"created_at\":\"2025-09-28 06:03:40\",\"deleted_at\":null}', '2025-10-03 14:17:14', '2025-11-02'),
(0, 'blocked_date', 18, '{\"id\":18,\"block_date\":\"2025-09-29\"}', '2025-10-03 14:26:20', '2025-11-02'),
(0, 'blocked_date', 17, '{\"id\":17,\"block_date\":\"2025-08-07\"}', '2025-10-03 14:26:32', '2025-11-02'),
(0, 'blocked_date', 15, '{\"id\":15,\"block_date\":\"2025-10-02\"}', '2025-10-03 14:27:13', '2025-11-02'),
(0, 'blocked_date', 13, '{\"id\":13,\"block_date\":\"2025-09-30\"}', '2025-10-03 14:27:17', '2025-11-02'),
(0, 'blocked_date', 16, '{\"id\":16,\"block_date\":\"2025-09-29\"}', '2025-10-03 14:27:21', '2025-11-02'),
(0, 'blocked_date', 11, '{\"id\":11,\"block_date\":\"2025-09-28\"}', '2025-10-03 14:27:25', '2025-11-02'),
(0, 'testimonial', 5, '{\"id\":5,\"user_id\":14,\"reservation_id\":21,\"rating\":3,\"comment\":\"sdfgh\",\"is_featured\":1,\"created_at\":\"2025-09-26 11:10:52\",\"deleted_at\":null}', '2025-10-03 14:28:02', '2025-11-02'),
(0, 'user', 39, '{\"user_id\":39,\"username\":\"axus\",\"email\":\"publicotavern@gmail.com\",\"verification_token\":\"105399c5c91f2c19a16209f53479579811629b5055b8f3cd7ba98e2b2a0dec0e24cc3cf59dc2f849f3da59e500bde5fb4d33\",\"is_verified\":0,\"is_admin\":0,\"avatar\":null,\"mobile\":null,\"birthday\":null,\"created_at\":\"2025-10-02 08:50:54\",\"deleted_at\":null}', '2025-10-03 14:28:23', '2025-11-02'),
(0, 'user', 39, '{\"user_id\":39,\"username\":\"axus\",\"email\":\"publicotavern@gmail.com\",\"verification_token\":\"105399c5c91f2c19a16209f53479579811629b5055b8f3cd7ba98e2b2a0dec0e24cc3cf59dc2f849f3da59e500bde5fb4d33\",\"is_verified\":0,\"is_admin\":0,\"avatar\":null,\"mobile\":null,\"birthday\":null,\"created_at\":\"2025-10-02 08:50:54\",\"deleted_at\":\"2025-10-03 07:28:23\"}', '2025-10-03 14:37:08', '2025-11-02'),
(0, 'user', 39, '{\"user_id\":39,\"username\":\"axus\",\"email\":\"publicotavern@gmail.com\",\"verification_token\":\"105399c5c91f2c19a16209f53479579811629b5055b8f3cd7ba98e2b2a0dec0e24cc3cf59dc2f849f3da59e500bde5fb4d33\",\"is_verified\":0,\"is_admin\":0,\"avatar\":null,\"mobile\":null,\"birthday\":null,\"created_at\":\"2025-10-02 08:50:54\",\"deleted_at\":\"2025-10-03 07:37:08\"}', '2025-10-03 14:37:45', '2025-11-02'),
(0, 'user', 40, '{\"user_id\":40,\"username\":\"hello\",\"email\":\"vince@gmail.com\",\"verification_token\":null,\"is_verified\":0,\"is_admin\":0,\"avatar\":null,\"mobile\":null,\"birthday\":null,\"created_at\":\"2025-10-03 07:42:38\",\"deleted_at\":null}', '2025-10-03 14:42:50', '2025-11-02'),
(0, 'contact_message', 9, '{\"id\":9,\"name\":\"admin\",\"email\":\"keycm109@gmail.com\",\"subject\":\"Reservation Inquiry\",\"message\":\"HELLLO\",\"admin_reply\":\"sdfgh\",\"replied_at\":\"2025-09-28 05:44:50\",\"is_read\":1,\"created_at\":\"2025-09-27 08:02:49\",\"deleted_at\":null}', '2025-10-03 14:51:11', '2025-11-02'),
(0, 'event', 6, '{\"id\":6,\"title\":\"Hello\",\"date\":\"2025-12-21\",\"end_date\":null,\"description\":\"im\",\"image\":\"uploads\\/68d62337a1aaf5.89438545.png\",\"deleted_at\":null}', '2025-10-03 14:51:50', '2025-11-02'),
(0, 'event', 7, '{\"id\":7,\"title\":\"dfghjkl\",\"date\":\"275760-07-06\",\"end_date\":null,\"description\":\"hgfdsdf\",\"image\":\"uploads\\/68d62b59851c63.68834595.png\",\"deleted_at\":null}', '2025-10-03 17:36:20', '2025-11-02'),
(0, 'hero_slide', 18, '{\"id\":18,\"image_path\":\"uploads\\/68dac70392b650.98291033.jpg\",\"title\":\"3\",\"subtitle\":\"efg\",\"video_path\":\"\",\"media_type\":\"image\",\"created_at\":\"2025-09-29 10:50:59\",\"deleted_at\":null}', '2025-10-04 14:02:53', '2025-11-03'),
(0, 'team_member', 3, '{\"id\":3,\"name\":\"Karl Louis Navarro\",\"title\":\"Chef\",\"bio\":\"HEHEEHEHEHEHE\",\"image\":\"uploads\\/68e12940aee965.21936954.jpg\",\"created_at\":\"2025-10-04 07:03:44\",\"deleted_at\":null}', '2025-10-04 14:03:51', '2025-11-03'),
(0, 'gallery_image', 16, '{\"id\":16,\"image\":\"uploads\\/68dacf1f63f5f2.80098481.jpg\",\"description\":\".\",\"deleted_at\":null}', '2025-10-04 14:04:21', '2025-11-03'),
(0, 'blocked_date', 19, '{\"id\":19,\"block_date\":\"2025-10-04\"}', '2025-10-04 14:04:56', '2025-11-03'),
(0, 'testimonial', 2, '{\"id\":2,\"user_id\":14,\"reservation_id\":18,\"rating\":3,\"comment\":\"Based on the code, the rating feature will only appear on the homepage under specific conditions. It is not visible in your screenshot because one or more of the following requirements have not been met:\",\"is_featured\":0,\"created_at\":\"2025-09-26 08:04:18\",\"deleted_at\":null}', '2025-10-04 14:13:47', '2025-11-03');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `end_date` date DEFAULT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `date`, `end_date`, `description`, `image`, `deleted_at`) VALUES
(6, 'Hello', '2025-12-21', NULL, 'im', 'uploads/68d62337a1aaf5.89438545.png', '2025-10-03 14:51:50'),
(7, 'dfghjkl', '275760-07-06', NULL, 'hgfdsdf', 'uploads/68d62b59851c63.68834595.png', '2025-10-03 17:36:20'),
(8, 'Chrismast', '2025-12-21', '2025-12-25', 'My apologies. I shortened the code in my last response to make it easier to copy, but I see now that you\'d prefer to see it fully formatted. You are correct, no functionality was removed, it was only compressed.', 'uploads/68d62ec99388d6.32195318.png', '2025-10-03 14:05:17'),
(9, 'Hallowen', '2025-11-01', '2025-11-05', 'Happ Halloween', 'uploads/68d9326184f316.63890607.jpeg', '2025-09-29 03:28:20'),
(10, 'Birthday ko ngayon', '2025-09-30', '2025-09-29', 'Anjing', 'uploads/68d9fd838946c2.41190803.png', '2025-09-29 03:31:53'),
(12, 'Happy New Year', '2025-12-01', '2026-01-05', '\"Tonight is the midnight magic where endings become beautiful beginnings. Dream big; the whole year is listening.\"', 'uploads/68dacd3649c864.17654122.jpg', NULL),
(13, 'Happy Valentine\'s Day', '2026-02-14', NULL, '\"Love is the main course, and our atmosphere is the perfect accompaniment. A night you’ll both cherish.\"', 'uploads/68dacd8d9cb509.70007263.jpg', NULL),
(14, 'Happy Mother\'s Day', '2026-05-11', NULL, '\"A meal made with gratitude. This Mother\'s Day, we celebrate the woman who taught us everything about nourishment.\"', 'uploads/68dacdf1df31a4.48880259.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `image`, `description`, `deleted_at`) VALUES
(12, 'uploads/68d62b9ea7afe7.31026399.png', 'seiokjhgfdsxcvbnmjhfdxcv', '2025-10-03 14:05:40'),
(14, 'uploads/68dacf0551c0c9.71677068.jpg', '.', NULL),
(15, 'uploads/68dacf13998cf3.82374254.jpg', '.', NULL),
(16, 'uploads/68dacf1f63f5f2.80098481.jpg', '.', '2025-10-04 14:04:21'),
(17, 'uploads/68dacf30173f29.03142440.jpg', 'family', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `media_type` varchar(10) NOT NULL DEFAULT 'image',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `image_path`, `title`, `subtitle`, `video_path`, `media_type`, `created_at`, `deleted_at`) VALUES
(12, 'uploads/68d80a5b4966f3.20083863.jpg', 'Tavern Publico', 'Where good company gathers', '', 'image', '2025-09-27 16:01:31', NULL),
(13, '', '', '', 'uploads/68d80a65aadfd9.10474346.mp4', 'video', '2025-09-27 16:01:41', NULL),
(14, 'uploads/68da9a94154ec6.29158311.jpeg', 'Tavern Publico', 'fghjk', '', 'image', '2025-09-29 14:41:24', '2025-09-30 01:47:51'),
(15, 'uploads/68da9aa16c43f7.95794272.jpeg', 'HEllo', 'cvbn', '', 'image', '2025-09-29 14:41:37', '2025-09-30 01:47:48'),
(16, 'uploads/68dac667d90566.26397573.jpg', '1st', '2nd', '', 'image', '2025-09-29 17:48:23', NULL),
(17, 'uploads/68dac695b68195.67723369.jpg', '2nd', 'AWRSDSSSSSS', '', 'image', '2025-09-29 17:49:09', '2025-10-03 07:06:10'),
(18, 'uploads/68dac70392b650.98291033.jpg', '3', 'efg', '', 'image', '2025-09-29 17:50:59', '2025-10-04 07:02:53');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `category`, `price`, `image`, `description`, `deleted_at`) VALUES
(18, 'sdfgh', 'Specialty', 34.00, 'uploads/68d6239f7c51c5.11311157.png', 'dfgh', '2025-09-29 17:57:15'),
(21, 'cfe', 'Lunch', 23.00, 'uploads/68d62a9ca42191.99713898.png', 'Completely replace the code in your update.php file with this corrected version. The only change is to the sanitize function.', '2025-09-29 18:03:34'),
(22, 'asdf', 'Lunch', 234.00, 'uploads/68d657427b8541.26434268.png', 'sdfghgfdvb', '2025-09-29 18:03:32'),
(23, 'wdefg', 'Specialty', 2.00, 'uploads/68d6d8f85b38e2.02182447.png', 'defgh', '2025-09-29 17:57:20'),
(24, 'ertgh', 'Specialty', 400.00, 'uploads/68d6d906e925d9.11936485.jpg', 'dfg', '2025-09-28 13:03:04'),
(25, 'ert', 'Specialty', 34.00, 'uploads/68d6d914ba4907.26884802.png', 'wertghj', '2025-09-29 17:57:08'),
(26, 'caramel', 'Coffee', 85.00, 'uploads/68d9329e59bee4.03672499.jpg', 'yummy', '2025-09-29 18:03:29'),
(27, 'Chicken Inasal', 'Specialty', 178.00, 'uploads/68dac918c83784.54662868.jpg', 'That perfect bite of Inasal: smoky, tangy, garlicky, and utterly addictive. It\'s the taste of Filipino sunshine.', NULL),
(28, 'Carbonara', 'Specialty', 168.00, 'uploads/68dac99e467982.27663719.jpg', 'Carbonara is a testament to flavor alchemy. Eggs, cheese, pork fat, and pepper—transformed into a silk so rich, you need nothing else.', NULL),
(29, 'Pork Steak', 'Specialty', 178.00, 'uploads/68dac9ed426bc6.00407464.jpg', 'The sound of the sauce simmering, the scent of caramelized onions... Filipino Pork Steak is less a dish, and more a call home.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'his table is designed to store the ratings and comments that guests submit about their reservations. It also includes a', NULL, 1, '2025-09-26 16:03:27'),
(2, 1, 'his table is designed to store the ratings and comments that guests submit about their reservations. It also includes a', NULL, 1, '2025-09-26 16:03:28'),
(3, 1, 'his table is designed to store the ratings and comments that guests submit about their reservations. It also includes a', NULL, 1, '2025-09-26 16:03:28'),
(4, 1, 'cvbnhgsx', NULL, 1, '2025-09-26 16:05:31'),
(5, 14, 'gvfdcvccc', NULL, 1, '2025-09-26 16:10:02'),
(6, 14, 'Good', NULL, 1, '2025-09-26 16:13:46'),
(7, 14, 'You\'ve found a PHP warning bug. The error messages you\'re seeing, \"Constant DB_SERVER already defined', NULL, 1, '2025-09-26 17:04:36'),
(8, 1, 'HElllo Karlll Louis', NULL, 1, '2025-09-27 15:03:15'),
(9, 1, 'sdfgh', NULL, 1, '2025-09-28 12:44:50'),
(10, 14, 'hello', NULL, 1, '2025-09-28 14:16:26'),
(11, 14, 'joshua', NULL, 1, '2025-09-29 07:41:34');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `res_date` date NOT NULL,
  `res_time` time NOT NULL,
  `num_guests` int(11) NOT NULL,
  `res_name` varchar(100) NOT NULL,
  `res_phone` varchar(20) NOT NULL,
  `res_email` varchar(100) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_table` varchar(50) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `is_notified` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `source` varchar(50) NOT NULL DEFAULT 'Online'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `user_id`, `res_date`, `res_time`, `num_guests`, `res_name`, `res_phone`, `res_email`, `status`, `created_at`, `assigned_table`, `table_id`, `is_notified`, `deleted_at`, `source`) VALUES
(15, NULL, '2025-09-16', '11:00:00', 1, 'Vincent paul GNC Pena', '09667785843', 'vincentpaul.pena@gnc.edu.ph', 'Confirmed', '2025-09-16 14:18:15', NULL, NULL, 0, NULL, 'Online'),
(16, NULL, '2025-09-25', '11:00:00', 1, 'Vincent paul', '09667785843', 'vincentpaul.pena@gnc.edu.ph', 'Confirmed', '2025-09-25 07:46:26', NULL, NULL, 0, NULL, 'Online'),
(17, 14, '2025-09-26', '11:00:00', 1, 'Vincent paul D Pena', '09667785843', 'keycm109@gmail.com', 'Cancelled', '2025-09-26 10:14:04', NULL, NULL, 1, NULL, 'Online'),
(18, 14, '2025-09-26', '11:00:00', 1, 'Vincent paul D Pena', '09667785843', 'keycm109@gmail.com', 'Confirmed', '2025-09-26 10:15:37', NULL, NULL, 1, NULL, 'Online'),
(19, 1, '2025-09-26', '11:00:00', 6, 'KIm', '09667785843', 'vincentpaul.pena@gnc.edu.ph', 'Pending', '2025-09-26 12:40:27', NULL, NULL, 0, NULL, 'Online'),
(20, 14, '2025-09-26', '11:00:00', 1, 'Tavern Publico', '09663195259', 'karllouisnavarro@gmail.com', 'Confirmed', '2025-09-26 15:00:23', NULL, NULL, 1, NULL, 'Online'),
(21, 14, '2025-09-26', '11:00:00', 1, 'Tavern', '09663195259', 'karllouisnavarro@gmail.com', 'Confirmed', '2025-09-26 15:10:00', NULL, NULL, 1, NULL, 'Online'),
(22, 14, '2025-09-27', '11:00:00', 1, 'Vincent', '09663195259', 'karllouisnavarro@gmail.com', 'Declined', '2025-09-26 17:03:24', NULL, NULL, 1, NULL, 'Online'),
(23, 14, '2025-09-27', '11:00:00', 56, 'isaac macaraeg', '09667785843', 'vincentpaul.pena@gnc.edu.ph', 'Pending', '2025-09-26 17:26:35', NULL, NULL, 0, NULL, 'Online'),
(24, 14, '2025-09-27', '11:00:00', 12, 'Vincent paul D Pena', '09667785843', 'penapaul858@gmail.com', 'Confirmed', '2025-09-26 17:31:35', NULL, NULL, 1, NULL, 'Online'),
(25, 14, '2025-09-27', '11:00:00', 54, 'Tavern', '09663195259', 'karllouisnavarro@gmail.com', 'Confirmed', '2025-09-26 17:52:10', NULL, NULL, 1, '2025-09-27 14:55:34', 'Online'),
(26, 1, '2025-09-27', '11:00:00', 50, 'Tavern', '09663195259', 'karllouisnavarro@gmail.com', 'Confirmed', '2025-09-27 15:02:30', NULL, NULL, 1, NULL, 'Online'),
(27, 14, '2025-09-28', '11:00:00', 10, 'Kimberly Anne D. Pena', '09663195259', 'karllouisnavarro@gmail.com', 'Confirmed', '2025-09-28 08:29:56', NULL, NULL, 1, NULL, 'Online'),
(28, NULL, '2025-02-12', '20:47:00', 10, 'Vincent paul D Pena', '09667785843', 'keycm109@gmail.com', 'Confirmed', '2025-09-28 09:47:55', NULL, NULL, 0, NULL, 'Walk-in'),
(29, 14, '2025-09-28', '14:00:00', 10, 'ed', '09663195259', 'karllouisnavarro@gmail.com', 'Confirmed', '2025-09-28 10:04:53', NULL, NULL, 1, NULL, 'Online'),
(30, 14, '2025-10-01', '11:00:00', 10, 'James', '09667785843', 'keycm109@gmail.com', 'Confirmed', '2025-09-28 10:35:49', NULL, NULL, 1, NULL, 'Online'),
(0, 14, '2025-10-04', '11:00:00', 20, 'Paul', '09334257317', 'keycm109@gmail.com', 'Confirmed', '2025-10-03 16:40:19', NULL, NULL, 1, NULL, 'Online'),
(0, 14, '2025-10-04', '11:00:00', 520, 'Paul', '09334257317', 'keycm109@gmail.com', 'Confirmed', '2025-10-03 17:30:22', NULL, NULL, 1, NULL, 'Online');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('Available','Unavailable','Maintenance') DEFAULT 'Available',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `bio` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`id`, `name`, `title`, `bio`, `image`, `created_at`, `deleted_at`) VALUES
(1, 'fdghyjuh', 'rtyui', 'rtyuik', 'uploads/68d66bb522e764.00626705.jpg', '2025-09-26 10:32:21', '2025-09-30 02:26:00'),
(2, 'karl', 'CEO', 'FULL STACK', 'uploads/68d9322c4e2517.13457155.jpg', '2025-09-28 13:03:40', '2025-10-03 07:17:14'),
(3, 'Karl Louis Navarro', 'Chef', 'HEHEEHEHEHEHE', 'uploads/68e12940aee965.21936954.jpg', '2025-10-04 14:03:44', '2025-10-04 07:03:51');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `comment` text NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `reservation_id`, `rating`, `comment`, `is_featured`, `created_at`, `deleted_at`) VALUES
(1, 14, 20, 5, 'wonderul', 1, '2025-09-26 15:01:26', NULL),
(2, 14, 18, 3, 'Based on the code, the rating feature will only appear on the homepage under specific conditions. It is not visible in your screenshot because one or more of the following requirements have not been met:', 0, '2025-09-26 15:04:18', '2025-10-04 07:13:47'),
(3, 14, 24, 3, 'dfghn', 1, '2025-09-26 18:02:39', '2025-09-28 21:20:14'),
(4, 14, 25, 3, 'dsfghj', 0, '2025-09-26 18:05:48', NULL),
(5, 14, 21, 3, 'sdfgh', 1, '2025-09-26 18:10:52', '2025-10-03 07:28:02'),
(6, 14, 30, 2, 'thank you', 1, '2025-09-29 07:39:51', '2025-09-29 15:46:02'),
(7, 14, 27, 3, 'You are right! My apologies, it looks like a default style from the icon library was overriding the rule meant to hide the icon on desktops.\r\n\r\nLet&#039;s apply a more specific and forceful CSS rule to fix this immediately.', 0, '2025-09-29 16:29:04', '2025-09-30 02:13:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `avatar` varchar(255) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `verification_token`, `is_verified`, `is_admin`, `avatar`, `mobile`, `birthday`, `created_at`, `deleted_at`) VALUES
(1, 'admin', 'keycm109@gmail.com', '$2y$10$/3fYTIq9ymjPWjHRo9TVoOrTaDtdzRQ69miUzRMdbWL6HU3aXuOVe', NULL, 1, 1, 'uploads/avatars/68d6892138f3e7.41591577.jpg', NULL, NULL, '2025-07-16 15:38:28', NULL),
(14, 'user', 'penapaul858@gmail.com', '$2y$10$PCJ8NoYx/TzZnoAVVo63euwQ5yGQpAGl0h61xmWe1X/ngnBI5AShu', 'NULL', 1, 0, 'uploads/avatars/68e00952df8762.55667764.jpg', '09334257317', '2002-02-12', '2025-09-25 09:10:18', NULL),
(39, 'axus', 'publicotavern@gmail.com', '$2y$10$OagnXQ0.yCAxvrXnnbL8Te4TgGvdIXx9AcjhASDq3kAceDwTEynh2', '105399c5c91f2c19a16209f53479579811629b5055b8f3cd7ba98e2b2a0dec0e24cc3cf59dc2f849f3da59e500bde5fb4d33', 0, 0, NULL, NULL, NULL, '2025-10-02 15:50:54', '2025-10-03 14:37:45'),
(40, 'hello', 'vince@gmail.com', '$2y$10$0ISskCZFwGPDGljGw.TDUudCxVjZXOGSvYDdJsm5M7EHuSDlcIAVu', NULL, 0, 0, NULL, NULL, NULL, '2025-10-03 14:42:38', '2025-10-03 14:42:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `block_date` (`block_date`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `table_name` (`table_name`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_id` (`reservation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
