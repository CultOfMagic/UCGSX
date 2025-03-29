-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('User', 'Administrator') NOT NULL,
    ministry ENUM('UCM', 'CWA', 'CHOIR', 'PWT', 'CYF') NOT NULL,
    status ENUM('Active', 'Deactivated') NOT NULL DEFAULT 'Active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

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
    last_updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

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
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,
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
    request_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Add foreign key relationships with cascading actions
ALTER TABLE borrow_requests
ADD CONSTRAINT fk_borrow_requests_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_borrow_requests_item_id FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE return_requests
ADD CONSTRAINT fk_return_requests_borrow_id FOREIGN KEY (borrow_id) REFERENCES borrow_requests(borrow_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE borrowed_items
ADD CONSTRAINT fk_borrowed_items_request_id FOREIGN KEY (request_id) REFERENCES borrow_requests(borrow_id) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_borrowed_items_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE returned_items
ADD CONSTRAINT fk_returned_items_borrow_id FOREIGN KEY (borrow_id) REFERENCES borrow_requests(borrow_id) ON DELETE CASCADE ON UPDATE CASCADE,
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
