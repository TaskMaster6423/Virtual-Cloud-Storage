-- Create database
CREATE DATABASE IF NOT EXISTS cloud_storage;
USE cloud_storage;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    account_status ENUM('active', 'suspended', 'deleted') DEFAULT 'active',
    is_verified BOOLEAN DEFAULT FALSE
);

-- Authentication factors table
CREATE TABLE IF NOT EXISTS authentication_factors (
    factor_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    factor_type ENUM('password', 'phone', 'authenticator', 'biometric') NOT NULL,
    factor_value VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_used TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Login attempts table
CREATE TABLE IF NOT EXISTS login_attempts (
    attempt_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    ip_address VARCHAR(45),
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success', 'failed', 'logout') NOT NULL,
    factor_used VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Files table
CREATE TABLE IF NOT EXISTS files (
    file_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size BIGINT,
    file_type VARCHAR(255),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_modified TIMESTAMP NULL,
    is_encrypted BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- File access logs table
CREATE TABLE IF NOT EXISTS file_access_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT,
    user_id INT NULL,
    access_type ENUM('read', 'write', 'delete', 'share', 'shared_download') NOT NULL,
    access_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (file_id) REFERENCES files(file_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- File shares table
CREATE TABLE IF NOT EXISTS file_shares (
    share_id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT,
    user_id INT,
    share_email VARCHAR(100) NOT NULL,
    share_token VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (file_id) REFERENCES files(file_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Security questions table
CREATE TABLE IF NOT EXISTS security_questions (
    question_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    question VARCHAR(255) NOT NULL,
    answer_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- User devices table
CREATE TABLE IF NOT EXISTS user_devices (
    device_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    device_name VARCHAR(100),
    device_type VARCHAR(50),
    device_identifier VARCHAR(255) UNIQUE,
    last_used TIMESTAMP NULL,
    is_trusted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Backup codes table
CREATE TABLE IF NOT EXISTS backup_codes (
    code_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    code_hash VARCHAR(255) NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert a test user (password: test123)
INSERT INTO users (username, email, password_hash, first_name, last_name, phone_number, account_status, is_verified)
VALUES ('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User', '1234567890', 'active', TRUE);

-- Insert authentication factors for test user
INSERT INTO authentication_factors (user_id, factor_type, factor_value, is_active)
VALUES (1, 'password', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE),
       (1, 'phone', '1234567890', TRUE),
       (1, 'authenticator', 'test_secret', TRUE);

-- Insert some backup codes for test user
INSERT INTO backup_codes (user_id, code_hash, is_used, expires_at)
VALUES 
(1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE, DATE_ADD(NOW(), INTERVAL 1 YEAR)),
(1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE, DATE_ADD(NOW(), INTERVAL 1 YEAR));

-- Create indexes for better performance
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_user_factor ON authentication_factors(user_id, factor_type);
CREATE INDEX idx_user_attempts ON login_attempts(user_id);
CREATE INDEX idx_file_user ON files(user_id);
CREATE INDEX idx_share_token ON file_shares(share_token);
CREATE INDEX idx_device_user ON user_devices(user_id);
CREATE INDEX idx_backup_user ON backup_codes(user_id); 