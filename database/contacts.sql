-- Tambahkan ke database framesmile
-- Jalankan di phpMyAdmin atau auto-create sudah ada di contact.php

USE `framesmile`;

CREATE TABLE IF NOT EXISTS `contacts` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(100)  NOT NULL,
    `email`      VARCHAR(100)  NOT NULL,
    `message`    TEXT          NOT NULL,
    `ip_address` VARCHAR(45),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
