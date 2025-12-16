-- Add field_required column to navigation table for storing field required/validation settings
-- This column stores JSON data mapping field names to boolean required status

ALTER TABLE `navigation`
ADD COLUMN IF NOT EXISTS `field_required` TEXT NULL AFTER `field_types`;

-- Example of data format that will be stored:
-- {"email": true, "password": true, "age": true}
-- true = field is required, false/null = field is optional