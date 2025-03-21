<?php
// Redis Configuration
$redis = new Redis();
$redis->connect('redis', 6379);

// PostgreSQL Configuration
$dsn = "pgsql:host=postgres;port=5432;dbname=mydb;";
$username = "myuser";
$password = "mypassword";

try {
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Ensure the table exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_info (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            location VARCHAR(100) NOT NULL,
            status VARCHAR(20) NOT NULL
        )
    ");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

