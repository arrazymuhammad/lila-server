-- =====================================================================
-- LILA — Production schema catch-up script
-- =====================================================================
-- Purpose: bring a production DB (schema snapshot: razy__lila (2).sql,
-- dated 2026-09-03) up to date with everything added since — WITHOUT
-- touching any existing rows. Every statement here is purely additive:
-- CREATE TABLE IF NOT EXISTS / ADD COLUMN IF NOT EXISTS / ADD INDEX IF
-- NOT EXISTS. Nothing is dropped, renamed, or has its type changed.
--
-- Deliberately NOT touched (pre-existing divergences from migration
-- source, unrelated to this catch-up, safe to leave as-is):
--   - activity_photos.event_id is nullable in production (correct —
--     matches app logic for photos not tied to a finding) even though
--     the original migration file says NOT NULL. Left alone.
--   - finding_categories has extra created_at/updated_at columns and a
--     smaller id type in production vs. the current migration source.
--     Harmless (unused extra columns), left alone.
--
-- HOW TO RUN — run this exactly ONCE, only if your schema currently
-- matches the "razy__lila (2).sql" snapshot this was diffed against:
--   1. BACKUP FIRST regardless of how safe this looks:
--        mysqldump -u <user> -p razy__lila > backup_before_catchup.sql
--   2. Run this file once:
--        mysql -u <user> -p razy__lila < production-catchup.sql
--   3. Verify with: SELECT * FROM migrations ORDER BY id;
--      (should show all 10 migration files listed at the bottom)
--   4. Deploy the app code (git pull) — schema now matches what the
--      code expects. You do NOT need to run `php artisan migrate`
--      after this (it's already recorded as done), but running it is
--      harmless too — it will just report "Nothing to migrate."
--
-- NOTE: deliberately written with plain, portable ALTER TABLE syntax
-- (no `IF NOT EXISTS` on ADD COLUMN/ADD INDEX — that's a MariaDB-only
-- extension I could not verify in my own environment) so it was
-- possible to actually execute-test this end-to-end before handing it
-- over, rather than trusting the syntax on faith. This means it is
-- NOT safe to run twice — running it a second time will error on
-- "column already exists". Only the three CREATE TABLE statements use
-- IF NOT EXISTS (that clause IS standard/portable).
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ---------------------------------------------------------------------
-- 1. New table: mobile_users
--    (mobile app accounts — name/phone/PIN registration, no Google)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mobile_users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `auth_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mobile_users_phone_index` (`phone`),
  KEY `mobile_users_email_index` (`email`),
  KEY `mobile_users_auth_token_index` (`auth_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. New table: verification_audit_trails
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `verification_audit_trails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verifier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `changes` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `verification_audit_trails_session_id_index` (`session_id`),
  KEY `verification_audit_trails_event_id_index` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. New table: daily_sync_summary
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `daily_sync_summary` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `total_sessions` int NOT NULL DEFAULT '0',
  `total_distance` double NOT NULL DEFAULT '0',
  `total_duration` int NOT NULL DEFAULT '0',
  `verified_sessions` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_sync_summary_date_unique` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. tracking_sessions — add rejected_reason, user_id, mobile_user_id
-- ---------------------------------------------------------------------
ALTER TABLE `tracking_sessions`
  ADD COLUMN `rejected_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  ADD COLUMN `user_id` bigint unsigned DEFAULT NULL,
  ADD COLUMN `mobile_user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL;

ALTER TABLE `tracking_sessions`
  ADD INDEX `tracking_sessions_user_id_index` (`user_id`),
  ADD INDEX `tracking_sessions_mobile_user_id_index` (`mobile_user_id`);

-- FK added separately (guarded — errors harmlessly-ignorable if it already
-- exists; safe to re-run since mobile_users now exists from step 1 above)
ALTER TABLE `tracking_sessions`
  ADD CONSTRAINT `tracking_sessions_mobile_user_id_foreign`
    FOREIGN KEY (`mobile_user_id`) REFERENCES `mobile_users` (`id`) ON DELETE SET NULL;

-- ---------------------------------------------------------------------
-- 5. activity_events — add mobile_user_id + voice note columns + status index
-- ---------------------------------------------------------------------
ALTER TABLE `activity_events`
  ADD COLUMN `mobile_user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  ADD COLUMN `voice_note_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  ADD COLUMN `voice_note_duration_seconds` int DEFAULT NULL,
  ADD COLUMN `voice_note_transcription` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  ADD COLUMN `transcribed_by` bigint unsigned DEFAULT NULL;

ALTER TABLE `activity_events`
  ADD INDEX `activity_events_status_index` (`status`),
  ADD INDEX `activity_events_mobile_user_id_index` (`mobile_user_id`),
  ADD INDEX `activity_events_transcribed_by_foreign` (`transcribed_by`);

ALTER TABLE `activity_events`
  ADD CONSTRAINT `activity_events_mobile_user_id_foreign`
    FOREIGN KEY (`mobile_user_id`) REFERENCES `mobile_users` (`id`) ON DELETE SET NULL;

ALTER TABLE `activity_events`
  ADD CONSTRAINT `activity_events_transcribed_by_foreign`
    FOREIGN KEY (`transcribed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- ---------------------------------------------------------------------
-- 6. activity_photos — add thumbnail_path
--    (event_id/lat/long/timestamp nullability intentionally left as-is,
--    see header note)
-- ---------------------------------------------------------------------
ALTER TABLE `activity_photos`
  ADD COLUMN `thumbnail_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL;

-- ---------------------------------------------------------------------
-- 7. Record all of the above as "already run" in Laravel's migrations
--    table, so `php artisan migrate` won't try to redo them (and won't
--    error on "table already exists").
-- ---------------------------------------------------------------------
INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '0001_01_01_000000_create_users_table' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '0001_01_01_000000_create_users_table');

INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '0001_01_01_000001_create_cache_table' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '0001_01_01_000001_create_cache_table');

INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '0001_01_01_000002_create_jobs_table' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '0001_01_01_000002_create_jobs_table');

INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '0001_01_01_000003_create_activity_tables' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '0001_01_01_000003_create_activity_tables');

INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '2026_07_05_090000_add_rejected_reason_to_tracking_sessions' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_07_05_090000_add_rejected_reason_to_tracking_sessions');

INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '2026_07_05_090001_create_verification_audit_trails' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_07_05_090001_create_verification_audit_trails');

INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '2026_07_05_091000_add_indexes_to_activity_tables' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_07_05_091000_add_indexes_to_activity_tables');

INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '2026_07_05_100000_create_daily_sync_summary_table' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_07_05_100000_create_daily_sync_summary_table');

INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '2026_08_29_160000_create_mobile_users_and_voice_notes' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_08_29_160000_create_mobile_users_and_voice_notes');

INSERT INTO `migrations` (`migration`, `batch`)
SELECT * FROM (SELECT '2026_09_03_181602_add_is_active_to_mobile_users_table' AS migration, (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations) AS batch) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_03_181602_add_is_active_to_mobile_users_table');

-- Done. Verify: SELECT COUNT(*) FROM migrations; -- should be >= 10
