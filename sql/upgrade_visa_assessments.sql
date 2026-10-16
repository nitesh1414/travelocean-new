-- Upgrade an existing visa_assessments table without dropping existing records.
-- In phpMyAdmin, select the database configured as DB_NAME in includes/config.php,
-- then import/run this file. It adds only columns that are currently missing.
-- Back up the database first. Requires permission to create/drop routines and ALTER the table.

DELIMITER $$
DROP PROCEDURE IF EXISTS `upgrade_visa_assessments_20261009`$$
CREATE PROCEDURE `upgrade_visa_assessments_20261009`()
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.tables
    WHERE table_schema = DATABASE() AND table_name = 'visa_assessments'
  ) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'visa_assessments table is missing; import sql/visa_assessment.sql first';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'id') THEN
    IF EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND extra LIKE '%auto_increment%') THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'visa_assessments has a different auto-increment column; add an id column manually';
    ELSE
      ALTER TABLE `visa_assessments` ADD COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE FIRST;
    END IF;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'full_legal_name') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `full_legal_name` VARCHAR(200) NOT NULL DEFAULT '';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'dob') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `dob` DATE DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'gender') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `gender` VARCHAR(20) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'birthplace_country') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `birthplace_country` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'birthplace_state') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `birthplace_state` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'birthplace_city') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `birthplace_city` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'nationality') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `nationality` VARCHAR(200) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'passport_number') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `passport_number` VARCHAR(50) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'passport_issue') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `passport_issue` DATE DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'passport_expiry') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `passport_expiry` DATE DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'current_residency') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `current_residency` VARCHAR(200) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'address_country') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `address_country` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'address_state') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `address_state` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'address_city') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `address_city` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'address') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `address` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'phone') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `phone` VARCHAR(60) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'email') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `email` VARCHAR(160) NOT NULL DEFAULT '';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'marital_status') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `marital_status` VARCHAR(50) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'spouse_partner_details') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `spouse_partner_details` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'dependent_children') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `dependent_children` INT UNSIGNED DEFAULT 0;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'children_json') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `children_json` JSON DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'family_target_country') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `family_target_country` VARCHAR(10) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'family_target_details') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `family_target_details` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'target_countries') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `target_countries` VARCHAR(200) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'visa_category') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `visa_category` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'intended_travel_date') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `intended_travel_date` DATE DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'expected_duration') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `expected_duration` VARCHAR(50) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'previous_visa_yesno') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `previous_visa_yesno` VARCHAR(10) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'previous_visa_details') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `previous_visa_details` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'refusal_yesno') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `refusal_yesno` VARCHAR(10) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'refusal_details') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `refusal_details` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'education_level') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `education_level` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'institution_country') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `institution_country` VARCHAR(200) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'field_of_study') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `field_of_study` VARCHAR(200) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'graduation_year') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `graduation_year` VARCHAR(10) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'native_language') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `native_language` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'english_proficiency') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `english_proficiency` VARCHAR(50) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'english_test_score') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `english_test_score` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'other_languages') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `other_languages` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'french_spanish_test_score') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `french_spanish_test_score` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'employment_status') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `employment_status` VARCHAR(50) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'job_title') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `job_title` VARCHAR(200) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'employer_industry') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `employer_industry` VARCHAR(200) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'years_experience') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `years_experience` VARCHAR(50) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'employment_summary') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `employment_summary` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'employer1_details') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `employer1_details` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'employer2_details') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `employer2_details` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'source_of_funds') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `source_of_funds` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'liquid_funds') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `liquid_funds` VARCHAR(100) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'monthly_income') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `monthly_income` VARCHAR(50) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'assets_yesno') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `assets_yesno` VARCHAR(10) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'assets_summary') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `assets_summary` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'travel_history_countries') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `travel_history_countries` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'valid_visas_yesno') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `valid_visas_yesno` VARCHAR(10) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'valid_visas_list') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `valid_visas_list` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'criminal_record_yesno') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `criminal_record_yesno` VARCHAR(10) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'criminal_details') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `criminal_details` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'medical_conditions_yesno') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `medical_conditions_yesno` VARCHAR(10) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'medical_details') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `medical_details` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'ip_address') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `ip_address` VARCHAR(60) DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'status') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'pending';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'is_read') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `is_read` TINYINT(1) NOT NULL DEFAULT 0;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'admin_notes') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `admin_notes` TEXT DEFAULT NULL;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'created_at') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_assessments' AND column_name = 'updated_at') THEN
    ALTER TABLE `visa_assessments` ADD COLUMN `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;
  END IF;

END$$
DELIMITER ;

CALL `upgrade_visa_assessments_20261009`();
DROP PROCEDURE `upgrade_visa_assessments_20261009`;

SELECT 'Visa assessment table upgrade complete. Existing data was retained.' AS migration_status;
