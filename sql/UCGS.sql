-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('User', 'Administrator') NOT NULL,
    ministry ENUM('UCM', 'CWA', 'CHOIR', 'PWT', 'CYF') NOT NULL,
    status ENUM('Active', 'Deactivated') NOT NULL DEFAULT 'Active',
    deactivation_end DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Add indexes for performance optimization
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);

-- Create Items Table
CREATE TABLE IF NOT EXISTS items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_no VARCHAR(50) UNIQUE NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    quantity INT NOT NULL,
    unit VARCHAR(20),
    status ENUM('Available', 'Out of Stock', 'Low Stock') NOT NULL,
    reorder_point INT DEFAULT 0,
    model_no VARCHAR(50),
    item_category VARCHAR(50),
    item_location VARCHAR(50),
    expiration DATE DEFAULT NULL,
    brand VARCHAR(50),
    supplier VARCHAR(50),
    price_per_item DECIMAL(10,2),
    deleted_at DATETIME DEFAULT NULL,
    last_updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Example inserts for the items table
INSERT INTO items (item_no, item_name, description, quantity, unit, status, reorder_point, model_no, item_category, item_location, expiration, brand, supplier, price_per_item)
VALUES 
('ITEM001', 'Laptop', 'High-performance laptop', 10, 'pcs', 'Available', 5, 'MOD123', 'Electronics', 'Warehouse A', '2025-12-31', 'Dell', 'TechSupplier Inc.', 1200.00),
('ITEM002', 'Projector', '4K resolution projector', 5, 'pcs', 'Low Stock', 2, 'MOD456', 'Electronics', 'Warehouse B', NULL, 'Epson', 'AV Supplies Co.', 800.00),
('ITEM003', 'Office Chair', 'Ergonomic office chair', 20, 'pcs', 'Available', 10, 'CHAIR789', 'Furniture', 'Warehouse C', NULL, 'Ikea', 'Furniture World', 150.00);

-- Create Reports Table
CREATE TABLE IF NOT EXISTS reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    item_no VARCHAR(50) NOT NULL,
    last_updated DATETIME NOT NULL,
    model_no VARCHAR(50),
    item_name VARCHAR(255),
    description TEXT,
    item_category VARCHAR(50),
    item_location VARCHAR(50),
    expiration DATE DEFAULT NULL,
    brand VARCHAR(50),
    supplier VARCHAR(50),
    price_per_item DECIMAL(10,2),
    quantity INT,
    unit VARCHAR(20),
    status ENUM('Available', 'Out of Stock', 'Low Stock'),
    reorder_point INT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create Borrow Requests Table
CREATE TABLE IF NOT EXISTS borrow_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY, -- Ensure the column name is 'request_id'
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    date_needed DATE NOT NULL,
    return_date DATE NOT NULL,
    purpose TEXT NOT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    request_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    transaction_id INT DEFAULT NULL,
    UNIQUE (user_id, item_id, status, date_needed)
);

-- Create Return Requests Table
CREATE TABLE IF NOT EXISTS return_requests (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    borrow_id INT NOT NULL,
    return_date DATE NOT NULL,
    item_condition ENUM('Good', 'Damaged', 'Lost') NOT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    admin_reason TEXT DEFAULT NULL,
    UNIQUE (borrow_id)
);

-- Modify Return Requests Table to add the missing 'quantity' column
ALTER TABLE return_requests
ADD COLUMN quantity INT NOT NULL AFTER borrow_id;

-- Create Borrowed Items Table
CREATE TABLE IF NOT EXISTS borrowed_items (
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    actual_return_date DATE DEFAULT NULL,
    item_condition ENUM('Good', 'Damaged', 'Lost') DEFAULT NULL,
    return_notes TEXT DEFAULT NULL,
    status ENUM('Borrowed', 'Returned', 'Overdue') NOT NULL DEFAULT 'Borrowed',
    user_id INT NOT NULL,
    borrow_date DATE DEFAULT CURRENT_DATE
);

-- Create Returned Items Table
CREATE TABLE IF NOT EXISTS returned_items (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    borrow_id INT NOT NULL,
    return_date DATE NOT NULL,
    item_condition ENUM('Good', 'Damaged', 'Lost') NOT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    processed_by INT DEFAULT NULL
);

