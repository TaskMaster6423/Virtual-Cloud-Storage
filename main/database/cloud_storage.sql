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
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Login attempts table
CREATE TABLE IF NOT EXISTS login_attempts (
    attempt_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    ip_address VARCHAR(45),
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success', 'failed', 'logout') NOT NULL,
    factor_used VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
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
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- File access logs table
CREATE TABLE IF NOT EXISTS file_access_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT,
    user_id INT NULL,
    access_type ENUM('read', 'write', 'delete', 'share', 'shared_download') NOT NULL,
    access_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (file_id) REFERENCES files(file_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
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
    FOREIGN KEY (file_id) REFERENCES files(file_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Security questions table
CREATE TABLE IF NOT EXISTS security_questions (
    question_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    question VARCHAR(255) NOT NULL,
    answer_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
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
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Backup codes table
CREATE TABLE IF NOT EXISTS backup_codes (
    code_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    code_hash VARCHAR(255) NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
); 