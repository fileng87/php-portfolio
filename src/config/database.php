<?php

$db_host = getenv('DB_HOST') ?: 'db';
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME') ?: 'portfolio';
$db_user = getenv('DB_USER') ?: 'admin';
$db_pass = getenv('DB_PASSWORD') ?: 'password';

$dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

$pdo = null;
$error_message = null;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    $error_message = "Database connection failed: " . $e->getMessage();
    // Log the error instead of dying immediately
    error_log($error_message);
    die("Database error. Please check logs or contact support.");
}

// Function to get the PDO instance (or null if connection failed)
function get_db_connection(): ?PDO
{
    global $pdo;
    return $pdo;
}

// Function to get any connection error message
function get_db_error(): ?string
{
    global $error_message;
    return $error_message;
}
