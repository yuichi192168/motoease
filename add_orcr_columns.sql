-- Add OR/CR document columns to client_list table
ALTER TABLE `client_list` 
ADD COLUMN `or_document` TEXT DEFAULT NULL AFTER `or_cr_file_path`,
ADD COLUMN `cr_document` TEXT DEFAULT NULL AFTER `or_document`;
