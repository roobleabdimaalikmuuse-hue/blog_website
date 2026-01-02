-- Kulmiye Blog System Database Schema
-- Created: 2025-12-08
-- Updated: 2026-01-02 (Fixed Drop Order & Admin Password)
-- Database: blog_db

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ========================================================
-- 1. DROP TABLES (In Correct Order to avoid Foreign Key Errors)
-- ========================================================
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `post_tags`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `admins`;

-- ========================================================
-- 2. CREATE TABLES
-- ========================================================

-- Table structure for `admins`
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (Username: Admin, Password: admin1122)
INSERT INTO `admins` (`username`, `password`, `email`) VALUES
('Admin', '$2y$10$uQdHpJ6AYuss6PSyj7qOkeayjYaAsQ4tetGkLUsP7eTcAjK5Kr5DW', 'admin@kulmiye.com');

-- Table structure for `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `profile_image` varchar(255) DEFAULT 'default.jpg',
  `remember_token` varchar(255) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample users
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Table structure for `categories`
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample categories
INSERT INTO `categories` (`name`, `description`) VALUES
('News', 'Latest global and local news updates'),
('History', 'Exploring historical events, culture, and stories'),
('Technology', 'Artificial Intelligence (AI) and latest tech innovations');

-- Table structure for `posts`
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `author_type` enum('user','admin') NOT NULL DEFAULT 'admin',
  `category_id` int(11) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `author_id` (`author_id`),
  FULLTEXT KEY `search_idx` (`title`,`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample posts
INSERT INTO `posts` (`title`, `slug`, `content`, `excerpt`, `author_id`, `author_type`, `category_id`, `status`, `views`) VALUES
('Welcome to Kulmiye Blog', 'welcome-to-kulmiye-blog', '<p>Welcome to <strong>Kulmiye</strong>, your premier destination for insightful articles, engaging stories, and valuable knowledge across various topics.</p><p>Our mission is to provide high-quality content that informs, inspires, and empowers our readers. Whether you\'re interested in technology, lifestyle, travel, or business, we\'ve got you covered.</p><p>Stay tuned for regular updates and join our growing community of readers!</p>', 'Welcome to Kulmiye, your premier destination for insightful articles and engaging stories.', 1, 'admin', 1, 'published', 150),
('10 Essential Web Development Tools in 2025', '10-essential-web-development-tools-2025', '<p>Web development continues to evolve rapidly. Here are the top 10 tools every developer should know in 2025:</p><ol><li><strong>VS Code</strong> - The most popular code editor</li><li><strong>Git & GitHub</strong> - Version control essentials</li><li><strong>Docker</strong> - Containerization platform</li><li><strong>Postman</strong> - API testing tool</li><li><strong>Chrome DevTools</strong> - Browser debugging</li></ol><p>These tools will significantly boost your productivity and code quality.</p>', 'Discover the essential web development tools that will boost your productivity in 2025.', 1, 'admin', 1, 'published', 89),
('The Art of Mindful Living', 'art-of-mindful-living', '<p>In our fast-paced world, mindfulness has become more important than ever. Learn how to incorporate mindful practices into your daily routine.</p><p><strong>Key practices:</strong></p><ul><li>Morning meditation</li><li>Mindful eating</li><li>Digital detox</li><li>Gratitude journaling</li></ul><p>Start small and build consistency. Your mental health will thank you.</p>', 'Discover how to incorporate mindfulness into your daily routine for better mental health.', 2, 'user', 2, 'published', 67),
('Top 5 Hidden Gems in Southeast Asia', 'top-5-hidden-gems-southeast-asia', '<p>Southeast Asia is full of incredible destinations beyond the usual tourist spots. Here are 5 hidden gems you must visit:</p><ol><li><strong>Luang Prabang, Laos</strong> - Ancient temples and waterfalls</li><li><strong>Hoi An, Vietnam</strong> - Charming old town</li><li><strong>Pai, Thailand</strong> - Mountain paradise</li><li><strong>Flores, Indonesia</strong> - Pristine beaches</li><li><strong>Bohol, Philippines</strong> - Chocolate Hills</li></ol>', 'Explore the most beautiful hidden destinations in Southeast Asia that tourists often miss.', 1, 'admin', 3, 'published', 123);

-- Table structure for `tags`
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample tags
INSERT INTO `tags` (`name`) VALUES
('beginner'),
('tutorial'),
('guide'),
('tips'),
('news'),
('review'),
('productivity'),
('health'),
('adventure');

-- Table structure for `post_tags`
CREATE TABLE `post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample post-tag relationships
INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES
(1, 5),
(2, 2),
(2, 7),
(3, 8),
(3, 4),
(4, 3),
(4, 9);

-- Table structure for `comments`
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample comments
INSERT INTO `comments` (`post_id`, `user_id`, `content`, `status`) VALUES
(1, 1, 'Great article! Looking forward to more content.', 'approved'),
(1, 2, 'Welcome to the community!', 'approved'),
(2, 2, 'Very helpful tools list. Thanks for sharing!', 'approved'),
(3, 1, 'Mindfulness has changed my life. Highly recommend!', 'pending');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
