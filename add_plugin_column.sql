-- Add plugin column to navigation table for storing field-plugin mappings
-- This column stores JSON data mapping field names to plugin names

ALTER TABLE `navigation`
ADD COLUMN IF NOT EXISTS `plugin` TEXT NULL AFTER `urn`;

-- Example of data format that will be stored:
-- {"meeting_link": "Experte Meetings", "file_upload": "Document Manager"}