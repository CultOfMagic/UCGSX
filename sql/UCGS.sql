-- Create Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('User', 'Administrator') NOT NULL,
    ministry ENUM('UCM', 'CWA', 'CHOIR', 'PWT', 'CYF') NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    deleted_at DATETIME
);

-- Create Items Table
CREATE TABLE items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_no VARCHAR(50) UNIQUE NOT NULL,
    last_updated DATETIME NOT NULL,
    model_no VARCHAR(50),
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    location VARCHAR(50),
    expiration DATE,
    brand VARCHAR(50),
    supplier VARCHAR(50),
    price DECIMAL(10,2),
    quantity INT NOT NULL,
    unit VARCHAR(20),
    status ENUM('Available', 'Out of Stock', 'Low Stock') NOT NULL,
    reorder_point INT
);

-- Create Requests Table
CREATE TABLE requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    date_needed DATE,
    return_date DATE,
    purpose TEXT,
    notes TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL,
    request_date DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (item_id) REFERENCES items(item_id)
);

-- Create Borrowed Items Table
CREATE TABLE borrowed_items (
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    actual_return_date DATE,
    item_condition ENUM('Good', 'Damaged', 'Lost'),
    return_notes TEXT,
    status ENUM('Borrowed', 'Returned', 'Overdue'),
    FOREIGN KEY (request_id) REFERENCES requests(request_id)
);

-- Create Returned Items Table
CREATE TABLE returned_items (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    borrow_id INT NOT NULL,
    return_date DATE NOT NULL,
    item_condition ENUM('Good', 'Damaged', 'Lost') NOT NULL,
    notes TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL,
    FOREIGN KEY (borrow_id) REFERENCES borrowed_items(borrow_id)
);