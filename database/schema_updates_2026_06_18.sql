-- SQL Schema Updates for Product Batches, Delivery Notes, and Warranty Periods
-- Date: 2026-06-18

-- 1. Create product_batches table
CREATE TABLE `product_batches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `mfg_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `initial_qty` int(11) NOT NULL,
  `current_qty` int(11) NOT NULL,
  `warranty_months` int(11) NOT NULL DEFAULT '0',
  `prate` decimal(10,2) DEFAULT NULL,
  `srate` decimal(10,2) DEFAULT NULL,
  `mrp` decimal(10,2) DEFAULT NULL,
  `cprice` decimal(10,2) DEFAULT NULL,
  `dprice` decimal(10,2) DEFAULT NULL,
  `sdprice` decimal(10,2) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_batches_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create delivery_notes table
CREATE TABLE `delivery_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `dn_number` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `porder_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `carrier_info` varchar(255) DEFAULT NULL,
  `dn_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_notes_dn_number_unique` (`dn_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create delivery_note_items table
CREATE TABLE `delivery_note_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_note_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `batch_id` bigint(20) unsigned DEFAULT NULL,
  `qty_shipped` int(11) NOT NULL,
  `qty_received` int(11) NOT NULL,
  `qty_damaged` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_note_items_delivery_note_id_foreign` (`delivery_note_id`),
  KEY `delivery_note_items_batch_id_foreign` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Alter products table
ALTER TABLE `products` 
  ADD COLUMN `warranty_months` int(11) NOT NULL DEFAULT '0' AFTER `pcode`;

-- 5. Alter order_item table
ALTER TABLE `order_item` 
  ADD COLUMN `batch_id` bigint(20) unsigned DEFAULT NULL AFTER `prod_id`,
  ADD COLUMN `warranty_expiry_date` date DEFAULT NULL AFTER `qty`;

-- 6. Alter eorder_item table
ALTER TABLE `eorder_item` 
  ADD COLUMN `batch_id` bigint(20) unsigned DEFAULT NULL AFTER `prod_id`,
  ADD COLUMN `warranty_expiry_date` date DEFAULT NULL AFTER `qty`;

-- 7. Alter products table pcode column length to 50
ALTER TABLE `products` MODIFY COLUMN `pcode` VARCHAR(50) NULL;
