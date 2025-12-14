/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 10.4.32-MariaDB : Database - register_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`register_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `register_db`;

/*Table structure for table `attendance` */

DROP TABLE IF EXISTS `attendance`;

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time NOT NULL,
  `hours_logged` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `attendance` */

LOCK TABLES `attendance` WRITE;

insert  into `attendance`(`id`,`user_id`,`date`,`time_in`,`hours_logged`) values 
(1,8,'2025-10-09','08:58:30',0.00);

UNLOCK TABLES;

/*Table structure for table `issues` */

DROP TABLE IF EXISTS `issues`;

CREATE TABLE `issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `priority` enum('High','Medium','Low') NOT NULL DEFAULT 'Medium',
  `status` enum('Open','In Progress','Resolved') NOT NULL DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `issues_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `issues` */

LOCK TABLES `issues` WRITE;

UNLOCK TABLES;

/*Table structure for table `reports` */

DROP TABLE IF EXISTS `reports`;

CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `reports` */

LOCK TABLES `reports` WRITE;

insert  into `reports`(`id`,`user_id`,`photo`,`comment`,`created_at`) values 
(39,3,'uploads/reports/report_1760013941_user3.png','ff','2025-10-09 20:45:41'),
(42,8,'uploads/reports/report_1760015430_user8.png','dark rayleigh','2025-10-09 21:10:30');

UNLOCK TABLES;

/*Table structure for table `status_logs` */

DROP TABLE IF EXISTS `status_logs`;

CREATE TABLE `status_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `status_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `status_logs` */

LOCK TABLES `status_logs` WRITE;

insert  into `status_logs`(`id`,`user_id`,`status`,`start_time`,`end_time`) values 
(1,8,'Present','2025-10-08 10:23:00','2025-10-08 10:23:03'),
(2,8,'Present','2025-10-08 10:23:03','2025-10-08 10:26:54'),
(3,8,'Present','2025-10-08 10:26:54','2025-10-08 10:26:56'),
(4,8,'Absent','2025-10-08 10:26:56','2025-10-08 10:26:58'),
(5,8,'Present','2025-10-08 10:26:58','2025-10-08 10:26:58'),
(6,8,'On Duty','2025-10-08 10:26:58','2025-10-08 10:27:00'),
(7,8,'Present','2025-10-08 10:27:00','2025-10-08 10:27:00'),
(8,8,'On Break','2025-10-08 10:27:00','2025-10-08 10:27:02'),
(9,8,'Present','2025-10-08 10:27:02','2025-10-08 10:27:02'),
(10,8,'Present','2025-10-08 10:27:02','2025-10-08 10:27:27'),
(11,8,'On Break','2025-10-08 10:27:27','2025-10-08 10:27:34'),
(12,8,'Present','2025-10-08 10:27:34','2025-10-08 10:27:34'),
(13,8,'Excused','2025-10-08 10:27:34','2025-10-08 10:28:00'),
(14,8,'Present','2025-10-08 10:28:00','2025-10-08 10:32:05'),
(15,8,'Absent','2025-10-08 10:32:05','2025-10-08 10:32:07'),
(16,8,'Present','2025-10-08 10:32:07','2025-10-08 10:32:07'),
(17,8,'On Duty','2025-10-08 10:32:07','2025-10-08 10:32:09'),
(18,8,'Present','2025-10-08 10:32:09','2025-10-08 10:32:09'),
(19,8,'On Break','2025-10-08 10:32:09','2025-10-08 10:32:10'),
(20,8,'Present','2025-10-08 10:32:10','2025-10-08 10:32:10'),
(21,8,'Excused','2025-10-08 10:32:10','2025-10-08 10:32:12'),
(22,8,'Present','2025-10-08 10:32:12','2025-10-08 10:32:12'),
(23,8,'On Break','2025-10-08 10:32:12','2025-10-08 10:32:14'),
(24,8,'Present','2025-10-08 10:32:14','2025-10-08 10:32:14'),
(25,8,'On Duty','2025-10-08 10:32:15','2025-10-08 10:32:15'),
(26,8,'Present','2025-10-08 10:32:15','2025-10-08 10:32:15'),
(27,8,'Absent','2025-10-08 10:32:15','2025-10-08 10:32:16'),
(28,8,'Present','2025-10-08 10:32:16','2025-10-08 10:32:16'),
(29,8,'Present','2025-10-08 10:32:16','2025-10-08 10:38:28'),
(30,8,'On Duty','2025-10-08 10:38:28','2025-10-09 08:48:04'),
(31,3,'Present','2025-10-09 08:20:37',NULL),
(32,8,'Present','2025-10-09 08:48:04','2025-10-09 08:59:47'),
(33,8,'Absent','2025-10-09 08:59:47','2025-10-09 08:59:57'),
(34,8,'Present','2025-10-09 08:59:57','2025-10-09 08:59:57'),
(35,8,'On Duty','2025-10-09 08:59:57','2025-10-09 09:03:44'),
(36,8,'Present','2025-10-09 09:03:44','2025-10-09 09:07:44'),
(37,8,'Absent','2025-10-09 09:07:44','2025-10-09 09:07:52'),
(38,8,'Present','2025-10-09 09:07:52','2025-10-09 09:07:52'),
(39,8,'On Duty','2025-10-09 09:07:52','2025-10-09 09:08:00'),
(40,8,'Present','2025-10-09 09:08:00','2025-10-09 09:08:00'),
(41,8,'Present','2025-10-09 09:08:00','2025-10-09 09:08:09'),
(42,8,'On Break','2025-10-09 09:08:09','2025-10-09 09:08:39'),
(43,8,'Present','2025-10-09 09:08:39','2025-10-09 10:00:20'),
(44,8,'On Break','2025-10-09 10:00:20','2025-10-09 10:05:59'),
(45,8,'Present','2025-10-09 10:05:59',NULL),
(46,12,'Present','2025-10-09 10:18:44','2025-10-09 10:20:18'),
(47,12,'Absent','2025-10-09 10:20:18','2025-10-09 10:20:27'),
(48,12,'Present','2025-10-09 10:20:27','2025-10-09 10:20:27'),
(49,12,'Excused','2025-10-09 10:20:27','2025-10-09 10:20:35'),
(50,12,'Present','2025-10-09 10:20:35','2025-10-09 10:20:35'),
(51,12,'On Break','2025-10-09 10:20:35','2025-10-09 10:20:40'),
(52,12,'Present','2025-10-09 10:20:40','2025-10-09 10:20:40'),
(53,12,'On Duty','2025-10-09 10:20:40','2025-10-09 10:20:50'),
(54,12,'Present','2025-10-09 10:20:50',NULL);

UNLOCK TABLES;

/*Table structure for table `task_logs` */

DROP TABLE IF EXISTS `task_logs`;

CREATE TABLE `task_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `scanned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending',
  `hours_logged` decimal(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_tasklogs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `task_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `task_logs` */

