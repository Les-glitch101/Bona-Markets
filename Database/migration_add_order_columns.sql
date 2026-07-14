-- ================================================================
-- MIGRATION: Add missing `address` and `payment_id` columns to `orders`
-- ================================================================
-- The checkout flow (Public/orders/create.php) and the order history
-- page (Public/orders/index.php) read/write `orders.address` and
-- `orders.payment_id`, but the original schema.sql did not define
-- these columns. Run this once against any existing `bonamarkets`
-- database that was created before this fix (a fresh import of the
-- updated schema.sql already includes these columns, so this script
-- is only needed for upgrading an existing database).
-- ================================================================

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS address VARCHAR(500) NULL AFTER status,
    ADD COLUMN IF NOT EXISTS payment_id VARCHAR(255) NULL AFTER address;
