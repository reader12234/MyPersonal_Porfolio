-- Standard database schema for the portfolio app

CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) DEFAULT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  file_path VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS profile_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(50) NOT NULL UNIQUE,
  setting_value TEXT DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO admins (full_name, username, password)
VALUES ('russel','russ', '1');
VALUES ('test','admin', '0');

INSERT INTO profile_settings (setting_key, setting_value) VALUES
('name', NULL),
('avatar_path', NULL),
('intro_text', 'I''m a fresh graduate with a degree in Information Technology from Camarines Sur Polytechnic Colleges, and I am eager to apply the skills and knowledge I''ve gained to contribute meaningfully to a professional team.'),
('education_text', NULL),
('services_text', NULL),
('contact_text', NULL);
