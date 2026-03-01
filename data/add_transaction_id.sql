-- Add transaction_id column to orders table for payment tracking
USE smartdine;

ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS transaction_id VARCHAR(100) NULL AFTER payment_method;
