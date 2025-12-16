-- Add field_options column to navigation table for storing field options for select/radio/checkbox
-- This column stores JSON data mapping field names to comma-separated option values

ALTER TABLE `navigation`
ADD COLUMN IF NOT EXISTS `field_options` TEXT NULL AFTER `field_required`;

-- Example of data format that will be stored:
-- {"status": "Active,Inactive,Pending", "priority": "Low,Medium,High", "category": "Sales,Support,Billing"}
-- Options are comma-separated strings that will be split into arrays in the application