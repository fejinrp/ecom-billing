-- ============================================================
--  Lucky Draw Integration — MySQL Setup Script
--  Application : ecom-billing
--  Generated   : 2026-07-05
--
--  Run this file once to activate the Lucky Draw module.
--  Safe to run multiple times (uses IF NOT EXISTS / IF EXISTS).
-- ============================================================


-- ============================================================
-- STEP 1: Create lucky_draw_settings table
--         Stores all configurable business rules (categories,
--         thresholds, batch sizes, prize amounts).
-- ============================================================

CREATE TABLE IF NOT EXISTS `lucky_draw_settings` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_key`   VARCHAR(255)    NOT NULL COMMENT 'Unique slug, e.g. bronze, premium',
  `category_label` VARCHAR(255)    NOT NULL COMMENT 'Display name, e.g. Bronze',
  `min_amount`     DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Inclusive lower order total bound',
  `max_amount`     DECIMAL(10,2)       NULL DEFAULT NULL  COMMENT 'Inclusive upper bound; NULL = no limit',
  `batch_size`     INT             NOT NULL               COMMENT 'Entries required before draw unlocks',
  `prize_amount`   DECIMAL(10,2)   NOT NULL DEFAULT 1000.00,
  `is_active`      TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`     TIMESTAMP           NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lucky_draw_settings_category_key_unique` (`category_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- STEP 2: Create lucky_draw_winners table
--         Stores every draw result.
--         Supports both offline (order_id) and online
--         (uorder_id) customers via nullable FK pattern.
-- ============================================================

CREATE TABLE IF NOT EXISTS `lucky_draw_winners` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category`       VARCHAR(255)    NOT NULL COMMENT 'Matches lucky_draw_settings.category_key',
  `batch_no`       INT             NOT NULL COMMENT 'Sequential batch number per category',
  `source`         VARCHAR(50)     NOT NULL COMMENT 'offline | online',
  `order_id`       BIGINT UNSIGNED     NULL DEFAULT NULL COMMENT 'FK → orders.order_id (offline)',
  `uorder_id`      BIGINT UNSIGNED     NULL DEFAULT NULL COMMENT 'FK → uorder.orderid (online)',
  `winner_name`    VARCHAR(255)    NOT NULL,
  `winner_mobile`  VARCHAR(50)     NOT NULL,
  `prize_amount`   DECIMAL(10,2)   NOT NULL DEFAULT 1000.00,
  `drawn_by`       BIGINT UNSIGNED     NULL DEFAULT NULL COMMENT 'FK → ausers.user_id (audit)',
  `created_at`     TIMESTAMP           NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ldw_category_batch`  (`category`, `batch_no`),
  KEY `ldw_order_id`        (`order_id`),
  KEY `ldw_uorder_id`       (`uorder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- STEP 3: Add lucky draw tracking columns to orders table
--         (offline / walk-in sales)
-- ============================================================

ALTER TABLE `orders`
  ADD COLUMN  `lucky_draw_batch_no` INT          NULL DEFAULT NULL
    COMMENT 'Batch number this order was used in' AFTER `order_status`,
  ADD COLUMN `lucky_draw_cat`       VARCHAR(50) NULL DEFAULT NULL
    COMMENT 'Draw category key (bronze / premium …)' AFTER `lucky_draw_batch_no`;

-- Index for fast undrawn-order lookups
CREATE INDEX  `orders_lucky_draw_idx`
  ON `orders` (`lucky_draw_batch_no`, `order_status`);


-- ============================================================
-- STEP 4: Add lucky draw tracking columns to uorder table
--         (online / registered-user orders)
-- ============================================================

ALTER TABLE `uorder`
  ADD COLUMN `lucky_draw_batch_no` INT          NULL DEFAULT NULL
    COMMENT 'Batch number this order was used in' AFTER `ostatus`,
  ADD COLUMN  `lucky_draw_cat`       VARCHAR(50) NULL DEFAULT NULL
    COMMENT 'Draw category key (bronze / premium …)' AFTER `lucky_draw_batch_no`;

-- Index for fast undrawn-order lookups
CREATE INDEX  `uorder_lucky_draw_idx`
  ON `uorder` (`lucky_draw_batch_no`, `ostatus`);


-- ============================================================
-- STEP 5: Seed default draw categories
--         Bronze  — orders ₹0.01 – ₹999.99, batch of 40
--         Premium — orders ₹1000+,           batch of 20
--
--         Uses INSERT … ON DUPLICATE KEY UPDATE so re-running
--         this script won't create duplicates.
-- ============================================================

INSERT INTO `lucky_draw_settings`
  (`category_key`, `category_label`, `min_amount`, `max_amount`, `batch_size`, `prize_amount`, `is_active`, `created_at`, `updated_at`)
VALUES
  ('bronze',  'Bronze',  0.01,    999.99, 40, 1000.00, 1, NOW(), NOW()),
  ('premium', 'Premium', 1000.00, NULL,   20, 1000.00, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `category_label` = VALUES(`category_label`),
  `min_amount`     = VALUES(`min_amount`),
  `max_amount`     = VALUES(`max_amount`),
  `batch_size`     = VALUES(`batch_size`),
  `prize_amount`   = VALUES(`prize_amount`),
  `is_active`      = VALUES(`is_active`),
  `updated_at`     = NOW();


-- ============================================================
-- STEP 6: Register migrations in Laravel's migrations table
--         so php artisan migrate doesn't re-run them.
-- ============================================================

INSERT IGNORE INTO `migrations` (`migration`, `batch`)
SELECT migration, (SELECT IFNULL(MAX(`batch`), 0) + 1 FROM `migrations`) AS batch
FROM (
  SELECT '2026_07_05_000001_create_lucky_draw_settings_table'        AS migration
  UNION ALL
  SELECT '2026_07_05_000002_create_lucky_draw_winners_table'
  UNION ALL
  SELECT '2026_07_05_000003_add_lucky_draw_tracking_to_orders_and_uorder'
) AS m
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` WHERE `migration` = m.migration
);


-- ============================================================
-- DONE — Lucky Draw module is ready.
-- Navigate to:  Admin Panel → Lucky Draw → Draw Board
-- ============================================================
