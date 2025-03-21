<?php
$redis = new Redis();
$redis->connect('redis', 6379);

// PostgreSQL Database Connection
$dsn = "pgsql:host=postgres;port=5432;dbname=mydb;";
$username = "myuser";
$password = "mypassword";

try {
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Ensure table exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_info (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            location VARCHAR(100) NOT NULL,
            status VARCHAR(20) NOT NULL
        )
    ");

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $location = $_POST['location'];
        $status = $_POST['status'];

        // Insert data into DB
        $stmt = $pdo->prepare("INSERT INTO user_info (name, location, status) VALUES (?, ?, ?)");
        $stmt->execute([$name, $location, $status]);

        // Update Redis cache
        $redis->setex("user_name", 30, $name);
        $redis->setex("user_location", 60, $location);

        echo "Data saved successfully! <a href='index.php'>View Data</a>";
        exit;
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<html>
<body>
    <h1>Enter User Information</h1>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required><br><br>

        <label>Location:</label>
        <input type="text" name="location" required><br><br>

        <label>Status:</label>
        <input type="text" name="status" required><br><br>

        <button type="submit">Submit</button>
    </form>

    <br>
    <a href="index.php">View Stored Data</a>
</body>
</html>

