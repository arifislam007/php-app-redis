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
            name VARCHAR(100),
            location VARCHAR(100),
            status VARCHAR(20)
        )
    ");

    // Insert data if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM user_info");
    $rowCount = $stmt->fetchColumn();
    
    if ($rowCount == 0) {
        $pdo->exec("INSERT INTO user_info (name, location, status) VALUES ('Ariful Islam', 'Dhaka', 'Active')");
    }

    // Fetch "Name" from Redis or DB
    $nameKey = "user_name";
    if ($redis->exists($nameKey)) {
        $name = $redis->get($nameKey);
        $nameFromCache = true;
    } else {
        $stmt = $pdo->query("SELECT name FROM user_info LIMIT 1");
        $name = $stmt->fetchColumn();
        $redis->setex($nameKey, 30, $name);
        $nameFromCache = false;
    }

    // Fetch "Location" from Redis or DB
    $locationKey = "user_location";
    if ($redis->exists($locationKey)) {
        $location = $redis->get($locationKey);
        $locationFromCache = true;
    } else {
        $stmt = $pdo->query("SELECT location FROM user_info LIMIT 1");
        $location = $stmt->fetchColumn();
        $redis->setex($locationKey, 60, $location);
        $locationFromCache = false;
    }

    // Fetch "Status" (always from DB, no caching)
    $stmt = $pdo->query("SELECT status FROM user_info LIMIT 1");
    $status = $stmt->fetchColumn();

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<html>
<body>
    <h1>User Information</h1>
    <p><strong>Name:</strong> <?php echo $name; ?> (<?php echo $nameFromCache ? 'Cache' : 'DB'; ?>)</p>
    <p><strong>Location:</strong> <?php echo $location; ?> (<?php echo $locationFromCache ? 'Cache' : 'DB'; ?>)</p>
    <p><strong>Status:</strong> <?php echo $status; ?> (DB)</p>
</body>
</html>

