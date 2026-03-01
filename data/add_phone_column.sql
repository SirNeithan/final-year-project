-- Add phone number column to orders table
-- Remove requirement for ZIP code (make it optional)

USE smartdine;

-- Add phone column
ALTER TABLE orders 
ADD COLUMN customer_phone VARCHAR(20) NULL AFTER customer_email;

-- Make delivery_zip optional (allow NULL)
ALTER TABLE orders 
MODIFY COLUMN delivery_zip VARCHAR(20) NULL;

-- Verify changes
DESCRIBE orders;
