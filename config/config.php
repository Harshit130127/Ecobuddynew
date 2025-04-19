<?php
// config.php
$db_path = __DIR__ . '/../data/ecobuddy.db';
$db = new SQLite3($db_path);

// Create tables with new columns
$db->exec("
  CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    user_type TEXT NOT NULL
  )
");

$db->exec("
  CREATE TABLE IF NOT EXISTS ecofacilities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    category TEXT NOT NULL,
    description TEXT NOT NULL,
    location TEXT NOT NULL,
    latitude TEXT NOT NULL,
    longitude TEXT NOT NULL,
    photo TEXT NOT NULL,
    status_of_facility TEXT NOT NULL,
    reviews TEXT
  )
");

// Insert test accounts
$password_admin = password_hash('Admin@SecurePass123!', PASSWORD_DEFAULT);
$password_lee = password_hash('Lee@SecurePass456!', PASSWORD_DEFAULT);

$db->exec("
  INSERT OR IGNORE INTO users (username, password, user_type) 
  VALUES 
    ('admin', '$password_admin', 'manager'),
    ('lee', '$password_lee', 'user')
");

function getDB() {
    global $db;
    return $db;
}
?>
