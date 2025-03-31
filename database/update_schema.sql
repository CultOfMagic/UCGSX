ALTER TABLE new_item_requests
ADD COLUMN item_unit VARCHAR(50) NOT NULL AFTER quantity;