-- Create Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    type ENUM('Info', 'Warning', 'Error') DEFAULT 'Info'
);

-- Create Audit Logs Table
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create Transactions Table
CREATE TABLE IF NOT EXISTS transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action ENUM('Borrow', 'Return', 'Request', 'Approval') NOT NULL,
    details TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    item_name VARCHAR(255) DEFAULT NULL,
    quantity INT DEFAULT NULL,
    status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
    item_id INT DEFAULT NULL
);

-- Create User Requests Table
CREATE TABLE IF NOT EXISTS user_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    admin_reason TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create Item Returns Table
CREATE TABLE IF NOT EXISTS item_returns (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    return_date DATE NOT NULL,
    item_condition ENUM('Good', 'Damaged', 'Lost') NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create New Item Requests Table
CREATE TABLE IF NOT EXISTS new_item_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    item_category VARCHAR(50) NOT NULL,
    item_id INT DEFAULT NULL,
    quantity INT NOT NULL,
    purpose TEXT NOT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    ministry ENUM('UCM', 'CWA', 'CHOIR', 'PWT', 'CYF') NOT NULL,
    request_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Add foreign key relationships with cascading actions
ALTER TABLE borrow_requests
ADD CONSTRAINT fk_borrow_requests_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_borrow_requests_item_id FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE return_requests
ADD CONSTRAINT fk_return_requests_borrow_id FOREIGN KEY (borrow_id) REFERENCES borrow_requests(request_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE borrowed_items
ADD CONSTRAINT fk_borrowed_items_request_id FOREIGN KEY (request_id) REFERENCES borrow_requests(request_id) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_borrowed_items_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE returned_items
ADD CONSTRAINT fk_returned_items_borrow_id FOREIGN KEY (borrow_id) REFERENCES borrow_requests(request_id) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_returned_items_processed_by FOREIGN KEY (processed_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT fk_returned_items_user_id FOREIGN KEY (processed_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE notifications
ADD CONSTRAINT fk_notifications_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE audit_logs
ADD CONSTRAINT fk_audit_logs_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE transactions
ADD CONSTRAINT fk_transactions_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_transactions_item_id FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE user_requests
ADD CONSTRAINT fk_user_requests_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE item_returns
ADD CONSTRAINT fk_item_returns_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_item_returns_item_id FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE new_item_requests
ADD CONSTRAINT fk_new_item_requests_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_new_item_requests_item_id FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE SET NULL ON UPDATE CASCADE;

-- Modify Reports Table to add foreign key relationships
ALTER TABLE reports
ADD CONSTRAINT fk_reports_item_no FOREIGN KEY (item_no) REFERENCES items(item_no) ON DELETE CASCADE ON UPDATE CASCADE;

-- Add missing indexes for performance optimization
CREATE INDEX idx_items_item_name ON items(item_name);
CREATE INDEX idx_new_item_requests_status ON new_item_requests(status);
CREATE INDEX idx_borrow_requests_status ON borrow_requests(status);
CREATE INDEX idx_notifications_is_read ON notifications(is_read);
CREATE INDEX idx_notifications_type ON notifications(type);
CREATE INDEX idx_new_item_requests_ministry ON new_item_requests(ministry);
CREATE INDEX idx_new_item_requests_request_date ON new_item_requests(request_date);
CREATE INDEX idx_items_status ON items(status);
CREATE INDEX idx_items_item_category ON items(item_category);
CREATE INDEX idx_items_item_location ON items(item_location);
CREATE INDEX idx_return_requests_status ON return_requests(status);
CREATE INDEX idx_user_requests_status ON user_requests(status);

-- Modify Items Table to ensure all necessary fields are present
ALTER TABLE items
ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER item_id,
ADD COLUMN updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Modify Items Table to rename updated_at to last_updated
ALTER TABLE items
CHANGE COLUMN updated_at last_updated DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- Ensure indexes for frequently queried columns
CREATE INDEX idx_items_item_no ON items(item_no);
CREATE INDEX idx_items_item_name ON items(item_name);
CREATE INDEX idx_items_status ON items(status);
CREATE INDEX idx_items_item_category ON items(item_category);
CREATE INDEX idx_items_item_location ON items(item_location);
