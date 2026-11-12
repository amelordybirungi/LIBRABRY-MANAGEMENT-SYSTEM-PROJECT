
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `authors` WRITE;
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
INSERT INTO `authors` (`id`, `name`, `country`, `created_at`) VALUES
(1,'Stephen King','United States','2026-11-09 14:17:43'),
(2,'Shirley Jackson','United States','2026-11-09 14:17:43'),
(3,'Silvia Moreno-Garcia','Mexico','2026-11-09 14:17:43'),
(4,'James Clear','United States','2026-11-09 14:17:43'),
(5,'Cal Newport','United States','2026-11-09 14:17:43'),
(6,'J.R.R. Tolkien','United Kingdom','2026-11-09 14:17:43'),
(7,'Brandon Sanderson','United States','2026-11-09 14:17:43'),
(8,'Gregory Maguire','United States','2026-11-09 14:17:43'),
(9,'Susan Kay','United Kingdom','2026-11-09 14:17:43'),
(10,'Taylor Jenkins Reid','United States','2026-11-09 14:17:43'),
(11,'Colleen Hoover','United States','2026-11-09 14:17:43');
/*!40000 ALTER TABLE `authors` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isbn` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `publisher` varchar(150) NOT NULL,
  `year_published` int(11) NOT NULL,
  `total_copies` int(11) NOT NULL DEFAULT 1,
  `available_copies` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `fk_books_authors` (`author_id`),
  KEY `fk_books_categories` (`category_id`),
  CONSTRAINT `fk_books_authors` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_books_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_year_published` CHECK (`year_published` > 0),
  CONSTRAINT `chk_total_copies` CHECK (`total_copies` >= 0),
  CONSTRAINT `chk_available_copies_min` CHECK (`available_copies` >= 0),
  CONSTRAINT `chk_available_copies_max` CHECK (`available_copies` <= `total_copies`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` (`id`, `isbn`, `title`, `author_id`, `category_id`, `publisher`, `year_published`, `total_copies`, `available_copies`, `created_at`) VALUES
(1,'9780385086950','Carrie',1,1,'Doubleday',1974,4,3,'2026-11-09 14:17:43'),
(2,'9780385121675','The Shining',1,1,'Doubleday',1977,3,2,'2026-11-09 14:17:43'),
(3,'9780143039983','The Haunting of Hill House',2,1,'Penguin Classics',1959,2,2,'2026-11-09 14:17:43'),
(4,'9780525620785','Mexican Gothic',3,1,'Del Rey',2020,3,2,'2026-11-09 14:17:43'),
(5,'9780735211292','Atomic Habits',4,3,'Avery',2018,5,4,'2026-11-09 14:17:43'),
(6,'9781455586691','Deep Work',5,3,'Grand Central Publishing',2016,4,3,'2026-11-09 14:17:43'),
(7,'9780547928227','The Hobbit',6,2,'Houghton Mifflin',1937,3,2,'2026-11-09 14:17:43'),
(8,'9780765311788','Mistborn: The Final Empire',7,2,'Tor Books',2006,2,2,'2026-11-09 14:17:43'),
(9,'9780060987107','Wicked: The Life and Times of the Wicked Witch of the West',8,2,'ReganBooks',1995,2,2,'2026-11-09 14:17:43'),
(10,'9780440210306','Phantom',9,4,'Dell',1990,3,2,'2026-11-09 14:17:43'),
(11,'9781501161933','The Seven Husbands of Evelyn Hugo',10,4,'Atria Books',2017,5,4,'2026-11-09 14:17:43'),
(12,'9781501110368','It Ends with Us',11,4,'Atria Books',2016,4,4,'2026-11-09 14:17:43');
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1,'Horror','Gothic, psychological and supernatural horror fiction.','2026-11-09 14:17:43'),
(2,'Fantasy','Epic and imaginative fantasy worlds and adventures.','2026-11-09 14:17:43'),
(3,'Self-Help & Productivity','Personal development, habits and focused work.','2026-11-09 14:17:43'),
(4,'Romance & Drama','Relationships, emotional conflict and dramatic storytelling.','2026-11-09 14:17:43'),
(5,'Science Fiction','Speculative fiction exploring future technology, space and science.','2026-11-09 16:04:40'),
(6,'Mystery & Thriller','Suspenseful crime, detective and psychological thriller stories.','2026-11-09 16:04:40');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `date_issued` date NOT NULL,
  `due_date` date NOT NULL,
  `date_returned` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_loans_member` (`member_id`),
  KEY `idx_loans_book` (`book_id`),
  KEY `idx_loans_status` (`date_returned`),
  CONSTRAINT `fk_loans_books` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_loans_members` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
INSERT INTO `loans` (`id`, `member_id`, `book_id`, `date_issued`, `due_date`, `date_returned`, `created_at`) VALUES
(1,1,5,'2026-11-06','2026-11-20',NULL,'2026-11-09 14:17:43'),
(2,2,7,'2026-10-28','2026-11-11',NULL,'2026-11-09 14:17:43'),
(3,6,4,'2026-11-02','2026-11-16',NULL,'2026-11-09 14:17:43'),
(4,3,1,'2026-10-20','2026-11-03',NULL,'2026-11-09 14:17:43'),
(5,5,2,'2026-10-15','2026-10-29',NULL,'2026-11-09 14:17:43'),
(6,8,8,'2026-09-10','2026-09-24','2026-09-22','2026-11-09 14:17:43'),
(7,9,12,'2026-10-01','2026-10-15','2026-10-14','2026-11-09 14:17:43'),
(8,14,6,'2026-11-07','2026-11-21',NULL,'2026-11-09 16:04:40'),
(9,15,11,'2026-11-04','2026-11-18',NULL,'2026-11-09 16:04:40'),
(10,16,10,'2026-10-22','2026-11-05',NULL,'2026-11-09 16:04:40'),
(11,2,9,'2026-09-20','2026-10-04','2026-10-02','2026-11-09 16:04:40');
/*!40000 ALTER TABLE `loans` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_number` varchar(50) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `date_joined` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_number` (`member_number`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` (`id`, `member_number`, `full_name`, `email`, `phone`, `photo`, `status`, `date_joined`, `created_at`) VALUES
(1,'MEM-001','John Bobson','jBobson@gmail.com','+11-55-095-101','m1794223547_a4ac0f37ec.jpg','active','2024-01-15','2026-11-02 12:34:43'),
(2,'MEM-002','Tom Banners','tombanners@gmail.com','0740295289','m1794223274_fb5e1c4e49.jpg','active','2024-02-10','2026-11-02 12:34:43'),
(3,'MEM-003','Lewis Stones','lewisstones@un.org.uk','+306-70-112-233','m1794223427_3e5d85b60b.jpg','active','2024-03-05','2026-11-02 12:34:43'),
(4,'MEM-004','Abigail hardman','abigailhardman@gmail.com','+254-700-112233','m1794239838_43d36e7dbc.jpg','active','2024-04-12','2026-11-02 12:34:43'),
(5,'MEM-005','David Phillips','davidphilips@gmail.com','+901-345-12-05',NULL,'suspended','2024-05-20','2026-11-02 12:34:43'),
(6,'MEM-006','Emily Davis','emily.davis@gmail.com','+1-555-0106',NULL,'suspended','2024-06-01','2026-11-02 12:34:43'),
(8,'MEM-008','Elizabeth Mpotya','elizmpotya@gmail.com.com','+256-725-55-0108','m1794223949_fd5217f38c.jpg','active','2024-07-04','2026-11-02 12:34:43'),
(9,'MEM-009','Nikita Swims','nikitaswims@beautyservices.com','+09-225-225-509','m1794223697_4ad15915f7.jpg','active','2024-08-11','2026-11-02 12:34:43'),
(14,'MEM-007','Ian Ace','ianace@gmail.com','07090078010','m1794239400_be0bb85af7.jpg','active','2026-11-09','2026-11-09 15:50:01'),
(15,'MEM-010','Henry Amadrabo','henrya@gmail.com','0745173714','m1794239478_3c9b58a5a1.jpg','active','2026-11-09','2026-11-09 15:51:18'),
(16,'MEM-011','Barbara Mukwaya','barbaram@gmail.com','+256 749 8978 00','m1794239701_72051eca24.jpg','active','2026-11-09','2026-11-09 15:54:06');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `role` enum('admin','librarian') NOT NULL DEFAULT 'librarian',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1,'admin','$2y$10$e5JUJW7Co1NBjY.cJPx.Lu/xxwbTsYc0kZtowEyJICqRMY.kPmCrS','Abigail','admin','2026-11-02 12:34:43'),
(2,'librarian','$2y$10$TQCWHLChcymEiD7.7gPxW.qe8YpUPEhZqqNXv75xShj3mNnmXC6oa','Alex','librarian','2026-11-02 12:34:43');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

