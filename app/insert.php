<?php
require 'config.php'; // Load DB & Redis configuration

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $status = $_POST['status'];

    // Insert into Database
    $stmt = $pdo->prepare("INSERT INTO user_info (name, location, status) VALUES (?, ?, ?)");
    $stmt->execute([$name, $location, $status]);

    // Store Name (30s) and Location (60s) in Redis
    $redis->setex("user_name", 30, $name);
    $redis->setex("user_location", 60, $location);

    echo "Data saved successfully! <a href='view.php'>View Data</a>";
}
?>