LOCK TABLES `task_logs` WRITE;

insert  into `task_logs`(`id`,`user_id`,`task_name`,`scanned_at`,`status`,`hours_logged`) values 
(1,3,'https://www.facebook.com/zab.tugaps/','2025-09-28 00:11:03','completed',0.00),
(2,3,'https://www.facebook.com/zab.tugaps/','2025-09-28 00:11:04','completed',0.00),
(3,3,'https://www.facebook.com/zab.tugaps/','2025-09-28 00:11:04','completed',0.00),
(4,8,'Pick garbages - kiosk','2025-10-09 10:05:38','completed',0.00),
(5,8,'Pick garbages - kiosk','2025-10-09 10:06:28','completed',0.00),
(6,8,'Replace trash bins - Canteen area','2025-10-09 10:06:57','completed',0.00),
(7,8,'Watering plants - Near open area','2025-10-09 10:12:53','completed',0.00),
(8,8,'Mop - Faculty Building','2025-10-09 10:13:21','completed',0.00),
(9,8,'Sanitize bathroom - Near Library','2025-10-09 10:13:28','completed',0.00),
(10,8,'Replace trash bins - Canteen area','2025-10-09 10:13:33','completed',0.00),
(11,12,'Replace trash bins - Canteen area','2025-10-09 10:19:24','completed',0.00),
(12,12,'Replace trash bins - Canteen area','2025-10-09 10:21:05','completed',0.00),
(13,12,'Sweep - First Floor Building','2025-10-09 10:21:18','completed',0.00),
(14,12,'Pick garbages - kiosk','2025-10-09 10:21:22','completed',0.00),
(15,12,'Mop - Faculty Building','2025-10-09 10:21:27','completed',0.00),
(16,12,'Watering plants - Near open area','2025-10-09 10:21:31','completed',0.00);

