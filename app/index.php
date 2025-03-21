<?php
$redis = new Redis();
$redis->connect('redis', 6379);

// PostgreSQL Database Connection
$dsn = "pgsql:host=postgres;port=5432;dbname=mydb;";
$username = "myuser";
$password = "mypassword";

try {
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Fetch "Name" from Redis or DB
    $nameKey = "user_name";
    if ($redis->exists($nameKey)) {
        $name = $redis->get($nameKey);
        $nameFromCache = true;
    } else {
        $stmt = $pdo->query("SELECT name FROM user_info ORDER BY id DESC LIMIT 1");
        $name = $stmt->fetchColumn();
        if ($name) $redis->setex($nameKey, 30, $name);
        $nameFromCache = false;
    }

    // Fetch "Location" from Redis or DB
    $locationKey = "user_location";
    if ($redis->exists($locationKey)) {
        $location = $redis->get($locationKey);
        $locationFromCache = true;
    } else {
        $stmt = $pdo->query("SELECT location FROM user_info ORDER BY id DESC LIMIT 1");
        $location = $stmt->fetchColumn();
        if ($location) $redis->setex($locationKey, 60, $location);
        $locationFromCache = false;
    }

    // Fetch "Status" (always from DB)
    $stmt = $pdo->query("SELECT status FROM user_info ORDER BY id DESC LIMIT 1");
    $status = $stmt->fetchColumn();

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<html>
<body>
    <h1>Latest User Information</h1>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?> (<?php echo $nameFromCache ? 'Cache' : 'DB'; ?>)</p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($location); ?> (<?php echo $locationFromCache ? 'Cache' : 'DB'; ?>)</p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?> (DB)</p>

    <br>
    <a href="form.php">Enter New Data</a>
</body>
</html>

