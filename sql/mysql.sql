CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL
);
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(200) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_verified TINYINT(1) DEFAULT 0,
  verify_token VARCHAR(120),
  status TINYINT(1) DEFAULT 1,
  extra_data TEXT,
  created_at DATETIME NOT NULL
);
CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  config_key VARCHAR(100) UNIQUE NOT NULL,
  config_value TEXT
);
CREATE TABLE IF NOT EXISTS surveys (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  start_at DATETIME,
  end_at DATETIME,
  require_login TINYINT(1) DEFAULT 0,
  page_size INT DEFAULT 10,
  limit_mode VARCHAR(20) DEFAULT 'none',
  anonymous_allowed TINYINT(1) DEFAULT 1,
  notify_admin TINYINT(1) DEFAULT 0,
  is_public TINYINT(1) DEFAULT 1,
  created_at DATETIME NOT NULL
);
CREATE TABLE IF NOT EXISTS survey_fields (
  id INT AUTO_INCREMENT PRIMARY KEY,
  survey_id INT NOT NULL,
  field_name VARCHAR(100) NOT NULL,
  field_label VARCHAR(120) NOT NULL,
  required TINYINT(1) DEFAULT 0
);
CREATE TABLE IF NOT EXISTS questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  survey_id INT NOT NULL,
  question_text TEXT NOT NULL,
  type VARCHAR(30) NOT NULL,
  options_text TEXT,
  required TINYINT(1) DEFAULT 0,
  jump_logic TEXT,
  sort_order INT DEFAULT 0
);
CREATE TABLE IF NOT EXISTS responses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  survey_id INT NOT NULL,
  user_id INT NULL,
  ip VARCHAR(64),
  extra_info TEXT,
  created_at DATETIME NOT NULL
);
CREATE TABLE IF NOT EXISTS answers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  response_id INT NOT NULL,
  question_id INT NOT NULL,
  answer_text TEXT
);