UNLOCK TABLES;

/*Table structure for table `user_logs` */

DROP TABLE IF EXISTS `user_logs`;

CREATE TABLE `user_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `STATUS` enum('Present','Absent','On Duty','On Break','Excuse') NOT NULL,
  `log_time` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `user_logs` */

LOCK TABLES `user_logs` WRITE;

UNLOCK TABLES;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Present','Absent','On Duty','On Break','Excused') NOT NULL DEFAULT 'Absent',
  `profile_image` varchar(255) DEFAULT 'uploads/default.png',
  `is_active` tinyint(1) DEFAULT 1,
  `is_admin` tinyint(1) DEFAULT 0,
  `employee_id` varchar(50) DEFAULT NULL,
  `availability` enum('available','not_available') NOT NULL DEFAULT 'available',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

LOCK TABLES `users` WRITE;

insert  into `users`(`id`,`username`,`email`,`password`,`role`,`created_at`,`status`,`profile_image`,`is_active`,`is_admin`,`employee_id`,`availability`) values 
(1,'Admin','admin@example.com','$2y$10$h2rV0grbBEsw3mL3sPn4Uue8RFWZxCwL20O5tuvR.lcROZ9vFyyuW','user','2025-09-09 21:43:24','','uploads/default.png',1,1,NULL,'available'),
(3,'admin','admin@gmail.com','$2y$10$8YUsi8S2ZEEA/U/dGo8PmuHorghDZQ9CbZBgVVfptrDxugw7Y0oeK','user','2025-09-18 23:58:33','Present','uploads/profile_3_1759239188.jpeg',1,0,NULL,'available'),
(4,'demo','demo@gmail.com','$2y$10$If1o.zVcUBAYHtE.XXA6aepM48jI.aDkvdctty.u2J91B1RiXACNm','user','2025-09-28 12:47:33','','uploads/default.png',1,0,NULL,'available'),
(8,'sin','sin@gmail.com','$2y$10$2rHuAmbD6hrzb/rqioPqs.eZaXJ1Dg4vWg3pe1F2a/kLTlCnCjX.O','user','2025-09-28 13:16:23','Present','uploads/profile_8_1759068512.jpeg',1,0,NULL,'available'),
(9,'bilbo','bilbo@gmail.com','$2y$10$NHdqY7LQrIiZN6YyM8gXdOMstnH8KN3vp2BOhJmu7a56ck5BN8k6m','user','2025-09-28 13:34:22','','uploads/default.png',1,0,NULL,'available'),
(10,'baboj','baboj@gmail.com','$2y$10$JUGBC9L2BfUf8KZYc8Kh..MOyXyX8CodM88Hjdsp9wj9nvk/6ikUm','user','2025-09-30 17:15:00','','uploads/default.png',1,0,NULL,'available'),
(11,'sukuna','sukuna@gmail.com','$2y$10$MFdcHnypWGtLOstt8PMLh.heRTgDt.ySGv/m8z9IaiMZid1Ufx8T6','user','2025-10-06 23:03:37','','uploads/default.png',1,0,NULL,'available'),
(12,'kiko','kiko@gmail.com','$2y$10$n2Fv4QKcgbHQcs5ypWpwleESKBRwbUxvE4V7VPEz7hL4sCucsVd4K','user','2025-10-09 10:18:30','Present','uploads/default.png',1,0,NULL,'available'),
(15,'lemon','lemon@gmail.com','$2y$10$FF6tqcicQ.a8esTyAgXfi.7ekn6UmSb.2fsxLJZMPIZ.yKnXn4vAy','user','2025-10-09 21:59:42','Absent','uploads/default.png',1,0,NULL,'available');

UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
