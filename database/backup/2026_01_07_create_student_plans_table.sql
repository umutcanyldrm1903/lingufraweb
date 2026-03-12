-- Create the student_plans table (MySQL)
-- Import this file in phpMyAdmin if you are not running Laravel migrations.

CREATE TABLE IF NOT EXISTS `student_plans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `display_title` VARCHAR(255) NULL,
  `label` VARCHAR(255) NULL,
  `subtitle` VARCHAR(255) NULL,
  `tagline` TEXT NULL,
  `duration_months` INT UNSIGNED NOT NULL DEFAULT 0,
  `lessons_total` INT UNSIGNED NOT NULL DEFAULT 0,
  `cancel_total` INT UNSIGNED NOT NULL DEFAULT 0,
  `old_price` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `price` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_plans_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional seed (you can also add/edit these from Admin > Paketler)
INSERT INTO `student_plans`
  (`key`, `title`, `display_title`, `label`, `subtitle`, `tagline`, `duration_months`, `lessons_total`, `cancel_total`, `old_price`, `price`, `featured`, `is_active`, `sort_order`, `created_at`, `updated_at`)
VALUES
  ('plan_3m', 'CORE STARTER', '🥉 CORE STARTER', NULL, NULL, NULL, 3, 24, 1, 48000.00, 36000.00, 0, 1, 0, NOW(), NOW()),
  ('plan_6m', 'PROGRESS BUILDER', '🥈 PROGRESS BUILDER', 'En Çok Tercih Edilen', 'En çok tercih edilen gelişim paketi', NULL, 6, 48, 2, 96000.00, 48000.00, 1, 1, 0, NOW(), NOW()),
  ('plan_12m', 'PREMIUM PAKET', '🥇 PREMIUM PAKET ⭐', 'En İyi Değer', 'En iyi değer – En yüksek indirim', 'En düşük ders başı ücret + maksimum ilerleme.\nGerçek dönüşüm isteyenler için.', 12, 96, 4, 192000.00, 67200.00, 0, 1, 0, NOW(), NOW());

